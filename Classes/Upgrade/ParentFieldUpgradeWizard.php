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
 * Last modified: 2021.09.03 at 20:15
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Upgrade;


use LaborDigital\T3ba\Upgrade\AbstractChunkedUpgradeWizard;
use LaborDigital\T3plfe\Configuration\Table\Override\PagesTable;
use LaborDigital\T3plfe\FormEngine\Field\PageLayoutFormElement;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;

class ParentFieldUpgradeWizard extends AbstractChunkedUpgradeWizard
{
    
    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Migrates the "pages" table by filling the "t3plfe_parent" field with the reference to the containing record';
    }
    
    /**
     * @inheritDoc
     */
    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function executeUpdate(): bool
    {
        $pagesDh = $this->getDataHandler('pages');
        $pagesQuery = $this->getQuery('pages');
        
        foreach ($this->findRelevantTableFields() as $tableName => $fields) {
            $this->chunks = null;
            $this->selectFields = array_merge(['uid', 'pid'], $fields);
            $this->tableName = $tableName;
            
            $this->output->writeln('Starting table ' . $tableName . ' (' . implode(',', $fields) . ')...');
            
            $updates = 0;
            
            while ($chunk = $this->getChunk()) {
                foreach ($chunk as $row) {
                    foreach ($fields as $field) {
                        $pageId = $row[$field] ?? null;
                        if (! is_numeric($pageId)) {
                            $this->output->writeln(
                                'Ignoring field: ' . $field . ' of ' . $tableName . ' record ' . $row['uid'] . ', because it has no numeric content');
                            continue;
                        }
                        
                        $pageRow = $pagesQuery
                            ->withWhere(['uid' => $pageId])
                            ->withVersionOverlay(false)
                            ->getFirst(['uid', 'form_element_parent', PagesTable::PAGE_LAYOUT_FIELD]);
                        
                        if (! $pageRow || empty($pageRow['uid']) || empty($pageId)) {
                            $this->output->writeln(
                                'Could not find matching page for field: ' . $field . ' of ' . $tableName . ' record ' . $row['uid']
                            );
                            continue;
                        }
                        
                        if (! empty($pageRow[PagesTable::PAGE_LAYOUT_FIELD])) {
                            $this->output->writeln('Ignore page ' . $pageId . ' because ' . PagesTable::PAGE_LAYOUT_FIELD . ' field is not empty!');
                            continue;
                        }
                        
                        $pagesDh->save([
                            'uid' => $pageId,
                            PagesTable::PAGE_LAYOUT_FIELD => $tableName . '_' . $row['uid'],
                        ]);
                        
                        $updates++;
                    }
                }
            }
            
            if ($updates) {
                $this->output->write('Finished processing ' . $tableName . ' ' . $updates . ' pages were updated');
            }
        }
        
        return true;
    }
    
    /**
     * Finds the list of relevant table fields that have to be updated
     *
     * @return array
     */
    protected function findRelevantTableFields(): array
    {
        $list = [];
        foreach ($GLOBALS['TCA'] as $tableName => $tableConfig) {
            foreach ($tableConfig['columns'] as $columnName => $columnConfig) {
                if (($columnConfig['config']['t3baClass'] ?? null) === PageLayoutFormElement::class) {
                    $list[$tableName][] = $columnName;
                }
            }
        }
        
        return $list;
    }
    
}