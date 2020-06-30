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
 * Last modified: 2020.06.30 at 12:51
 */

declare(strict_types=1);


namespace LaborDigital\Typo3PageLayoutFormElement\Event;


use Psr\Http\Message\ResponseInterface;

/**
 * Class PageLayoutBackendOutputFilterEvent
 *
 * Emitted in the PageLayoutFilterMiddleware to allow filtering of the responded
 * backend content if the page layout is rendered
 *
 * @package LaborDigital\Typo3PageLayoutFormElement\Event
 */
class PageLayoutBackendOutputFilterEvent
{
    /**
     * The http response to filter
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;
    
    /**
     * PageLayoutBackendOutputFilterEvent constructor.
     *
     * @param   \Psr\Http\Message\ResponseInterface  $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }
    
    /**
     * Returns the http response to filter
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
    
    /**
     * Updates the http response to filter
     *
     * @param   \Psr\Http\Message\ResponseInterface  $response
     *
     * @return PageLayoutBackendOutputFilterEvent
     */
    public function setResponse(ResponseInterface $response): PageLayoutBackendOutputFilterEvent
    {
        $this->response = $response;
        
        return $this;
    }
    
    
}
