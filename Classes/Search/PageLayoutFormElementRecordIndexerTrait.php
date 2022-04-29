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
use LaborDigital\T3sai\Core\Indexer\Node\Node;
use LaborDigital\T3sai\Core\Indexer\Queue\QueueRequest;
use LaborDigital\T3sai\Search\Indexer\Page\PageContent\PageContentIndexer;
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
     * @var \LaborDigital\T3sai\Search\Indexer\Page\PageContent\PageContentIndexer
     */
    protected $contentIndexer;
    
    public function injectPageContentResolver(PageContentResolver $contentResolver): void
    {
        $this->contentResolver = $contentResolver;
    }
    
    public function injectPageContentProcessor(PageContentIndexer $contentIndexer): void
    {
        $this->contentIndexer = $contentIndexer;
    }
    
    /**
     * This method can be used to find the search indexer content of a page layout field.
     * It receives the field name / the PageLayoutContent content object and automatically append it to the provided node
     * While resolving the content, the method will respect the registered content element transformers.
     *
     * @param   int|string|PageLayout  $layout   The value of the pageLayout field, or the instance of a PageLayoutContent element
     * @param   Node                   $node     The node to apply the contents to
     * @param   QueueRequest|null      $request  Optional request object to replace that of the node
     *
     * @return void
     */
    public function addPageLayoutContentToNode($layout, Node $node, ?QueueRequest $request = null): void
    {
        if ($layout instanceof PageLayout) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $layout = $layout->getPid() ?? $layout->getUid();
        }
        
        if (empty($layout)) {
            return;
        }
        
        $this->contentIndexer->index(
            $this->contentResolver->makeContentIterator((int)$layout),
            $node,
            $request ?? $node->getRequest()
        );
    }
}
