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
 * Last modified: 2021.07.23 at 10:03
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Event;


class PageLayoutPageRowFilterEvent
{
    /**
     * The raw database row of the page to save
     *
     * @var array
     */
    protected $row;
    
    /**
     * The generated title of the page to generate
     *
     * @var string
     */
    protected $title;
    
    /**
     * The name of the parent table that contains the page layout field
     *
     * @var string
     */
    protected $tableName;
    
    /**
     * The absolute render name of the field inside of $tableName that is the page layout field
     *
     * @var string
     */
    protected $fieldRenderName;
    
    /**
     * The record uid in $tableName that references the page layout field
     *
     * @var int
     */
    protected $recordUid;
    
    /**
     * The pid that should contain the new content page
     *
     * @var int
     */
    protected $storagePid;
    
    /**
     * The options provided for the layout field
     *
     * @var array
     */
    protected $options;
    
    /**
     * PageLayoutPageRowFilterEvent constructor.
     *
     * @param   array   $row
     * @param   string  $title
     * @param   string  $fieldRenderName
     * @param   int     $recordUid
     * @param   int     $storagePid
     */
    public function __construct(
        array $row,
        string $title,
        string $tableName,
        string $fieldRenderName,
        int $recordUid,
        int $storagePid,
        array $options
    )
    {
        $this->row = $row;
        $this->title = $title;
        $this->fieldRenderName = $fieldRenderName;
        $this->recordUid = $recordUid;
        $this->storagePid = $storagePid;
        $this->tableName = $tableName;
        $this->options = $options;
    }
    
    /**
     * Returns the raw database row of the page to save
     *
     * @return array
     */
    public function getRow(): array
    {
        return $this->row;
    }
    
    /**
     * Updates the raw database row of the page to save
     *
     * @param   array  $row
     *
     * @return PageLayoutPageRowFilterEvent
     */
    public function setRow(array $row): PageLayoutPageRowFilterEvent
    {
        $this->row = $row;
        
        return $this;
    }
    
    /**
     * Returns the generated title of the page to generate
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
    
    /**
     * Updates the generated title of the page to generate
     *
     * @param   string  $title
     *
     * @return PageLayoutPageRowFilterEvent
     */
    public function setTitle(string $title): PageLayoutPageRowFilterEvent
    {
        $this->title = $title;
        
        return $this;
    }
    
    /**
     * Returns the name of the parent table that contains the page layout field
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }
    
    /**
     * Returns the absolute render name of the field inside of $tableName that is the page layout field
     *
     * @return string
     */
    public function getFieldRenderName(): string
    {
        return $this->fieldRenderName;
    }
    
    /**
     * Return the record uid in $tableName that references the page layout field
     *
     * @return int
     */
    public function getRecordUid(): int
    {
        return $this->recordUid;
    }
    
    /**
     * The pid that should contain the new content page
     *
     * @return int
     */
    public function getStoragePid(): int
    {
        return $this->storagePid;
    }
    
    /**
     * Return the options provided for the layout field
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
