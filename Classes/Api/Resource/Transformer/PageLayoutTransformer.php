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
 * Last modified: 2021.07.27 at 09:25
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Api\Resource\Transformer;


use LaborDigital\T3ba\Tool\Simulation\EnvironmentSimulator;
use LaborDigital\T3fa\Api\Resource\Factory\PageContent\PageContentResourceFactory;
use LaborDigital\T3fa\Core\Resource\Transformer\AbstractResourceTransformer;
use LaborDigital\T3fa\Core\Resource\Transformer\AutoMagic\AutoMagicTransformerTrait;

class PageLayoutTransformer extends AbstractResourceTransformer
{
    use AutoMagicTransformerTrait;
    
    /**
     * @var \LaborDigital\T3fa\Api\Resource\Factory\PageContent\PageContentResourceFactory
     */
    protected $factory;
    /**
     * @var \LaborDigital\T3ba\Tool\Simulation\EnvironmentSimulator
     */
    protected $simulator;
    
    public function __construct(PageContentResourceFactory $factory, EnvironmentSimulator $simulator)
    {
        $this->factory = $factory;
        $this->simulator = $simulator;
    }
    
    /**
     * @inheritDoc
     */
    public function transform($value): array
    {
        /** @var \LaborDigital\T3plfe\Domain\Model\PageLayout $value */
        if ($value->getUid() === null) {
            return [
                'id' => null,
            ];
        }
        
        $content = $this->simulator->runWithEnvironment(['includeHiddenPages'], function () use ($value) {
            return $this->factory->make(
                $value->getUid(),
                $value->getLanguage(),
                $value->getSite()
            );
        });
        
        return $content->asArray();
    }
    
}