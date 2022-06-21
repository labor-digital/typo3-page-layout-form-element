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
 * Last modified: 2021.07.27 at 09:35
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Middleware;


use LaborDigital\T3plfe\Event\NewCeWizardInIframeFilterEvent;
use LaborDigital\T3plfe\Event\PageLayoutBackendOutputFilterEvent;
use Neunerlei\PathUtil\Path;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PageLayoutFilterMiddleware implements MiddlewareInterface
{
    public const PAGE_LAYOUT_REQUEST_MARKER = 'plfe_page_layout';
    public const PAGE_LAYOUT_IFRAME_MARKER = 'plfe_iframe';
    
    /**
     * @var \Psr\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;
    
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
    
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if (empty($request->getQueryParams()[static::PAGE_LAYOUT_REQUEST_MARKER])) {
            // Special handling for new content element wizard
            // If the return url is set, we check if it is executed in an iframe
            $route = $request->getQueryParams()['route'] ?? null;
            if (isset($request->getQueryParams()['returnUrl'])) {
                $uri = Path::makeUri($request->getQueryParams()['returnUrl']);
                parse_str($uri->getQuery(), $query);
                if ($route === '/record/content/wizard/new') {
                    if (empty($query[static::PAGE_LAYOUT_IFRAME_MARKER])) {
                        return $response;
                    }
                    
                    return $this->eventDispatcher
                        ->dispatch(new NewCeWizardInIframeFilterEvent($response, $request->withQueryParams($query)))
                        ->getResponse();
                }
                
                if (isset($query[static::PAGE_LAYOUT_REQUEST_MARKER])) {
                    $request = $request->withQueryParams($query);
                } else {
                    return $response;
                }
            } else {
                return $response;
            }
        }
        
        return $this->eventDispatcher
            ->dispatch(new PageLayoutBackendOutputFilterEvent($response, $request))
            ->getResponse();
    }
    
}
