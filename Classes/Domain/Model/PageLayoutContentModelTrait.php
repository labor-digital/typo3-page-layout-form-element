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
 * Last modified: 2019.07.14 at 20:05
 */

namespace LaborDigital\Typo3PageLayoutFormElement\Domain\Model;


use LaborDigital\Typo3BetterApi\Container\TypoContainer;
use LaborDigital\Typo3BetterApi\TypoContext\TypoContext;
use LaborDigital\Typo3PageLayoutFormElement\PageLayoutFormElementException;

trait PageLayoutContentModelTrait
{
    /**
     * The list of the instantiated layout contents
     *
     * @var \LaborDigital\Typo3PageLayoutFormElement\Domain\Model\PageLayoutContent[]
     */
    protected $__pageLayoutContents = [];
    
    /**
     * Sadly it is not easily possible to inject a instance of the page layout content object into
     * an extbase model. (E.g. Translation issues due to the fact we are handling pages, hidden elements are not
     * shown...)
     *
     * So we need this helper in your model.
     * 1. Add this trait to your model.
     * 2. Add a property for your pageLayout field and define it as param string $field
     * 3. Add a getter like getPageLayoutContent() and use this method in it,
     * with the name of the field you created in step 2.
     *
     * @param   string  $field  The name of the field that is configured as a page layout type
     *
     * @return \LaborDigital\Typo3PageLayoutFormElement\Domain\Model\PageLayoutContent
     * @throws \LaborDigital\Typo3PageLayoutFormElement\PageLayoutFormElementException
     */
    public function getPageLayoutContentObject(string $field): PageLayoutContent
    {
        // Check if we already have the instance for this element
        if (isset($this->__pageLayoutContents[$field])) {
            return $this->__pageLayoutContents[$field];
        }
        
        // Check if the field exists
        if (! property_exists($this, $field)) {
            throw new PageLayoutFormElementException("Could not load the page content object for field: $field, because the property does not exist!");
        }
        
        
        // Make sure we have the current language id in the list
        $language = $this->_languageUid;
        if ($this->_languageUid === -1) {
            $language = TypoContainer::getInstance()
                                     ->get(TypoContext::class)
                                     ->Language()
                                     ->getCurrentFrontendLanguage()
                                     ->getLanguageId();
        }
        
        // Create a new instance
        return $this->__pageLayoutContents[$field]
            = TypoContainer::getInstance()
                           ->get(
                               PageLayoutContent::class,
                               [
                                   "args" => [$language, "" . $this->$field],
                               ]);
    }
}
