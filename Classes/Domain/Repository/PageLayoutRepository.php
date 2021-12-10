<?php
/*
 * Copyright 2021 LABOR.digital
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Last modified: 2021.07.27 at 09:25
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Domain\Repository;


use LaborDigital\T3ba\ExtBase\Domain\Repository\BetterRepository;
use LaborDigital\T3ba\Tool\Database\DbService;
use LaborDigital\T3ba\Tool\DataHandler\DataHandlerException;
use LaborDigital\T3ba\Tool\DataHandler\DataHandlerService;
use LaborDigital\T3ba\Tool\DataHandler\Record\RecordDataHandler;
use LaborDigital\T3ba\Tool\Page\PageService;
use LaborDigital\T3plfe\Configuration\Table\Override\PagesTable;
use LaborDigital\T3plfe\Event\PageLayoutPageRowFilterEvent;
use LaborDigital\T3plfe\Util\FieldNamingUtil;
use Psr\EventDispatcher\EventDispatcherInterface;

class PageLayoutRepository extends BetterRepository
{
    
    /**
     * @var \LaborDigital\T3ba\Tool\Page\PageService
     */
    protected $pageService;
    
    /**
     * @var \LaborDigital\T3ba\Tool\DataHandler\DataHandlerService
     */
    protected $dataHandlerService;
    
    /**
     * @var \Psr\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;
    
    /**
     * @var \LaborDigital\T3ba\Tool\Database\DbService
     */
    protected $db;
    
    public function injectPageService(PageService $pageService): void
    {
        $this->pageService = $pageService;
    }
    
    public function injectDataHandlerService(DataHandlerService $dataHandlerService): void
    {
        $this->dataHandlerService = $dataHandlerService;
    }
    
    public function injectEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }
    
    public function injectDbService(DbService $db): void
    {
        $this->db = $db;
    }
    
    /**
     * Checks if the given row has a content page
     *
     * @param   int|null  $pid  The pid to check for
     *
     * @return bool
     */
    public function hasPage(?int $pid): bool
    {
        if ($pid === null || $pid === 0) {
            return false;
        }
        
        return $this->pageService->pageExists($pid, true);
    }
    
    /**
     * Creates a new content page and returns the new uid
     *
     * @param   int     $storagePid  The pid that should contain the new content page
     * @param   string  $renderName  The absolute render name of the field inside of $tableName that is the page layout field
     * @param   string  $title       The generated title of the page to generate
     * @param   array   $options     The options provided to the page layout field {@see Preset::applyPageLayout()}
     *
     * @return int The uid of the newly created page
     */
    public function createPage(int $storagePid, string $renderName, string $title, array $options = []): int
    {
        $tableName = FieldNamingUtil::getTableNameFromRenderName($renderName);
        $recordUid = FieldNamingUtil::getUidFromRenderName($renderName);
        
        $storageData = $this->pageService->getPageInfo($storagePid, true);
        if (! is_array($storageData)) {
            throw new \InvalidArgumentException('There is no page that has the uid you provided: ' . $storagePid);
        }
        
        $inheritedData = [];
        foreach (['perms_userid', 'perms_groupid', 'perms_user', 'perms_group', 'perms_everybody'] as $field) {
            $inheritedData[$field] = $storageData[$field] ?? 0;
        }
        
        $newPageRow = array_merge(
            $options['addToPageRow'] ?? [],
            $inheritedData,
            [
                PagesTable::PAGE_LAYOUT_FIELD => $tableName . '_' . $recordUid,
                'doktype' => PagesTable::PAGE_LAYOUT_DOK_TYPE,
                'nav_hide' => 1,
                'backend_layout' => 'pagets__page_layout_form_element_content',
            ]
        );
        
        $e = $this->eventDispatcher->dispatch(new PageLayoutPageRowFilterEvent(
            $newPageRow, $title, $tableName, $renderName, $recordUid, $storagePid, $options
        ));
        
        return $this->pageService->createNewPage($storagePid, [
            'force' => $this->getUseForce($options),
            'title' => $e->getTitle(),
            'pageRow' => $e->getRow(),
        ]);
    }
    
    /**
     * Deletes the page with the given uid
     *
     * @param   int    $pid      The uid of the content page that should be removed
     * @param   array  $options  The options provided to the page layout field {@see Preset::applyPageLayout()}
     */
    public function deletePage(int $pid, array $options = []): void
    {
        $this->pageService->deletePage($pid, $this->getUseForce($options));
    }
    
    /**
     * Restores a content page from being deleted
     *
     * @param   int    $pid      The uid of the content page that should be restored
     * @param   array  $options  The options provided to the page layout field {@see Preset::applyPageLayout()}
     */
    public function restorePage(int $pid, array $options = []): void
    {
        $this->pageService->restorePage($pid, $this->getUseForce($options));
    }
    
    /**
     * Handles the translation of a content page to another language
     *
     * @param   int|null  $pid       The content page we should translate (The BASE uid to translate from)
     * @param   int       $language  The language id the new page should be created for
     * @param   array     $options   The options provided to the page layout field {@see Preset::applyPageLayout()}
     */
    public function translatePage(int $pid, int $language, array $options = []): int
    {
        $command = $options['translationMode'] ?? 'copyToLanguage';
        
        $commands = [
            'pages' => [
                $pid => [$command => $language],
            ],
        ];
        
        $contents = $this->pageService->getPageContents($pid, [
            'includeHiddenPages',
            'includeHiddenContent',
            'returnRaw',
            'force' => $this->getUseForce($options) !== false,
        ]);
        
        foreach ($contents as $row) {
            $commands['tt_content'][$row['uid']][$command] = $language;
        }
        
        try {
            $dh = $this->dataHandlerService->processCommands($commands, [], $this->getUseForce($options));
        } catch (DataHandlerException $e) {
            $dh = $e->getHandler();
        }
        
        return $dh->copyMappingArray['pages'][$pid] ?? $pid;
    }
    
    /**
     * Copies a content page (either to a new page, or duplicate it on the same page)
     *
     * @param   int|null  $pid         The content page that should be copied
     * @param   int       $storagePid  The page the new content page should be created at
     * @param   array     $options     The options provided to the page layout field {@see Preset::applyPageLayout()}
     *
     * @return int|null Returns the id of the copied page, or null if the copy process failed
     */
    public function copyPage(int $pid, int $storagePid, array $options = []): int
    {
        return $this->pageService->copyPage($pid, [
            'targetPid' => $storagePid,
            'force' => $this->getUseForce($options),
        ]);
    }
    
    /**
     * Moves a content page to a new storage location
     *
     * @param   int    $pid         The content page that should be moved
     * @param   int    $storagePid  The page the content page should be moved to
     * @param   array  $options     The options provided to the page layout field {@see Preset::applyPageLayout()}
     */
    public function movePage(int $pid, int $storagePid, array $options = []): void
    {
        $this->pageService->movePage($pid, $storagePid, $this->getUseForce($options));
    }
    
    /**
     * Returns the data handler instance for the pages table
     *
     * @return \LaborDigital\T3ba\Tool\DataHandler\Record\RecordDataHandler
     */
    protected function getDataHandler(): RecordDataHandler
    {
        return $this->dataHandlerService->getRecordDataHandler('pages');
    }
    
    /**
     * Returns true if the force option should be used, false if not
     *
     * @param   array  $options
     *
     * @return bool|string
     */
    protected function getUseForce(array $options)
    {
        return ($options['respectUserPermissions'] ?? null) ? false : 'soft';
    }
}