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
 * Last modified: 2021.07.27 at 14:53
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Service;


use LaborDigital\T3ba\Core\Di\PublicServiceInterface;
use LaborDigital\T3ba\Tool\Rendering\TemplateRenderingService;
use LaborDigital\T3ba\Tool\Session\SessionService;
use LaborDigital\T3ba\Tool\TypoContext\TypoContext;
use LaborDigital\T3plfe\Middleware\PageLayoutFilterMiddleware;

class RewriteService implements PublicServiceInterface
{
    /**
     * @var \LaborDigital\T3ba\Tool\Session\SessionService
     */
    protected $sessionService;
    
    /**
     * @var \LaborDigital\T3ba\Tool\Rendering\TemplateRenderingService
     */
    protected $renderingService;
    /**
     * @var \LaborDigital\T3ba\Tool\TypoContext\TypoContext
     */
    protected $typoContext;
    
    public function __construct(
        SessionService $sessionService,
        TemplateRenderingService $renderingService,
        TypoContext $typoContext
    )
    {
        $this->sessionService = $sessionService;
        $this->renderingService = $renderingService;
        $this->typoContext = $typoContext;
    }
    
    /**
     * Is used to clean up the page module from unnecessary junk we don't want to show the editor.
     *
     * If there is ANY way to do it better than this. Tell me and I will change it immediately
     *
     * @param   string  $body
     *
     * @return string
     */
    public function cleanUpPageModule(string $body, bool $forIframeRequest = false): string
    {
        $replacements = [
            // Strip away everything we don't need
            // Remove the preview button...
            '/<a[^>]*?onclick="var preview.*?<\/a>/si' => '',
            // Remove the "path" info...
            '/(<div class="module-docheader-bar-column-right.*?text-right.*?>)(.*?)(<\/div>)/si' => '$1$3',
            // Remove the right buttons...
            '/(<div class="module-docheader-bar-column-right".*?>)(.|\n)*?((\s|\n)+<\/div>){3}/si' => '</div>',
            // Remove everything above the content...
            '/(<form.*?>).*?(<div class="(?:form-inline|t3-grid-container))/si' => '$1$2',
            // Remove the page preview and edit buttons...
            '/<td[^>]*?t3-page-lang-label.*?<\/td>/si' => '',
            // Remove search button...
            '/<a[^>]*?class="[^">]*?t3js-toggle-search-toolbox.*?<\/a>/si' => '',
            // Fix the "function" links in the select box
            '/(<option value="\/[^"]*?)"/si' =>
                '$1&' . PageLayoutFilterMiddleware::PAGE_LAYOUT_REQUEST_MARKER . '=1&backUrl=' .
                urlencode($this->typoContext->request()->getGet('backUrl', '')) .
                '&' . PageLayoutFilterMiddleware::PAGE_LAYOUT_IFRAME_MARKER . '=' . ($forIframeRequest ? '1' : '') .
                '"',
        
        ];
        
        if (! $forIframeRequest) {
            // Inject the "back" button
            $replacements['/(class="module-docheader-bar-column-left">[^>]*?class="btn-toolbar[^>]*?>)/si']
                = '$1' . $this->getBackButtonContent();
        }
        
        return preg_replace(array_keys($replacements), $replacements, $body);
    }
    
    /**
     * Rewrites the goToalt_doc() function inside a new content wizard request,
     * so it points to our preview iframe instead of the main content frame of the backend layout.
     *
     * @param   string  $body
     * @param   string  $iframeId
     *
     * @return string
     */
    public function provideIframeFix(string $body, string $iframeId): string
    {
        return preg_replace('~(goToalt_doc\(\) { list_frame\.)~si',
            '$1document.getElementById("' . $iframeId . '").contentWindow.', $body);
    }
    
    /**
     * Renders our "back" button code
     *
     * @return string
     */
    protected function getBackButtonContent(): string
    {
        $tpl = <<<HTML
	<div class="btn-group" role="group" aria-label>
		<a href="{{href}}" class="btn btn-default btn-sm">
			{{translate "plfe.backButtonLabel"}}
		</a>
	</div>
HTML;
        
        return $this->renderingService->renderMustache($tpl, [
            'href' => $this->sessionService->getBackendSession()->get('plfe_return_url'),
        ]);
    }
}