<?php
/**
 * Copyright 2020 LABOR.digital
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
 * Last modified: 2020.06.30 at 16:44
 */

declare(strict_types=1);


namespace LaborDigital\Typo3PageLayoutFormElement\CoreModding;


use LaborDigital\Typo3PageLayoutFormElement\Domain\Table\Override\PagesOverride;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Controller\Page\BetterApiClassOverrideCopy__TreeController;

/**
 * Class ExtendedTreeController
 *
 * Extends the tree controller to allow us to remove all of our page layout pages from the page tree
 *
 * @package LaborDigital\Typo3PageLayoutFormElement\CoreModding
 */
class ExtendedTreeController extends BetterApiClassOverrideCopy__TreeController
{
    /**
     * @inheritDoc
     */
    public function fetchDataAction(ServerRequestInterface $request): ResponseInterface
    {
        return $this->applyDoktypeExclusionWrap(function () use ($request) {
            return parent::fetchDataAction($request);
        });
    }

    /**
     * @inheritDoc
     */
    public function filterDataAction(ServerRequestInterface $request): ResponseInterface
    {
        return $this->applyDoktypeExclusionWrap(function () use ($request) {
            return parent::filterDataAction($request);
        });
    }

    /**
     * Applies the additional doktype constraint
     *
     * @param   callable  $callback
     *
     * @return mixed
     */
    protected function applyDoktypeExclusionWrap(callable $callback)
    {
        // Hide our doktype in the page tree
        $backendUser = $this->getBackendUser();
        $tsBackup    = $backendUser->getTSConfig();

        // Rewrite the excluded doktypes
        $excludedDokTypes = $userTsConfig['options.']['pageTree.']['excludeDoktypes'] ?? '';
        $excludedDokTypes .= ',' . PagesOverride::PAGE_LAYOUT_DOK_TYPE;
        $excludedDokTypes = trim($excludedDokTypes, ',');
        $tsClone          = $tsBackup;

        $tsClone['options.']['pageTree.']['excludeDoktypes'] = $excludedDokTypes;
        BackendUserAdapter::setUserTs($backendUser, $tsClone);

        try {
            return $callback();
        } finally {
            // Restore the user ts
            BackendUserAdapter::setUserTs($backendUser, $tsBackup);
            unset($tsBackup, $tsClone);
        }
    }
}
