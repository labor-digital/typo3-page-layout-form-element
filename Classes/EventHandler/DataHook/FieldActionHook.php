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
 * Last modified: 2021.07.27 at 09:27
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\EventHandler\DataHook;


use LaborDigital\T3ba\Core\Di\PublicServiceInterface;
use LaborDigital\T3ba\Tool\FormEngine\Custom\Field\CustomFieldDataHookContext;
use LaborDigital\T3plfe\Service\PageLayoutService;

class FieldActionHook implements PublicServiceInterface
{
    
    /**
     * @var \LaborDigital\T3plfe\Service\PageLayoutService
     */
    protected $pageService;
    
    public function __construct(PageLayoutService $pageService)
    {
        $this->pageService = $pageService;
    }
    
    public function translateHook(CustomFieldDataHookContext $context): void
    {
        if (empty($context->getData())) {
            return;
        }
        
        $this->pageService->getPageRepository()->translatePage(
            $context->getData(),
            (int)$context->getEvent()->getValue(),
            $context->getOptions()
        );
        
        $context->setData($context->getData());
    }
    
    public function restoreHook(CustomFieldDataHookContext $context): void
    {
        if (empty($context->getData())) {
            return;
        }
        
        $this->pageService->getPageRepository()->restorePage(
            $context->getData(),
            $context->getOptions()
        );
    }
    
    public function deleteHook(CustomFieldDataHookContext $context): void
    {
        if (empty($context->getData())) {
            return;
        }
        
        $this->pageService->getPageRepository()->deletePage(
            $context->getData(),
            $context->getOptions()
        );
    }
    
    public function copyHook(CustomFieldDataHookContext $context): void
    {
        if (empty($context->getData())) {
            return;
        }
        
        $copiedPid = $this->pageService->getPageRepository()->copyPage(
            $context->getData(),
            (int)$context->getEvent()->getValue(),
            $context->getOptions()
        );
        
        $context->setData($copiedPid);
    }
    
    public function moveHook(CustomFieldDataHookContext $context): void
    {
        if (empty($context->getData())) {
            return;
        }
        
        $this->pageService->getPageRepository()->movePage(
            $context->getData(),
            (int)($context->getRow()['pid'] ?? 0),
            $context->getOptions()
        );
    }
}