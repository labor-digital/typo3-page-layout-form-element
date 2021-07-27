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
 * Last modified: 2021.07.27 at 09:59
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\EventHandler;


use LaborDigital\T3plfe\Domain\Model\PageLayout;
use Neunerlei\EventBus\Subscription\EventSubscriptionInterface;
use Neunerlei\EventBus\Subscription\LazyEventSubscriberInterface;
use TYPO3\CMS\Extbase\Event\Persistence\ModifyQueryBeforeFetchingObjectDataEvent;

class ExtbaseQuery implements LazyEventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function subscribeToEvents(EventSubscriptionInterface $subscription): void
    {
        $subscription->subscribe(ModifyQueryBeforeFetchingObjectDataEvent::class, 'onQueryModify');
    }
    
    /**
     * Modifies the extbase query when page layout objects are hydrated.
     * This allows the query to resolve hidden records, which is a hack we use to prevent the
     * Pages from showing up randomly on the page
     *
     * @param   \TYPO3\CMS\Extbase\Event\Persistence\ModifyQueryBeforeFetchingObjectDataEvent  $event
     */
    public function onQueryModify(ModifyQueryBeforeFetchingObjectDataEvent $event): void
    {
        if ($event->getQuery()->getType() === PageLayout::class) {
            $settings = $event->getQuery()->getQuerySettings();
            $settings->setEnableFieldsToBeIgnored(['disabled']);
            $settings->setIgnoreEnableFields(true);
        }
    }
}