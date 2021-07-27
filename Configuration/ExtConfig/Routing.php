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
 * Last modified: 2021.07.27 at 09:29
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Configuration\ExtConfig;


use LaborDigital\T3ba\ExtConfig\ExtConfigContext;
use LaborDigital\T3ba\ExtConfigHandler\Routing\ConfigureRoutingInterface;
use LaborDigital\T3ba\ExtConfigHandler\Routing\RoutingConfigurator;
use LaborDigital\T3plfe\Controller\Backend\ButtonActionController;
use LaborDigital\T3plfe\Middleware\PageLayoutFilterMiddleware;

class Routing implements ConfigureRoutingInterface
{
    
    /**
     * @inheritDoc
     */
    public static function configureRouting(RoutingConfigurator $configurator, ExtConfigContext $context): void
    {
        $configurator->registerMiddleware(PageLayoutFilterMiddleware::class, ['stack' => 'backend']);
        
        $configurator->registerBackendRoute('/plfe/create-page', ButtonActionController::class, 'createPage', ['ajax'])
                     ->registerBackendRoute('/plfe/delete-page', ButtonActionController::class, 'deletePage', ['ajax']);
    }
    
}