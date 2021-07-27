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
 * Last modified: 2021.07.27 at 09:55
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Controller\Backend;


use LaborDigital\T3ba\Core\Di\ContainerAwareTrait;
use LaborDigital\T3plfe\Service\PageLayoutService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

class ButtonActionController
{
    use ContainerAwareTrait;
    
    /**
     * Handles the request to create a new content page when the button is clicked
     *
     * @param   \Psr\Http\Message\ServerRequestInterface  $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createPage(ServerRequestInterface $request): ResponseInterface
    {
        [$renderName, $storagePid, $title, $language] = $this->extractRequestParams($request);
        
        if (empty($renderName) || empty($storagePid) || empty($title)) {
            return new JsonResponse(['data' => ['error' => ['Not all parameters were provided']]], 400);
        }
        
        $contentService = $this->getService(PageLayoutService::class);
        $options = $this->getOptions($renderName);
        $contentPid = $contentService->createPage($renderName, $storagePid, $title, $language, $options);
        
        if ($options['noIframe'] ?? null) {
            $iframe = $contentService->renderActionButtons($renderName, $contentPid, $language);
        } else {
            $iframe = $contentService->renderIframe($renderName, $contentPid, $language);
        }
        
        return new JsonResponse([
            'data' => [
                'iframe' => $iframe,
            ],
        ]);
    }
    
    /**
     * Handles the deletion request of content pages via ajax
     *
     * @param   \Psr\Http\Message\ServerRequestInterface  $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deletePage(ServerRequestInterface $request): ResponseInterface
    {
        [$renderName, $contentPid] = $this->extractRequestParams($request);
        
        if (empty($renderName) || empty($contentPid)) {
            return new JsonResponse(['data' => ['error' => ['Not all parameters were provided']]], 400);
        }
        
        $this->getService(PageLayoutService::class)->deletePage($renderName, $contentPid, $this->getOptions($renderName));
        
        return new JsonResponse([
            'data' => [
                'status' => 'OK',
            ],
        ]);
    }
    
    /**
     * Internal helper to retrieve the request parameters and return them as an array
     *
     * @param   \Psr\Http\Message\ServerRequestInterface  $request
     *
     * @return array
     */
    protected function extractRequestParams(ServerRequestInterface $request): array
    {
        $query = $request->getQueryParams();
        
        return [
            $query['field'] ?? '',
            (int)($query['pid'] ?? 0),
            $query['title'] ?? ('Content page: ' . md5(random_bytes(22))),
            isset($query['lang']) ? (int)$query['lang'] : null,
        ];
    }
    
    /**
     * Reads the options from the session storage
     *
     * @param   string  $renderName  The name of the field to find the options for
     *
     * @return array|null
     */
    protected function getOptions(string $renderName): ?array
    {
        return $this->cs()->session->getBackendSession()->get('plfe_ajax_options', [])[$renderName] ?? null;
    }
}