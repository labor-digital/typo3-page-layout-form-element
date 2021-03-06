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
 * Last modified: 2019.08.20 at 12:54
 */

namespace LaborDigital\Typo3PageLayoutFormElement\Controller\Resource;


use LaborDigital\Typo3FrontendApi\JsonApi\Builtin\Resource\Entity\ContentElementColumnList;
use LaborDigital\Typo3FrontendApi\JsonApi\Transformation\AbstractResourceTransformer;

class FormPageLayoutContentResourceTransformer extends AbstractResourceTransformer
{
    /**
     * @inheritDoc
     */
    protected function transformValue($value): array
    {
        /** @var \LaborDigital\Typo3PageLayoutFormElement\Domain\Model\PageLayoutContent $value */
        // Done
        return [
            'id'       => $value->getPageUid(),
            'children' => $this->autoTransform(
                $this->getInstanceOf(ContentElementColumnList::class, [
                    $value->getPageUid(),
                    $value->getContents(),
                    $this->FrontendApiContext()->getLanguageCode(),
                ])
            ),
        ];
    }
}
