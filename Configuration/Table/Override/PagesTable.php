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


namespace LaborDigital\T3plfe\Configuration\Table\Override;


use LaborDigital\T3ba\ExtConfig\ExtConfigContext;
use LaborDigital\T3ba\ExtConfigHandler\Table\ConfigureTcaTableInterface;
use LaborDigital\T3ba\ExtConfigHandler\Table\TcaTableNameProviderInterface;
use LaborDigital\T3ba\Tool\Tca\Builder\Type\Table\TcaTable;
use LaborDigital\T3plfe\Domain\Model\PageLayout;

class PagesTable implements ConfigureTcaTableInterface, TcaTableNameProviderInterface
{
    public const PAGE_LAYOUT_DOK_TYPE = 8348;
    public const PAGE_LAYOUT_FIELD = 't3plfe_parent';
    
    /**
     * @inheritDoc
     */
    public static function getTableName(): string
    {
        return 'pages';
    }
    
    /**
     * @inheritDoc
     */
    public static function configureTable(TcaTable $table, ExtConfigContext $context): void
    {
        $table->registerModelClass(PageLayout::class);
        
        $type = $table->getType(static::PAGE_LAYOUT_DOK_TYPE);
        $type->clear();
        
        $type->getTab(0)->addMultiple(static function () use ($type) {
            $type->getField('title');
            
            $parentField = $type->getField(static::PAGE_LAYOUT_FIELD);
            $parentField->applyPreset()->relationGroup('*', ['maxItems' => 1, 'required', 'mmTable' => false]);
        });
    }
    
}