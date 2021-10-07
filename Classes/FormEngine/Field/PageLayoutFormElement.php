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
 * Last modified: 2021.07.27 at 11:33
 */

declare(strict_types=1);

namespace LaborDigital\T3plfe\FormEngine\Field;


use Doctrine\DBAL\Types\IntegerType;
use LaborDigital\T3ba\Tool\DataHook\DataHookTypes;
use LaborDigital\T3ba\Tool\FormEngine\Custom\Field\AbstractCustomField;
use LaborDigital\T3ba\Tool\Tca\Builder\Logic\AbstractField;
use LaborDigital\T3ba\Tool\Tca\Builder\TcaBuilderContext;
use LaborDigital\T3ba\Tool\Tca\Builder\Type\Table\TcaField;
use LaborDigital\T3ba\Tool\Tca\TcaUtil;
use LaborDigital\T3plfe\EventHandler\DataHook\FieldActionHook;
use LaborDigital\T3plfe\Service\PageLayoutService;
use LaborDigital\Typo3PageLayoutFormElement\Domain\Table\Override\PagesOverride;
use LaborDigital\Typo3PageLayoutFormElement\Event\PageLayoutPageRowFilterEvent;
use Neunerlei\Inflection\Inflector;

class PageLayoutFormElement extends AbstractCustomField
{
    
    protected const TEMPLATE
        = <<<MUSTACHE
{{{hiddenField}}}
{{#saved}}
    <div id="{{renderId}}_empty" {{{emptyGuiStyle}}}>
        {{{emptyGui}}}
    </div>
    <div id="{{renderId}}_container" {{{containerStyle}}}>
        {{{iframe}}}
    </div>
{{/saved}}
{{^saved}}
	{{translate "plfe.noElementSaveFirstMessage"}}
{{/saved}}
MUSTACHE;
    
    /**
     * @var \LaborDigital\T3plfe\Service\PageLayoutService
     */
    protected $contentPageService;
    
    public function __construct(PageLayoutService $contentPageService)
    {
        $this->contentPageService = $contentPageService;
    }
    
    /**
     * @inheritDoc
     */
    public static function configureField(AbstractField $field, array $options, TcaBuilderContext $context): void
    {
        $field->registerDeleteHook(FieldActionHook::class)
              ->registerRestoreHook(FieldActionHook::class)
              ->registerCopyHook(FieldActionHook::class)
              ->registerMoveHook(FieldActionHook::class)
              ->registerDataHook(DataHookTypes::TYPE_COPY_TO_LANGUAGE, FieldActionHook::class, 'translateHook')
              ->registerDataHook(DataHookTypes::TYPE_LOCALIZE, FieldActionHook::class, 'translateHook');
        
        if ($field instanceof TcaField) {
            $field->getColumn()->setType(new IntegerType())->setDefault(0)->setLength(11);
        }
    }
    
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->context->registerRequireJsModule(
            'TYPO3/CMS/T3plfe/FieldActions',
            'module("' . $this->context->getRenderId() . '");'
        );
        
        $uid = $this->context->getRecordUid();
        $args = [];
        
        if ($uid > 0) {
            $contentPid = (int)TcaUtil::getRowValue(['t' => $this->context->getValue()], 't');
            $hasPage = $this->contentPageService->getPageRepository()->hasPage($contentPid);
            
            $this->saveOptions();
            $this->saveReturnUrl();
            
            $language = TcaUtil::getLanguageUid($this->context->getRow(), $this->context->getTableName());
            $language = ((int)$language === -1) ? null : $language;
            
            $args['emptyGui'] = $this->contentPageService->renderEmptyUi(
                $this->resolveContentStoragePid(),
                $this->context->getRenderName(),
                $this->makeNewPageTitle(),
                $language
            );
            $args['saved'] = true;
            
            if ($hasPage) {
                if ($this->context->getOption('noIframe')) {
                    $args['iframe'] = $this->contentPageService->renderActionButtons(
                        $this->context->getRenderName(), $contentPid, $language);
                } else {
                    $args['iframe'] = $this->contentPageService->renderIframe(
                        $this->context->getRenderName(), $contentPid, $language);
                }
                $args['emptyGuiStyle'] = ' style="display:none";';
            } else {
                $args['containerStyle'] = ' style="display:none";';
            }
        }
        
        return $this->renderTemplate(static::TEMPLATE, $args);
    }
    
    /**
     * Stores the options provided for the layout field into the session,
     * so our ajax handler can work with them
     */
    protected function saveOptions(): void
    {
        $sess = $this->cs()->session->getBackendSession();
        $sess->set('plfe_ajax_options', array_merge(
            $sess->get('plfe_ajax_options', []),
            [$this->context->getRenderName() => $this->context->getOptions()]
        ));
    }
    
    /**
     * Stores the current request url in the session, so we can use it on the
     * content pages as a "return url"
     */
    protected function saveReturnUrl(): void
    {
        $sess = $this->cs()->session->getBackendSession();
        $rootRequest = $this->cs()->typoContext->request()->getRootRequest();
        $uri = ! $rootRequest ? null : (string)$rootRequest->getUri();
        
        if ($uri === null) {
            return;
        }
        
        // @todo remove this in the next major release - I keep it for now as a comment
//        if (strpos($uri, '=new') !== false) {
//            $uri = preg_replace(
//                '/%5D[^=]*?=new(?:&|$)/',
//                urlencode('][' . $context->getRecordUid() . ']') . '=edit&',
//                $uri, 1);
//        }
        
        $sess->set('plfe_return_url', $uri);
    }
    
    /**
     * Resolves the storage pid for the new page based on the given options
     *
     * @return int
     */
    protected function resolveContentStoragePid(): int
    {
        $defaultPid = TcaUtil::getRowValue($this->context->getRow(), 'pid');
        
        if ($defaultPid === null || $defaultPid === 0) {
            $defaultPid = $this->cs()->typoContext->pid()->getCurrent();
        }
        
        $storagePid = $this->context->getOption('storagePid', $defaultPid);
        
        if ($storagePid === null || $storagePid < 0) {
            $storagePid = $defaultPid;
        }
        
        return $storagePid;
    }
    
    /**
     * Generates the title of the new page based on the current table and field configuration
     *
     * @return string
     */
    protected function makeNewPageTitle(): string
    {
        $title = $this->context->getOption('newPageTitle');
        
        if ($title === null) {
            $translator = $this->cs()->translator;
            
            $fieldLabel = $translator->translateBe((string)($this->context->getConfig()['label'] ?? ''));
            $tableTitle = $translator->translateBe((string)($GLOBALS['TCA'][$this->context->getTableName()]['ctrl']['title'] ?? ''));
            $title = array_values(array_filter([$tableTitle, $fieldLabel]));
            if (empty($title)) {
                $title = [
                    Inflector::toHuman($this->context->getTableName()),
                    Inflector::toHuman($this->context->getFieldName()),
                ];
            }
            $title = 'Content elements of: ' . $title[0] . ' - {{uid}}';
        }
        
        return str_replace('{{uid}}', (string)$this->context->getRecordUid(), $title);
    }
}
