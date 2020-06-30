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
 * Last modified: 2019.08.16 at 16:28
 */

namespace LaborDigital\Typo3PageLayoutFormElement\CoreModding;


use LaborDigital\Typo3BetterApi\Container\ContainerAwareTrait;
use LaborDigital\Typo3BetterApi\Domain\DbService\DbService;
use TYPO3\CMS\Backend\Tree\View\BetterApiClassOverrideCopy__AbstractTreeView;

class ExtendedAbstractTreeView extends BetterApiClassOverrideCopy__AbstractTreeView
{
    use ContainerAwareTrait;
    
    /**
     * @inheritDoc
     */
    public function init($clause = '', $orderByFields = '')
    {
        parent::init($clause, $orderByFields);
        
        // Update the clause
        $connection   = $this->getInstanceOf(DbService::class)->getConnection();
        $this->clause .= ' AND ' . $connection->quoteIdentifier('form_element_parent') . ' IS NULL';
        
        // Emit event to the world
//        $this->getInstanceOf(TypoEventBus::class)->emit("pageLayoutFormElement__filterTreeView", [
//            "args" => [
//                "class"    => get_called_class(),
//                "instance" => $this,
//            ],
//        ]);
    }
    
}
