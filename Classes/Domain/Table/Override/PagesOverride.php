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
 * Last modified: 2019.09.06 at 16:02
 */

namespace LaborDigital\Typo3PageLayoutFormElement\Domain\Table\Override;


use LaborDigital\Typo3BetterApi\BackendForms\TcaForms\TcaTable;
use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigContext;
use LaborDigital\Typo3BetterApi\ExtConfig\Option\Table\TableConfigurationInterface;

class PagesOverride implements TableConfigurationInterface
{
    public const PAGE_LAYOUT_DOK_TYPE = 8348;
    
    /**
     * @inheritDoc
     */
    public static function configureTable(TcaTable $table, ExtConfigContext $context, bool $isOverride): void
    {
        // Add our marker field to the tca
        $table->getField("form_element_parent")->applyPreset()->passThrough();
        
        // Add our type to the dok types
        $dokTypeField = $table->getField("doktype");
        $dokType      = $dokTypeField->getRaw();
        $dokTypeField->setRaw($dokType);
        
        // Create our page type
        $type = $table->getType(static::PAGE_LAYOUT_DOK_TYPE);
        $type->removeAllElements();
        $type->getPalette("titleonly");
        $type->getField("form_element_parent");
    }
}
