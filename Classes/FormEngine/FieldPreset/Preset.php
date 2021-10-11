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
 * Last modified: 2021.07.27 at 10:49
 */

declare(strict_types=1);

namespace LaborDigital\T3plfe\FormEngine\FieldPreset;

use LaborDigital\T3ba\Tool\FormEngine\Custom\Field\CustomFieldPresetTrait;
use LaborDigital\T3ba\Tool\Tca\Builder\FieldPreset\AbstractFieldPreset;
use LaborDigital\T3plfe\FormEngine\Field\PageLayoutFormElement;
use Neunerlei\Options\Options;

class Preset extends AbstractFieldPreset
{
    use CustomFieldPresetTrait;
    
    /**
     * Adds a new field with a "virtual" page behind it.
     * You can create new contents and manage them like you would on a normal page.
     *
     * The page will not show up in your frontend tho!
     *
     * @param   array  $options  Additional config options
     *                           - storagePid int: If set can be used to move the virtual pages to another storage
     *                           directory. By default the pages will be created in the same directory / page as your
     *                           current record. Can be useful if you have to use a certain typoScript config for your
     *                           content elements.
     *                           - respectUserPermissions bool (FALSE): By default the permissions to create virtual
     *                           pages are tied to the fact that a user can edit this field or not. If you additionally
     *                           want to check if the user has permissions to edit/remove/move the virtual page you can
     *                           set this to true.
     *                           - addToPageRow array: Can be used to pass additional field values to newly created
     *                           page rows that are created when missing.
     *                           - translationMode string (copyToLanguage): Possible values are copyToLanguage|localize
     *                           Defines how page translations should be handled. They match their values in the
     *                           TYPO3 data handler. "copyToLanguage" copies all data as "free mode",
     *                           "localize" translates the records in strict mode, where a element in the main language
     *                           is always represented by an element in the translation.
     *                           - noIframe bool (FALSE): If the flag is set to true, the preview iframe will not be
     *                           rendered at all.
     *
     * @see https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/Typo3CoreEngine/Database/Index.html#command-keywords-and-values
     */
    public function applyPageLayout(array $options = []): void
    {
        $options = Options::make($options, [
            'storagePid' => [
                'type' => 'int',
                'default' => -1,
            ],
            'respectUserPermissions' => [
                'type' => 'bool',
                'default' => false,
            ],
            'noIframe' => [
                'type' => 'bool',
                'default' => false,
            ],
            'addToPageRow' => [
                'type' => 'array',
                'default' => [],
            ],
            'translationMode' => [
                'type' => 'string',
                'default' => 'copyToLanguage',
                'values' => ['copyToLanguage', 'localize'],
            ],
        ]);
        
        $this->applyCustomElementPreset(PageLayoutFormElement::class, $options);
        
        $this->field->addConfig([
            'type' => 'select',
            'localizeReferencesAtParentLocalization' => true,
            'foreign_table' => 'pages',
            'items' => [['', 0]],
        ]);
        
        $this->field->setDefault(0);
        
        if (empty($this->field->getDescription())) {
            $this->field->setDescription('plfe.fieldDescription');
        }
    }
}
