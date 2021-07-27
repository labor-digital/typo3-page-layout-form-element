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
 * Last modified: 2021.07.25 at 22:33
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\EventHandler;


use GuzzleHttp\Psr7\Utils;
use LaborDigital\T3ba\Core\Di\ContainerAwareTrait;
use LaborDigital\T3ba\Event\Backend\DbListQueryFilterEvent;
use LaborDigital\T3ba\Event\Core\ExtTablesLoadedEvent;
use LaborDigital\T3plfe\Event\NewCeWizardInIframeFilterEvent;
use LaborDigital\T3plfe\Event\PageLayoutBackendOutputFilterEvent;
use LaborDigital\T3plfe\Middleware\PageLayoutFilterMiddleware;
use LaborDigital\T3plfe\Service\RewriteService;
use Neunerlei\EventBus\Subscription\EventSubscriptionInterface;
use Neunerlei\EventBus\Subscription\LazyEventSubscriberInterface;

class Rewrites implements LazyEventSubscriberInterface
{
    use ContainerAwareTrait;
    
    /**
     * @inheritDoc
     */
    public static function subscribeToEvents(EventSubscriptionInterface $subscription): void
    {
        $subscription->subscribe(NewCeWizardInIframeFilterEvent::class, 'onNewCeWizardFilter');
        $subscription->subscribe(PageLayoutBackendOutputFilterEvent::class, 'onBackendOutputFilter');
        $subscription->subscribe(ExtTablesLoadedEvent::class, 'onCleanUpHooks');
        $subscription->subscribe(DbListQueryFilterEvent::class, 'onFilterDbTableRows');
    }
    
    public function onNewCeWizardFilter(NewCeWizardInIframeFilterEvent $event): void
    {
        $body = (string)$event->getResponse()->getBody();
        
        $body = $this->getService(RewriteService::class)
                     ->provideIframeFix(
                         $body,
                         $event->getRequest()->getQueryParams()[PageLayoutFilterMiddleware::PAGE_LAYOUT_IFRAME_MARKER] ?? ''
                     );
        
        $event->setResponse($event->getResponse()->withBody(Utils::streamFor($body)));
    }
    
    public function onBackendOutputFilter(PageLayoutBackendOutputFilterEvent $event): void
    {
        $body = (string)$event->getResponse()->getBody();
        
        $body = $this->getService(RewriteService::class)
                     ->cleanUpPageModule(
                         $body,
                         ! empty($event->getRequest()->getQueryParams()[PageLayoutFilterMiddleware::PAGE_LAYOUT_IFRAME_MARKER])
                     );
        
        $event->setResponse($event->getResponse()->withBody(Utils::streamFor($body)));
    }
    
    public function onCleanUpHooks(): void
    {
        // Ignore if this link is not interesting for us
        if (! $this->cs()->typoContext->request()->hasGet(PageLayoutFilterMiddleware::PAGE_LAYOUT_IFRAME_MARKER)) {
            return;
        }
        
        // Remove other render plugins to make sure there are no other plugins that could interfere with our endeavour
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'] = null;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawFooterHook'] = null;
    }
    
    public function onFilterDbTableRows(DbListQueryFilterEvent $event): void
    {
        if ($event->getTableName() !== 'pages') {
            return;
        }
        
        // Make sure we don't show our custom pages
        $connection = $this->cs()->db->getConnectionPool()->getConnectionForTable('pages');
        $event->setAdditionalWhereClause(
            (empty($event->getAdditionalWhereClause()) ? '' : '(' . $event->getAdditionalWhereClause() . ')') .
            ' AND ' . $connection->quoteIdentifier('doktype') . ' IS NULL');
    }
    
}