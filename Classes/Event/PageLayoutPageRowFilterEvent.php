<?php
/**
 * Copyright 2020 LABOR.digital
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
 * Last modified: 2020.06.30 at 12:39
 */

declare(strict_types=1);


namespace LaborDigital\Typo3PageLayoutFormElement\Event;


use LaborDigital\Typo3BetterApi\BackendForms\CustomElements\CustomElementFormActionContext;

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
     * The context used to generate the page
     *
     * @var \LaborDigital\Typo3BetterApi\BackendForms\CustomElements\CustomElementFormActionContext
     */
    protected $context;
    
    /**
     * PageLayoutPageRowFilterEvent constructor.
     *
     * @param   array                           $row
     * @param   string                          $title
     * @param   CustomElementFormActionContext  $context
     */
    public function __construct(array $row, string $title, CustomElementFormActionContext $context)
    {
        $this->row     = $row;
        $this->title   = $title;
        $this->context = $context;
    }
    
    /**
     * Returns the context used to generate the page
     *
     * @return \LaborDigital\Typo3BetterApi\BackendForms\CustomElements\CustomElementFormActionContext
     */
    public function getContext(): CustomElementFormActionContext
    {
        return $this->context;
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
}
