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
 * Last modified: 2021.07.26 at 14:15
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\EventHandler;


use LaborDigital\T3ba\Core\CodeGeneration\ClassOverrideGenerator;
use LaborDigital\T3ba\Event\Core\BasicBootDoneEvent;
use LaborDigital\T3plfe\Override\ExtendedAbstractTreeView;
use Neunerlei\EventBus\Subscription\EventSubscriptionInterface;
use Neunerlei\EventBus\Subscription\LazyEventSubscriberInterface;
use TYPO3\CMS\Backend\Tree\View\AbstractTreeView;

class Override implements LazyEventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function subscribeToEvents(EventSubscriptionInterface $subscription): void
    {
        $subscription->subscribe(BasicBootDoneEvent::class, 'onBootDone');
    }
    
    public function onBootDone(): void
    {
        // We need to override some core classes to hide the content pages in the display options
        ClassOverrideGenerator::registerOverride(AbstractTreeView::class, ExtendedAbstractTreeView::class);
    }
}