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
 * Last modified: 2021.07.27 at 09:36
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
 * Last modified: 2019.07.15 at 00:05
 */

namespace LaborDigital\T3plfe\ViewHelpers;

use LaborDigital\T3ba\Tool\Cache\Page\PageCacheTaggerAwareInterface;
use LaborDigital\T3ba\Tool\Cache\Page\PageCacheTaggerAwareTrait;
use LaborDigital\T3plfe\Domain\Model\PageLayout;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ContentPageViewHelper extends AbstractViewHelper implements PageCacheTaggerAwareInterface
{
    use PageCacheTaggerAwareTrait;
    
    protected $escapeOutput = false;
    protected $escapeChildren = false;
    
    /**
     * @inheritDoc
     */
    public function initializeArguments()
    {
        $this->registerArgument('value', PageLayout::class,
            'The content of the page layout form element to render', false);
    }
    
    /**
     * Render the contents of the linked page layout and be done
     *
     * @return string
     */
    public function render()
    {
        $value = $this->arguments['value'] ?? $this->renderChildren();
        
        if ($value instanceof PageLayout) {
            $this->getPageCacheTagger()->addTag($value);
            
            return $value->render();
        }
        
        return '';
    }
    
}
