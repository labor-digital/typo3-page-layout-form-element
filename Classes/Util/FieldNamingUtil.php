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
 * Last modified: 2021.07.26 at 08:48
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Util;


use LaborDigital\T3ba\Core\Di\NoDiInterface;
use Neunerlei\Inflection\Inflector;

class FieldNamingUtil implements NoDiInterface
{
    /**
     * Extracts the table name from the given field name
     *
     * @param   string  $renderName  The field name to be parsed
     *
     * @return string
     */
    public static function getTableNameFromRenderName(string $renderName): string
    {
        $data = static::parseRenderName($renderName);
        
        return key($data) ?? '';
    }
    
    /**
     * Parses the given fieldName into its data structure (like any other input field name)
     *
     * @param   string  $renderName  The field name to be parsed
     * @param   int     $value       An optional value that should be defined for the inner most node
     *
     * @return array
     */
    public static function parseRenderName(string $renderName, int $value = 1): array
    {
        parse_str($renderName . '=' . $value, $data);
        
        return $data['data'] ?? [];
    }
    
    /**
     * Converts a field render name into a field id, matching the form engine naming schema
     *
     * @param   string  $renderName  The field name to be transformed
     *
     * @return string
     */
    public static function getFieldIdFromRenderName(string $renderName): string
    {
        return str_replace('-', '_', Inflector::toFile($renderName));
    }
    
    /**
     * Extracts the record uid from the given render name
     *
     * @param   string  $renderName  The field name to extract the uid from
     *
     * @return int
     */
    public static function getUidFromRenderName(string $renderName): int
    {
        $data = static::parseRenderName($renderName);
        $data = reset($data);
        
        return (int)(key($data) ?? 0);
    }
    
    /**
     * Helper to replace the uid inside a field render name with a new one
     *
     * @param   string  $renderName
     * @param   int     $uid
     *
     * @return string
     */
    public static function updateUidInRenderName(string $renderName, int $uid): string
    {
        $tableName = static::getTableNameFromRenderName($renderName);
        
        return preg_replace('~(data\[' . $tableName . ']\[)(.*?)]~', '${1}' . $uid . ']', $renderName);
    }
}