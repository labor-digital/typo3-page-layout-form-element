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
 * Last modified: 2021.07.26 at 13:03
 */

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

namespace LaborDigital\T3plfe\Override;


use LaborDigital\T3ba\Core\Di\ContainerAwareTrait;
use LaborDigital\T3ba\Core\Di\NoDiInterface;
use TYPO3\CMS\Backend\Tree\View\T3BaCopyAbstractTreeView;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DocumentTypeExclusionRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ExtendedAbstractTreeView
 *
 * Extends the abstract tree view, so it can apply the excludeDoktypes tsconfig options correctly
 *
 * @package LaborDigital\T3plfe\Override
 */
class ExtendedAbstractTreeView extends T3BaCopyAbstractTreeView implements NoDiInterface
{
    use ContainerAwareTrait;
    
    /**
     * @inheritDoc
     */
    public function init($clause = '', $orderByFields = '')
    {
        parent::init($clause, $orderByFields);
        
        if ($this->table !== 'pages') {
            return;
        }
        
        $userTsConfig = $this->getBackendUser()->getTSConfig();
        $excludedDocumentTypes = GeneralUtility::intExplode(',', $userTsConfig['options.']['pageTree.']['excludeDoktypes'] ?? '', true);
        
        if (! empty($excludedDocumentTypes)) {
            $restriction = $this->makeInstance(DocumentTypeExclusionRestriction::class, [$excludedDocumentTypes]);
            $this->clause .= ' AND ' . $restriction->buildExpression([$this->table => $this->table], new ExpressionBuilder($this->cs()->db->getConnection()));
        }
    }
}
