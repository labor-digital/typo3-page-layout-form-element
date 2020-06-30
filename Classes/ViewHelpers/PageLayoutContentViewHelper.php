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
 * Last modified: 2019.07.15 at 00:05
 */

namespace LaborDigital\Typo3PageLayoutFormElement\ViewHelpers;


use LaborDigital\Typo3BetterApi\Container\TypoContainerInterface;
use LaborDigital\Typo3BetterApi\TypoContext\TypoContext;
use LaborDigital\Typo3PageLayoutFormElement\Domain\Model\PageLayoutContent;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class PageLayoutContentViewHelper extends AbstractViewHelper
{
    protected $escapeOutput   = false;
    protected $escapeChildren = false;
    
    /**
     * @var \LaborDigital\Typo3BetterApi\Container\TypoContainerInterface
     */
    protected $container;
    
    /**
     * @var \LaborDigital\Typo3BetterApi\TypoContext\TypoContext
     */
    protected $context;
    
    public function __construct(TypoContainerInterface $container, TypoContext $context)
    {
        $this->container = $container;
        $this->context   = $context;
    }
    
    /**
     * @inheritDoc
     */
    public function initializeArguments()
    {
        $this->registerArgument("field", "mixed", "The content of the page layout form element to render", true);
    }
    
    /**
     * Render the contents of the linked page layout and be done
     *
     * @return string
     */
    public function render()
    {
        // Make sure we have the content element to work with
        $field = $this->arguments["field"];
        if (! $field instanceof PageLayoutContent) {
            $field = $this->container->get(PageLayoutContent::class, [
                "args" => [
                    $this->context->Language()->getCurrentFrontendLanguage()->getLanguageId(),
                    $field,
                ],
            ]);
        }
        
        // Render the content
        return $field->renderContents();
    }
    
}
