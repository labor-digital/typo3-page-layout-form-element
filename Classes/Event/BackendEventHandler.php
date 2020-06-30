<?php
declare(strict_types=1);
/**
 * Copyright 2019 LABOR.digital
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
 * Last modified: 2019.07.09 at 23:28
 */

namespace LaborDigital\Typo3PageLayoutFormElement\Event;

use LaborDigital\Typo3BetterApi\Container\TypoContainer;
use LaborDigital\Typo3BetterApi\Domain\DbService\DbService;
use LaborDigital\Typo3BetterApi\Event\Events\BackendDbListQueryFilterEvent;
use LaborDigital\Typo3BetterApi\Event\Events\ExtTablesLoadedEvent;
use LaborDigital\Typo3BetterApi\Rendering\TemplateRenderingService;
use LaborDigital\Typo3BetterApi\Session\SessionService;
use LaborDigital\Typo3BetterApi\TypoContext\TypoContext;
use Neunerlei\EventBus\Subscription\EventSubscriptionInterface;
use Neunerlei\EventBus\Subscription\LazyEventSubscriberInterface;
use function GuzzleHttp\Psr7\stream_for;

class BackendEventHandler implements LazyEventSubscriberInterface
{
    
    /**
     * @var \LaborDigital\Typo3BetterApi\TypoContext\TypoContext
     */
    protected $context;
    
    /**
     * @var \LaborDigital\Typo3BetterApi\Rendering\TemplateRenderingService
     */
    protected $renderingService;
    
    /**
     * @var \LaborDigital\Typo3BetterApi\Session\SessionService
     */
    protected $session;
    
    /**
     * BackendEventHandler constructor.
     *
     * @param   \LaborDigital\Typo3BetterApi\TypoContext\TypoContext             $context
     * @param   \LaborDigital\Typo3BetterApi\Rendering\TemplateRenderingService  $renderingService
     * @param   \LaborDigital\Typo3BetterApi\Session\SessionService              $session
     */
    public function __construct(
        TypoContext $context,
        TemplateRenderingService $renderingService,
        SessionService $session
    ) {
        $this->context          = $context;
        $this->renderingService = $renderingService;
        $this->session          = $session;
    }
    
    /**
     * @inheritDoc
     */
    public static function subscribeToEvents(EventSubscriptionInterface $subscription)
    {
        $subscription->subscribe(PageLayoutBackendOutputFilterEvent::class, "onBackendOutputFilter");
        $subscription->subscribe(ExtTablesLoadedEvent::class, "onCleanUpHooks");
        $subscription->subscribe(BackendDbListQueryFilterEvent::class, "onFilterDbTableRows");
    }
    
    /**
     * Is used to remove all hooks from the page module when we are showing the content elements of a field.
     */
    public function onCleanUpHooks()
    {
        // Ignore if this link is not interesting for us
        if (! $this->context->Request()->getGet("pageLayoutContent")) {
            return;
        }
        
        // Remove other render plugins to make sure there are no other plugins that could interfere with our endeavour
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'] = null;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawFooterHook'] = null;
    }
    
    /**
     * Is used to clean up the page module from unnecessary junk we don't want to show the editor.
     *
     * If there is ANY way to do it better than this. Tell me and I will change it immediately
     *
     * @param   \LaborDigital\Typo3PageLayoutFormElement\Event\PageLayoutBackendOutputFilterEvent  $event
     */
    public function onBackendOutputFilter(PageLayoutBackendOutputFilterEvent $event)
    {
        // Get the html of the page
        $response = $event->getResponse();
        $html     = (string)$response->getBody();
        
        // Strip away everything we don't need
        // Remove the preview button...
        $pattern = "/<a[^>]*?onclick=\"var preview.*?<\\/a>/si";
        $html    = preg_replace($pattern, "", $html);
        
        // Remove the "path" info...
        $pattern = "/(<div class=\"module-docheader-bar-column-right.*?text-right.*?>)(.*?)(<\\/div>)/si";
        $html    = preg_replace($pattern, "$1$3", $html);
        
        // Remove the right buttons...
        $pattern = "/(<div class=\"module-docheader-bar-column-right\".*?>)(.|\n)*?((\s|\n)+<\/div>){3}/si";
        $html    = preg_replace($pattern, "</div>", $html);
        
        // Remove everything above the content...
        $pattern = "/(<form.*?>).*?(<div class=\"(?:form-inline|t3-grid-container))/si";
        $html    = preg_replace($pattern, "$1$2", $html);
        
        // Remove the page preview and edit buttons...
        $pattern = "/<td[^>]*?t3-page-lang-label.*?<\\/td>/si";
        $html    = preg_replace($pattern, "", $html);
        
        // Fix the "function" links in the select box
        $pattern = "/(<option value=\"\\/[^\"]*?)\"/si";
        $html    = preg_replace($pattern,
            "$1&pageLayoutContent=1&backUrl=" .
            urlencode($this->context->Request()->getGet("backUrl", '')) . "\"", $html);
        
        // Inject the "back" button
        $pattern = "/(class=\"module-docheader-bar-column-left\">[^>]*?class=\"btn-toolbar[^>]*?>)/si";
        $html    = preg_replace($pattern, "$1" . $this->getBackButtonContent(), $html);
        
        // Update response
        $event->setResponse($response->withBody(stream_for($html)));
    }
    
    /**
     * Makes sure that there are no pages we created in the "list" module
     *
     * @param   \LaborDigital\Typo3BetterApi\Event\Events\BackendDbListQueryFilterEvent  $event
     */
    public function onFilterDbTableRows(BackendDbListQueryFilterEvent $event): void
    {
        // Ignore if we don't apply to the pages table
        if ($event->getTableName() !== "pages") {
            return;
        }
        
        // Make sure we don't show our custom pages
        $connection = TypoContainer::getInstance()->get(DbService::class)->getConnection();
        $event->setAdditionalWhereClause(
            (empty($event->getAdditionalWhereClause())
                ? '' : '(' . $event->getAdditionalWhereClause() . ')') .
            ' AND ' . $connection->quoteIdentifier('form_element_parent') . ' IS NULL');
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
            "href" => $this->session->getBackendSession()->get("pageLayoutElementParentUrl"),
        ]);
    }
}
