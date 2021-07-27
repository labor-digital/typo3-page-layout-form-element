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

namespace LaborDigital\T3plfe\Domain\Model;


use LaborDigital\T3ba\Tool\Page\PageService;
use LaborDigital\T3ba\Tool\TypoContext\TypoContextAwareTrait;
use Throwable;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class PageLayout extends AbstractEntity
{
    use TypoContextAwareTrait;
    
    /**
     * @var int
     */
    protected $uid;

//    /**
//     * @var \LaborDigital\T3ba\Tool\Page\PageService
//     */
//    protected $pageService;
//
//    /**
//     * The uid of the content page that represents the conent
//     *
//     * @var int|null
//     */
//    protected $contentUid;
//
//    /**
//     * The site that is linked to the page content
//     *
//     * @var \TYPO3\CMS\Core\Site\Entity\SiteInterface
//     */
//    protected $site;
//
//    /**
//     * The language for which the content was resolved
//     *
//     * @var \TYPO3\CMS\Core\Site\Entity\SiteLanguage
//     */
//    protected $language;
//
//    public function __construct(?int $contentUid, SiteInterface $site, SiteLanguage $language, PageService $pageService)
//    {
//        $this->pageService = $pageService;
//        $this->contentUid = $contentUid;
//        $this->site = $site;
//        $this->language = $language;
//    }
    
    /**
     * Returns the site that is linked to the page content
     *
     * @return \TYPO3\CMS\Core\Site\Entity\SiteInterface
     */
    public function getSite(): SiteInterface
    {
        try {
            return $this->getTypoContext()->site()->getForPid($this->getPid());
        } catch (Throwable $e) {
            return $this->getTypoContext()->site()->getCurrent();
        }
    }
    
    /**
     * Returns the language for which the content was resolved
     *
     * @return \TYPO3\CMS\Core\Site\Entity\SiteLanguage
     */
    public function getLanguage(): SiteLanguage
    {
        $lang = $this->getTypoContext()->language();
        
        if ($this->_languageUid !== null) {
            return $lang->getLanguageById($this->_languageUid, $this->getSite()->getIdentifier());
        }
        
        return $lang->getCurrentFrontendLanguage($this->getSite()->getIdentifier());
    }
    
    /**
     * Returns the string of the linked content elements as html
     *
     * @return string
     * @throws \Throwable
     */
    public function render(): string
    {
        if (empty($this->contentUid)) {
            return '';
        }
        
        return $this->getTypoContext()->di()->getService(PageService::class)->renderPageContents(
            (int)$this->contentUid,
            [
                'site' => $this->getSite()->getIdentifier(),
                'language' => $this->getLanguage(),
                'includeHiddenPages',
            ]
        );
    }
    
    /**
     * Can be used to return the list of all content elements of a given page.
     * The contents will be sorted into their matching layout columns in order of their "sorting".
     *
     * This method will make an educated guess on your content elements and if you are running a modular griding
     * extension like grid elements. If you do, the elements will be hierarchically sorted by their parents.
     *
     * @return array
     */
    public function getElements(): array
    {
        if (empty($this->contentUid)) {
            return [];
        }
        
        return $this->getTypoContext()->di()->getService(PageService::class)->getPageContents(
            (int)$this->contentUid,
            [
                'site' => $this->getSite()->getIdentifier(),
                'language' => $this->getLanguage(),
                'includeHiddenPages',
            ]
        );
    }
}
