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
 * Last modified: 2021.07.26 at 13:15
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Domain\Repository;


use LaborDigital\T3ba\Core\Di\PublicServiceInterface;
use LaborDigital\T3ba\Tool\Database\DbService;

/**
 * Class ParentRepository
 *
 * Generic repository for non-table-specific database actions on the parent records of the field
 *
 * @package LaborDigital\T3plfe\Domain\Repository
 */
class ParentRepository implements PublicServiceInterface
{
    
    /**
     * @var \LaborDigital\T3ba\Tool\Database\DbService
     */
    protected $db;
    
    public function __construct(DbService $db)
    {
        $this->db = $db;
    }
    
    /**
     * Finds the uid of the non translated record, where the translated record is given as $uid
     *
     * @param   string  $tableName  The name of the table to find the base record in
     * @param   int     $uid        The uid of the translated record to find the base record for
     *
     * @return int|null
     */
    public function findBaseRecordUid(string $tableName, int $uid): ?int
    {
        [$pointerField, $languageField] = $this->getConstraintFields($tableName);
        if (empty($pointerField) || empty($languageField)) {
            return null;
        }
        
        $result = $this->db->getQuery($tableName)
                           ->withIncludeHidden()
                           ->withIncludeDeleted()
                           ->withLanguage(false)
                           ->withWhere([
                               'uid' => $uid,
                               $languageField . ' IN' => [0, '-1'],
                               'or',
                               ['uid' => $uid],
                               $pointerField . ' >' => 0,
                               $languageField . ' >' => 0,
                           ])->getFirst([$pointerField]);
        
        if (empty($result) || ! isset($pointerField)) {
            return null;
        }
        
        return empty((int)$result[$pointerField]) ? null : (int)$result[$pointerField];
    }
    
    /**
     * Executes the given callback for ALL rows that represent a single record.
     * This includes the base record row.
     *
     * @param   string    $tableName  The name of the table to find records for
     * @param   int       $uid        the BASE record uid, (the not translated uid)
     * @param   callable  $callback   The callback to execute, it receives the raw database row as first value,
     *                                And the resolved database constraint fields as second
     */
    public function runForAllLanguages(string $tableName, int $uid, callable $callback): void
    {
        [$pointerField, $languageField] = $this->getConstraintFields($tableName);
        if (empty($pointerField) || empty($languageField)) {
            return;
        }
        
        $rows = $this->db->getQuery($tableName)
                         ->withIncludeHidden()
                         ->withLanguage(false)
                         ->withWhere([
                             'uid' => $uid,
                             $languageField . ' IN' => [0, '-1'],
                             'or',
                             $pointerField => $uid,
                             $languageField . ' >' => 0,
                         ])->getAll();
        
        array_map($callback, $rows, array_fill(0, count($rows), [$pointerField, $languageField]));
    }
    
    /**
     * Helper to resolve the translation constraint fields for a specific table
     *
     * @param   string  $tableName
     *
     * @return array|null[]
     */
    protected function getConstraintFields(string $tableName): array
    {
        $pointerField = $GLOBALS['TCA'][$tableName]['ctrl']['transOrigPointerField'] ?? null;
        $languageField = $GLOBALS['TCA'][$tableName]['ctrl']['languageField'] ?? null;
        
        return [$pointerField, $languageField];
    }
}