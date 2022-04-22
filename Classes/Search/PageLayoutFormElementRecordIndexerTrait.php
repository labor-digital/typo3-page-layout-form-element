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
 * Last modified: 2021.07.27 at 09:33
 */

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
 * Last modified: 2019.07.14 at 15:23
 */

namespace LaborDigital\T3plfe\Search;


use LaborDigital\T3plfe\Domain\Model\PageLayout;
use LaborDigital\T3sai\Core\Indexer\Queue\QueueRequest;
use LaborDigital\T3sai\Search\Indexer\Page\ContentElement\PageContentProcessor;
use LaborDigital\T3sai\Search\Indexer\Page\PageContent\PageContentResolver;

/**
 * Trait PageLayoutFormElementSearchTransformerTrait
 *
 * This trait can be used in SearchAndIndex data providers to make the indexing of our page layout elements as easy as
 * possible
 *
 * @package LaborDigital\Typo3PageLayoutFormElement\Search
 */
trait PageLayoutFormElementRecordIndexerTrait
{
    
    /**
     * @var \LaborDigital\T3sai\Search\Indexer\Page\PageContent\PageContentResolver
     */
    protected $contentResolver;
    
    /**
     * @var \LaborDigital\T3sai\Search\Indexer\Page\ContentElement\PageContentProcessor
     */
    protected $pageContentProcessor;
    
    public function injectPageContentResolver(PageContentResolver $contentResolver): void
    {
        $this->contentResolver = $contentResolver;
    }
    
    public function injectPageContentProcessor(PageContentProcessor $PageContentProcessor): void
    {
        $this->pageContentProcessor = $PageContentProcessor;
    }
    
    /**
     * This method can be used to find the search indexer content of a page layout field.
     * It receives the field name / the PageLayoutContent content object and will return the whole content as a string.
     * While converting the layout to a string, the method will respect the registered content element transformers.
     *
     * @param   int|string|PageLayout  $layout   The value of the pageLayout field, or the instance of a PageLayoutContent element
     * @param   QueueRequest           $request  The indexer context to read the language from
     *
     * @return array
     */
    public function getPageLayoutContent($layout, QueueRequest $request): array
    {
        if ($layout instanceof PageLayout) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $layout = $layout->getPid() ?? $layout->getUid();
        }
        
        if (empty($layout)) {
            return [];
        }
        
        $layout = (int)$layout;
        
        return $this->pageContentProcessor->generateContent(
            $this->contentResolver->makeContentIterator($layout),
            $request
        );
    }
}
