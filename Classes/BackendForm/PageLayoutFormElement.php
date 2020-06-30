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
 * Last modified: 2019.07.09 at 21:23
 */

namespace LaborDigital\Typo3PageLayoutFormElement\BackendForm;


use LaborDigital\Typo3BetterApi\BackendForms\CustomElements\AbstractCustomElement;
use LaborDigital\Typo3BetterApi\BackendForms\CustomElements\CustomElementContext;
use LaborDigital\Typo3BetterApi\BackendForms\CustomElements\CustomElementFormActionContext;
use LaborDigital\Typo3BetterApi\Container\CommonServiceDependencyTrait;
use LaborDigital\Typo3BetterApi\Event\TypoEventBus;
use LaborDigital\Typo3PageLayoutFormElement\Domain\Table\Override\PagesOverride;
use LaborDigital\Typo3PageLayoutFormElement\Event\PageLayoutPageRowFilterEvent;
use Neunerlei\Arrays\Arrays;
use Neunerlei\Inflection\Inflector;

class PageLayoutFormElement extends AbstractCustomElement
{
    use CommonServiceDependencyTrait;
    
    protected const TEMPLATE
        = <<<MUSTACHE
{{{hiddenField}}}
{{#url}}
	<p>{{translate "plfe.clickOnButtonMessage"}}</p>
	<a href="{{url}}" class="btn btn-default">{{translate "plfe.editButtonLabel"}}</a>
{{/url}}
{{^url}}
	{{translate "plfe.noElementSaveFirstMessage"}}
{{/url}}
MUSTACHE;
    
    
    /**
     * @inheritDoc
     */
    public function render(CustomElementContext $context): string
    {
        $pageId  = $this->pageIdFromValue($context->getValue());
        $hasPage = ! empty($pageId) && $this->Page()->pageExists($pageId, true);
        
        if ($hasPage) {
            // Has a page
            // Build edit content url
            $url = $this->Links()->getBackendLink("web_layout", [
                "args" => [
                    "id"                => $pageId,
                    "pageLayoutContent" => 1,
                ],
            ]);
            
            $args = [
                "url" => ["url" => $url],
            ];
            
            $this->BackendSession()
                 ->set("pageLayoutElementParentUrl",
                     (string)$this->TypoContext()->Request()->getRootRequest()->getUri());
            
        } else {
            // Has no page
            $args = [];
        }
        
        // Render the template
        return $this->renderTemplate(static::TEMPLATE, $args);
    }
    
    /**
     * @inheritDoc
     */
    public function dataHandlerSaveFilter(CustomElementFormActionContext $context)
    {
        // Make sure we always have a page we can go to
        if (! $this->Page()->pageExists($this->pageIdFromValue($context->getValue()), true)) {
            // Prepare the storage pid
            $defaultPid = $context->getRow()["pid"];
            if (empty($defaultPid) || ! is_int($defaultPid)) {
                $defaultPid = $context->Pid;
            }
            $parentPid = $context->getOption("storagePid", $defaultPid);
            if ($parentPid < 0) {
                $parentPid = $defaultPid;
            }
            if (is_null($parentPid)) {
                $parentPid = $this->TypoContext()->Pid()->getCurrent();
            }
            
            // Build page title
            $fieldLabel = $this->Translation()->translateMaybe(
                (string)Arrays::getPath($context->getConfig(), ["label"], ""));
            $tableLabel = $this->Translation()->translateMaybe(
                (string)Arrays::getPath($GLOBALS, ["TCA", $context->getTableName(), "ctrl", "title"], ""));
            $title      = array_values(array_filter([$tableLabel, $fieldLabel]));
            if (empty($title)) {
                $title = [
                    Inflector::toHuman($context->getTableName()),
                    Inflector::toHuman($context->getKey()),
                ];
            }
            $title[0] = "Content elements of: " . $title[0];
            $title[]  = $context->getUid();
            $title    = implode(" - ", $title);
            
            // Make the new page array
            $pageRow = Arrays::merge($context->getOption("addToPageRow", []), [
                "form_element_parent" => 1,
                "doktype"             => PagesOverride::PAGE_LAYOUT_DOK_TYPE,
                "nav_hide"            => 1,
                "backend_layout"      => "pagets__page_layout_form_element_content",
            ]);
            TypoEventBus::getInstance()
                        ->dispatch(($e = new PageLayoutPageRowFilterEvent($pageRow, $title, $context)));
            
            // Create the new page and set it as our value
            $respectPermissions = $context->getOption("respectUserPermissions", false);
            $newPageId          = $this->Page()->createNewPage($parentPid, [
                "force"   => ! $respectPermissions,
                "title"   => $e->getTitle(),
                "pageRow" => $e->getRow(),
            ]);
            $context->setValue([$newPageId]);
        }
        
    }
    
    /**
     * @inheritDoc
     */
    public function dataHandlerActionHandler(CustomElementFormActionContext $context)
    {
        // Ignore if there is somehow no page..
        $pageId = $this->pageIdFromValue($context->getValue());
        if (! $this->Page()->pageExists($pageId, false, true)) {
            return;
        }
        
        // Handle the action
        switch ($context->getAction()) {
            case "copy":
                $newPid = $this->Page()->copyPage($pageId, ["force"]);
                $context->setValue($newPid);
                break;
            case "move":
                $this->Page()->movePage($pageId, (int)$context->getRow()["pid"], true);
                break;
            case "delete":
                $this->Page()->deletePage($pageId, true);
                break;
            case "undelete":
                $this->Page()->restorePage($pageId, true);
                break;
        }
    }
    
    /**
     * Internal helper to make sure we handle arrays and integers as values correctly
     *
     * @param $value
     *
     * @return int
     */
    protected function pageIdFromValue($value): int
    {
        return (int)(is_array($value) ? Arrays::shorten($value) : $value);
    }
}
