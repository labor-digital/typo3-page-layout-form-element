<?php
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
 * Last modified: 2019.07.05 at 13:05
 */

$EM_CONF[$_EXTKEY] = [
    'title'            => 'Page-Layout Form-Element',
    'description'      => 'A Typo3 Extension which allows to create backend form elements that allow a page layout for their content',
    'author'           => 'Martin Neundorfer',
    'author_email'     => 'm.neundorfer@labor.digital',
    'category'         => 'misc',
    'author_company'   => 'LABOR.digital',
    'shy'              => '',
    'conflicts'        => '',
    'priority'         => '',
    'module'           => '',
    'state'            => 'stable',
    'internal'         => '',
    'uploadfolder'     => 0,
    'createDirs'       => '',
    'modify_tables'    => '',
    'clearCacheOnLoad' => 1,
    'lockType'         => '',
    'version'          => '9.0.0',
    'constraints'      => [
        'depends'   => [
            'typo3'            => '9.0.0-9.99.99',
            'typo3_better_api' => '*',
        ],
        'conflicts' => [
        ],
        'suggests'  => [
        ],
    ],
    'suggests'         => [
    
    ],
];
