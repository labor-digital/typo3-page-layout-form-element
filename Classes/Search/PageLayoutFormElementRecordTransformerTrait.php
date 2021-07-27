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


use LaborDigital\T3ba\Core\Exception\NotImplementedException;

/**
 * Trait PageLayoutFormElementSearchTransformerTrait
 *
 * This trait can be used in SearchAndIndex data providers to make the indexing of our page layout elements as easy as
 * possible
 *
 * @package LaborDigital\Typo3PageLayoutFormElement\Search
 */
trait PageLayoutFormElementRecordTransformerTrait
{

//    /**
//     * The instance of the page content generator to convert the content element's into a single string
//     *
//     * @var PageContentGenerator
//     */
//    protected $pageContentGenerator;
//
//    /**
//     * Inject the instance of the page content generator
//     *
//     * @param   \LaborDigital\T3SAI\Builtin\Transformer\Page\PageContentGenerator  $pageContentGenerator
//     */
//    public function injectPageContentGenerator(PageContentGenerator $pageContentGenerator): void
//    {
//        $this->pageContentGenerator = $pageContentGenerator;
//    }
    
    /**
     * This method can be used to find the search indexer content of a page layout field.
     * It receives the field name / the PageLayoutContent content object and will return the whole content as a string.
     * While converting the layout to a string, the method will respect the registered content element transformers.
     *
     * @param   int|string|\LaborDigital\T3plfe\Domain\Model\PageLayout  $field    The value of the pageLayout field, or the instance of a
     *                                                                             PageLayoutContent element
     * @param   IndexerContext                                           $context  The indexer context to read the language from
     *
     * @return string
     */
    public function getPageLayoutContent($field, object $context): string
    {
        throw new NotImplementedException('This feature is not yet implemented');
//        // Make sure we have the content element to work with
//        if (! $field instanceof PageLayoutContent) {
//            $field = TypoContainer::getInstance()->get(PageLayoutContent::class, [
//                "args" => [
//                    $context->getLanguage()->getLanguageId(),
//                    $field,
//                ],
//            ]);
//        }
//
//        return $this->pageContentGenerator->getContent($field->getPageUid(), time(), $context);
    }
}
