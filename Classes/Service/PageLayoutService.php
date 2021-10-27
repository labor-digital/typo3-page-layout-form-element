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
 * Last modified: 2021.07.27 at 09:53
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Service;


use LaborDigital\T3ba\Core\Di\PublicServiceInterface;
use LaborDigital\T3ba\Tool\DataHandler\DataHandlerService;
use LaborDigital\T3ba\Tool\Link\LinkService;
use LaborDigital\T3plfe\Domain\Repository\PageLayoutRepository;
use LaborDigital\T3plfe\Domain\Repository\ParentRepository;
use LaborDigital\T3plfe\Middleware\PageLayoutFilterMiddleware;
use LaborDigital\T3plfe\Util\FieldNamingUtil;

class PageLayoutService implements PublicServiceInterface
{
    /**
     * @var \LaborDigital\T3plfe\Domain\Repository\ParentRepository
     */
    protected $parentRepository;
    
    /**
     * @var \LaborDigital\T3plfe\Domain\Repository\PageLayoutRepository
     */
    protected $pageRepository;
    
    /**
     * @var \LaborDigital\T3plfe\Service\RenderingService
     */
    protected $renderingService;
    
    /**
     * @var \LaborDigital\T3ba\Tool\DataHandler\DataHandlerService
     */
    protected $dataHandlerService;
    
    /**
     * @var \LaborDigital\T3ba\Tool\Link\LinkService
     */
    protected $linkService;
    
    public function __construct(
        ParentRepository $parentRepository,
        PageLayoutRepository $pageRepository,
        RenderingService $renderingService,
        DataHandlerService $dataHandlerService,
        LinkService $linkService
    )
    {
        $this->parentRepository = $parentRepository;
        $this->pageRepository = $pageRepository;
        $this->renderingService = $renderingService;
        $this->dataHandlerService = $dataHandlerService;
        $this->linkService = $linkService;
    }
    
    /**
     * Returns the content page repository
     *
     * @return \LaborDigital\T3plfe\Domain\Repository\PageLayoutRepository
     */
    public function getPageRepository(): PageLayoutRepository
    {
        return $this->pageRepository;
    }
    
    /**
     * Action handler to create a content page and, if necessary all translations of a page as well.
     *
     * @param   string    $renderName  The absolute render name of the field inside of $tableName that is the page layout field
     * @param   int       $storagePid  The page uid where to store the new page in
     * @param   string    $title       The title to provide for the newly generated page
     * @param   int|null  $language    Optional language uid that requests the creation of a page
     * @param   array     $options     The options provided to the page layout field {@see Preset::applyPageLayout()}
     * @param   int|null  $recordUid   Optional record uid, if omitted the uid is resolved through the $renderName value
     *
     * @return int
     */
    public function createPage(
        string $renderName,
        int $storagePid,
        string $title,
        ?int $language = null,
        array $options = [],
        ?int $recordUid = null
    ): int
    {
        $tableName = FieldNamingUtil::getTableNameFromRenderName($renderName);
        $recordUid = $recordUid ?? FieldNamingUtil::getUidFromRenderName($renderName);
        
        // If a page is created on a translation, we need to create the page on the base record and all other translations
        // I need to do this here, because my brain exploded when I tried to create pages on demand in flex form contexts
        if ($language !== null && $language > 0) {
            $baseRecordUid = $this->parentRepository->findBaseRecordUid($tableName, $recordUid);
            if ($baseRecordUid) {
                $baseRenderName = FieldNamingUtil::updateUidInRenderName($renderName, $recordUid);
                
                // Create the page on the parent record -> We don't need to update the row through the data handler here
                // because it is already done by the recursive call.
                return $this->createPage($baseRenderName, $storagePid, $title, null, $options, $baseRecordUid);
            }
        }
        
        $contentPid = $this->pageRepository->createPage($storagePid, $renderName, $title, $options);
        // Iterate all languages in the parent table and link our page to the fields
        $this->parentRepository->runForAllLanguages(
            $tableName,
            $baseRecordUid ?? $recordUid,
            function (array $row, array $constraints) use ($renderName, $contentPid, $options) {
                $renderName = FieldNamingUtil::updateUidInRenderName($renderName, $row['uid']);
                $data = FieldNamingUtil::parseRenderName($renderName, $contentPid);
                if (! empty($data)) {
                    $this->dataHandlerService->processData($data, [], ! ($options['respectUserPermissions'] ?? false));
                }
                
                // Create a translation of the page if required
                [, $languageField] = $constraints;
                if ($languageField && ! empty($row[$languageField]) && (int)$row[$languageField] > 0) {
                    $transContentPid = $this->pageRepository->translatePage($contentPid, (int)$row[$languageField]);
                    $data = FieldNamingUtil::parseRenderName($renderName, $transContentPid);
                    if (! empty($data)) {
                        $this->dataHandlerService->processData($data, [], ! ($options['respectUserPermissions'] ?? false));
                    }
                }
            }
        );
        
        return $contentPid;
    }
    
    /**
     * Action handler to delete a content page and update the record accordingly
     *
     * @param   string  $renderName  The absolute render name of the field inside of $tableName that is the page layout field
     * @param   int     $contentPid  The uid of the content page to delete
     * @param   array   $options     The options provided to the page layout field {@see Preset::applyPageLayout()}
     */
    public function deletePage(
        string $renderName,
        int $contentPid,
        array $options = []
    ): void
    {
        $tableName = FieldNamingUtil::getTableNameFromRenderName($renderName);
        $recordUid = FieldNamingUtil::getUidFromRenderName($renderName);
        
        $baseRecordUid = $this->parentRepository->findBaseRecordUid($tableName, $recordUid);
        $recordUid = $baseRecordUid ?? $recordUid;
        
        $this->pageRepository->deletePage($contentPid, $options);
        
        // Iterate all languages in the parent table and remove our page from the fields
        $this->parentRepository->runForAllLanguages($tableName, $recordUid, function (array $row) use ($renderName, $options) {
            $renderName = FieldNamingUtil::updateUidInRenderName($renderName, $row['uid']);
            $data = FieldNamingUtil::parseRenderName($renderName, 0);
            if (! empty($data)) {
                $this->dataHandlerService->processData($data, [], ! ($options['respectUserPermissions'] ?? false));
            }
        });
    }
    
    /**
     * Helper to render the empty ui html code with all links automatically generated
     *
     * @param   int       $storagePid
     * @param   string    $renderName
     * @param   string    $newPageTitle
     * @param   int|null  $language
     *
     * @return string
     */
    public function renderEmptyUi(
        int $storagePid,
        string $renderName,
        string $newPageTitle,
        ?int $language = null
    ): string
    {
        $createLink = $this->buildAjaxCreateLink($storagePid, $language, $renderName, $newPageTitle);
        
        return $this->renderingService->renderEmptyUi($createLink);
    }
    
    /**
     * Renders ONLY the action buttons, to show when the iframe was disabled via the options
     *
     * @param   string    $renderName
     * @param   int       $contentPid
     * @param   int|null  $language
     *
     * @return string
     */
    public function renderActionButtons(
        string $renderName,
        int $contentPid,
        ?int $language = null
    ): string
    {
        $contentPid = $this->parentRepository->findBaseRecordUid('pages', $contentPid) ?? $contentPid;
        $renderId = FieldNamingUtil::getFieldIdFromRenderName($renderName);
        $fullscreenLink = $this->buildEditLink($contentPid, $language);
        $deleteLink = $this->buildAjaxDeleteLink($renderName, $contentPid);
        
        return $this->renderingService->renderActionButtons($fullscreenLink, $deleteLink, $renderId, $renderName, false);
        
    }
    
    /**
     * Helper to render the iframe html code with all links automatically generated
     *
     * @param   string    $renderName
     * @param   int       $contentPid
     * @param   int|null  $language
     *
     * @return string
     */
    public function renderIframe(
        string $renderName,
        int $contentPid,
        ?int $language = null
    ): string
    {
        $contentPid = $this->parentRepository->findBaseRecordUid('pages', $contentPid) ?? $contentPid;
        $renderId = FieldNamingUtil::getFieldIdFromRenderName($renderName);
        $frameId = $this->renderingService->makeIframeId($renderId);
        $iframeLink = $this->buildEditLink($contentPid, $language, $frameId);
        $fullscreenLink = $this->buildEditLink($contentPid, $language);
        $deleteLink = $this->buildAjaxDeleteLink($renderName, $contentPid);
        
        return $this->renderingService->renderIframe($iframeLink, $fullscreenLink, $deleteLink, $renderId, $renderName);
    }
    
    /**
     * Builds the link to the edit view / web page layout
     *
     * @param   int          $pid
     * @param   int|null     $language
     * @param   string|null  $iframeId
     *
     * @return string
     */
    public function buildEditLink(int $pid, ?int $language, ?string $iframeId = null): string
    {
        return $this->linkService->getBackendLink('web_layout', [
            'args' => array_merge(
                [
                    'id' => $pid,
                    PageLayoutFilterMiddleware::PAGE_LAYOUT_REQUEST_MARKER => 1,
                ],
                ['SET' => ['language' => (int)$language]],
                ($iframeId ? [PageLayoutFilterMiddleware::PAGE_LAYOUT_IFRAME_MARKER => $iframeId] : [])
            ),
        ]);
    }
    
    /**
     * Builds the ajax url to create a new content page
     *
     * @param   int       $storagePid
     * @param   int|null  $language
     * @param   string    $renderName
     * @param   string    $newPageTitle
     *
     * @return string
     */
    public function buildAjaxCreateLink(
        int $storagePid,
        ?int $language,
        string $renderName,
        string $newPageTitle
    ): string
    {
        return $this->linkService->getBackendLink('ajax_plfe_create_page', [
            'args' => [
                'pid' => $storagePid,
                'field' => $renderName,
                'title' => $newPageTitle,
                'lang' => $language,
            ],
        ]);
    }
    
    /**
     * Builds the ajax url to remove a content page with the provided uid
     *
     * @param   string  $renderName
     * @param   int     $contentPid
     *
     * @return string
     */
    public function buildAjaxDeleteLink(string $renderName, int $contentPid): string
    {
        return $this->linkService->getBackendLink('ajax_plfe_delete_page', [
            'args' => [
                'field' => $renderName,
                'pid' => $contentPid,
            ],
        ]);
    }
}