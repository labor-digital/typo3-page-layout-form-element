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
 * Last modified: 2019.07.09 at 21:15
 */

namespace LaborDigital\Typo3PageLayoutFormElement\ExtConfig;

use LaborDigital\Typo3BetterApi\CoreModding\CodeGeneration\ClassOverrideGenerator;
use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigContext;
use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigInterface;
use LaborDigital\Typo3BetterApi\ExtConfig\Extension\ExtConfigExtensionInterface;
use LaborDigital\Typo3BetterApi\ExtConfig\Extension\ExtConfigExtensionRegistry;
use LaborDigital\Typo3BetterApi\ExtConfig\OptionList\ExtConfigOptionList;
use LaborDigital\Typo3PageLayoutFormElement\BackendForm\FormPreset;
use LaborDigital\Typo3PageLayoutFormElement\Controller\Resource\FormPageLayoutContentController;
use LaborDigital\Typo3PageLayoutFormElement\CoreModding\ExtendedAbstractTreeView;
use LaborDigital\Typo3PageLayoutFormElement\CoreModding\ExtendedTreeController;
use LaborDigital\Typo3PageLayoutFormElement\Domain\Table\Override\PagesOverride;
use LaborDigital\Typo3PageLayoutFormElement\Event\BackendEventHandler;
use LaborDigital\Typo3PageLayoutFormElement\Middleware\PageLayoutFilterMiddleware;
use TYPO3\CMS\Backend\Controller\Page\TreeController;
use TYPO3\CMS\Backend\Tree\View\AbstractTreeView;

class PageLayoutFormElementExtConfig implements ExtConfigInterface, ExtConfigExtensionInterface
{
    
    /**
     * @inheritDoc
     */
    public function configure(ExtConfigOptionList $configurator, ExtConfigContext $context): void
    {
        // Register our backend event handlers
        if ($context->TypoContext->Env()->isBackend()) {
            $configurator->event()->registerLazySubscriber(BackendEventHandler::class);
        }
        
        // Register middleware
        $configurator->http()->registerMiddleware(PageLayoutFilterMiddleware::class, 'backend');
        
        // Register a class override to hide our pages in the backend
        ClassOverrideGenerator::registerOverride(AbstractTreeView::class, ExtendedAbstractTreeView::class, true);
        ClassOverrideGenerator::registerOverride(TreeController::class, ExtendedTreeController::class, true);
        
        // Register table modification
        $configurator->table()
                     ->registerTableOverride(PagesOverride::class, "pages")
                     ->registerTableOverride(PagesOverride::class, "pages_language_overlay");
        
        // Register translations
        $configurator->translation()
                     ->registerContext("plfe", "EXT:{{extkey}}/Resources/Private/Language/locallang.xlf");
        
        // Register our backend layout
        $configurator->typoScript()
                     ->registerPageTsConfigFile("EXT:{{extkey}}/Configuration/TypoScript/PageTs/BackendLayout.typoscript");
        
        // Add resource information for frontend api
        if ($configurator->hasOption("frontendApi")) {
            $configurator->frontendApi()->resource()->registerResource(FormPageLayoutContentController::class);
        }
    }
    
    /**
     * @inheritDoc
     */
    public static function extendExtConfig(ExtConfigExtensionRegistry $extender, ExtConfigContext $context): void
    {
        $extender->registerFieldPreset(FormPreset::class);
    }
    
}
