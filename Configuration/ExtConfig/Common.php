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
 * Last modified: 2021.07.26 at 15:28
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Configuration\ExtConfig;


use LaborDigital\T3ba\ExtConfig\ExtConfigContext;
use LaborDigital\T3ba\ExtConfigHandler\Fluid\ConfigureFluidInterface;
use LaborDigital\T3ba\ExtConfigHandler\Fluid\FluidConfigurator;
use LaborDigital\T3ba\ExtConfigHandler\Translation\ConfigureTranslationInterface;
use LaborDigital\T3ba\ExtConfigHandler\Translation\TranslationConfigurator;
use LaborDigital\T3ba\ExtConfigHandler\TypoScript\ConfigureTypoScriptInterface;
use LaborDigital\T3ba\ExtConfigHandler\TypoScript\TypoScriptConfigurator;

class Common implements ConfigureTranslationInterface, ConfigureTypoScriptInterface, ConfigureFluidInterface
{
    /**
     * @inheritDoc
     */
    public static function configureTranslation(TranslationConfigurator $configurator, ExtConfigContext $context): void
    {
        $configurator->registerNamespace('plfe');
    }
    
    /**
     * @inheritDoc
     */
    public static function configureTypoScript(TypoScriptConfigurator $configurator, ExtConfigContext $context): void
    {
        $configurator->registerPageTsConfigImport()
                     ->registerUserTsConfigImport();
    }
    
    /**
     * @inheritDoc
     */
    public static function configureFluid(FluidConfigurator $configurator, ExtConfigContext $context): void
    {
        $configurator->registerViewHelpers('plfe');
    }
}