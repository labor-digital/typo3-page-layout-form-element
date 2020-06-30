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
 * Last modified: 2020.06.30 at 12:49
 */

declare(strict_types=1);


namespace LaborDigital\Typo3PageLayoutFormElement\Middleware;


use LaborDigital\Typo3BetterApi\Container\TypoContainer;
use LaborDigital\Typo3BetterApi\Event\TypoEventBus;
use LaborDigital\Typo3BetterApi\TypoContext\TypoContext;
use LaborDigital\Typo3PageLayoutFormElement\Event\PageLayoutBackendOutputFilterEvent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PageLayoutFilterMiddleware implements MiddlewareInterface
{
    
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Check if we have work to do
        $context  = TypoContainer::getInstance()->get(TypoContext::class);
        $response = $handler->handle($request);
        if (! $context->Request()->getGet('pageLayoutContent')) {
            return $response;
        }
        
        // Allow filtering
        TypoEventBus::getInstance()->dispatch(($e = new PageLayoutBackendOutputFilterEvent($response)));
        
        return $e->getResponse();
    }
    
}
