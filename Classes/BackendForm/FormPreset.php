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
 * Last modified: 2019.07.09 at 21:19
 */

namespace LaborDigital\Typo3PageLayoutFormElement\BackendForm;


use LaborDigital\Typo3BetterApi\BackendForms\CustomElements\CustomElementPresetTrait;
use LaborDigital\Typo3BetterApi\BackendForms\FormPresets\AbstractFormPreset;
use Neunerlei\Options\Options;

class FormPreset extends AbstractFormPreset
{
    use CustomElementPresetTrait;
    
    /**
     * Adds a new field with a "virtual" page behind it.
     * You can create new contents and manage them like you would on a normal page.
     *
     * The page will not show up in your frontend tho!
     *
     * @param   array  $options  Additional config options
     *                           - storagePid int: If set can be used to move the virtual pages to another storage
     *                           directory. By default the pages will be created in the same directory / page as your
     *                           current record. Can be useful if you have to use a certain typoscript config for your
     *                           content elements.
     *                           - respectUserPermissions bool (FALSE): By default the permissions to create virtual
     *                           pages are tied to the fact that a user can edit this field or not. If you additionally
     *                           want to check if the user has permissions to edit/remove/move the virtual page you can
     *                           set this to true.
     *                           - addToPageRow array: Can be used to pass additional field values to newly created
     *                           page
     *                           rows that are created when missing.
     */
    public function pageLayout(array $options = [])
    {
        // Prepare options
        $options = Options::make($options, [
            "storagePid"             => [
                "type"    => "int",
                "default" => -1,
            ],
            "respectUserPermissions" => [
                "type"    => "bool",
                "default" => false,
            ],
            "addToPageRow"           => [
                "type"    => "array",
                "default" => [],
            ],
        ]);
        
        // Register our custom element
        $this->applyCustomElementPreset($this->field, $this->context, PageLayoutFormElement::class, $options);
        
        // Add sql definition
        $this->setSqlDefinitionForTcaField("int(11) DEFAULT '0' NOT NULL");
        
        // Make a relation field out of it
        $this->field->addConfig([
            "type"          => "select",
            "foreign_table" => "pages",
            "items"         => [["", 0]],
        ]);
    }
}
