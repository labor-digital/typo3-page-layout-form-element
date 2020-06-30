<?php
declare(strict_types=1);
/**
 * Copyright 2019 LABOR.digital
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
 * Last modified: 2019.07.14 at 20:07
 */

namespace LaborDigital\Typo3PageLayoutFormElement\Domain\Model;


use LaborDigital\Typo3BetterApi\Page\PageService;
use LaborDigital\Typo3BetterApi\Tsfe\TsfeService;

class PageLayoutContent
{
    
    /**
     * @var PageService
     */
    protected $pageService;
    
    /**
     * @var TsfeService
     */
    protected $tsfeService;
    
    /**
     * @var int
     */
    protected $languageUid;
    
    /**
     * @var string|null
     */
    protected $pageUid;
    
    /**
     * PageLayoutContent constructor.
     *
     * @param   int     $languageUid
     * @param   string  $pageUid
     */
    public function __construct(int $languageUid, ?string $pageUid)
    {
        $this->languageUid = $languageUid;
        $this->pageUid     = $pageUid;
    }
    
    /**
     * Injects the page service
     *
     * @param   \LaborDigital\Typo3BetterApi\Page\PageService  $pageService
     */
    public function injectPageService(PageService $pageService): void
    {
        $this->pageService = $pageService;
    }
    
    /**
     * Injects the tsfe service
     *
     * @param   \LaborDigital\Typo3BetterApi\Tsfe\TsfeService  $tsfeService
     */
    public function injectTsfeService(TsfeService $tsfeService): void
    {
        $this->tsfeService = $tsfeService;
    }
    
    /**
     * Returns the uid if the page that stores the content elements
     *
     * @return int
     */
    public function getPageUid(): int
    {
        return (int)$this->pageUid;
    }
    
    /**
     * Returns the string of the linked content elements as html
     *
     * @return string
     * @throws \Throwable
     */
    public function renderContents(): string
    {
        if (empty($this->pageUid)) {
            return "";
        }
        
        // Render the contents
        return $this->pageService->renderPageContents(
            (int)$this->pageUid,
            [
                "language" => $this->languageUid,
                'includeHiddenPages',
            ]
        );
    }
    
    /**
     * Can be used to return the list of all content elements of a given page.
     * The contents will be sorted into their matching layout columns in order of their "sorting".
     *
     * This method will make an educated guess on your content elements and if you are running a modular griding
     * extension like gridelements. If you do, the elements will be hierarchically sorted by their parents.
     *
     * @return array
     */
    public function getContents(): array
    {
        if (empty($this->pageUid)) {
            return [];
        }
        
        return $this->pageService->getPageContents(
            (int)$this->pageUid,
            [
                "language" => $this->languageUid,
                'includeHiddenPages',
            ]);
    }
}
