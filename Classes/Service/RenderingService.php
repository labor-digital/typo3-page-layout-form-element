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
 * Last modified: 2021.07.27 at 14:18
 */

declare(strict_types=1);


namespace LaborDigital\T3plfe\Service;


use LaborDigital\T3ba\Core\Di\ContainerAwareTrait;
use LaborDigital\T3ba\Core\Di\PublicServiceInterface;
use LaborDigital\T3ba\Tool\Translation\Translator;
use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;

class RenderingService implements PublicServiceInterface
{
    use ContainerAwareTrait;
    
    /**
     * @var \TYPO3\CMS\Core\Imaging\IconFactory
     */
    protected $iconFactory;
    
    /**
     * @var \LaborDigital\T3ba\Tool\Translation\Translator
     */
    protected $translator;
    
    public function __construct(IconFactory $iconFactory, Translator $translator)
    {
        $this->iconFactory = $iconFactory;
        $this->translator = $translator;
    }
    
    /**
     * Renders the "empty" UI which is shown, when there is no content page available for a record
     *
     * @param   string  $createLink  The ajax link to handle the creation of a content page
     *
     * @return string
     * @see \LaborDigital\T3plfe\Service\PageLayoutService to generate the links
     */
    public function renderEmptyUi(string $createLink): string
    {
        return $this->makeInstance(LinkButton::class)
                    ->setHref('#')
                    ->setDataAttributes([
                        'action-button' => 'create',
                        'action-url' => $createLink,
                        'error-label' => $this->translator->translateBe('plfe.error.pageCreationFailed'),
                    ])
                    ->setTitle($this->translator->translateBe('plfe.action.enablePage'))
                    ->setShowLabelText(true)
                    ->setIcon($this->iconFactory->getIcon('actions-toggle-off', Icon::SIZE_SMALL))
                    ->render();
    }
    
    /**
     * Small helper to create an iframe id from a field render id.
     * I implemented this, because the idee has to be generated multiple times, sadly.
     * Will be removed once I have a better solution for this.
     *
     * @param   string  $renderId
     *
     * @return string
     */
    public function makeIframeId(string $renderId): string
    {
        return $renderId . '_iframe';
    }
    
    /**
     * Renders the inline editing UI for the content page
     *
     * @param   string  $iframeLink      The link to show in the iframe
     * @param   string  $fullscreenLink  The link to navigate to, when "fullscreen" is clicked
     * @param   string  $deleteLink      The ajax link to handle the deletion of content pages
     * @param   string  $renderId        The unique id for the iframe {@see makeIframeId)
     *
     * @return string
     * @see \LaborDigital\T3plfe\Service\PageLayoutService to generate the links
     */
    public function renderIframe(string $iframeLink, string $fullscreenLink, string $deleteLink, string $renderId): string
    {
        $iframeId = $this->makeIframeId($renderId);
        
        $style = '<style>
#' . $iframeId . '{
    border: none;
    width: 100%;
    height: 400px;
    transition: 0.2s height;
    margin-top: 15px;
}
#' . $iframeId . '.iframe--expanded{
    height: 800px;
}
</style>';
        
        return $style . $this->renderActionButtons($fullscreenLink, $deleteLink, $renderId) .
               '<iframe name="' . $iframeId . '" id="' . $iframeId . '" src="' . $iframeLink . '"></iframe>';
    }
    
    /**
     * Helper to render the action buttons for the iframe UI
     *
     * @param   string  $fullscreenLink
     * @param   string  $deleteLink
     * @param   string  $renderId
     * @param   bool    $expandButton
     *
     * @return string
     */
    public function renderActionButtons(string $fullscreenLink, string $deleteLink, string $renderId, bool $expandButton = true): string
    {
        $buttons = [];
        
        $buttons[] = $this->makeInstance(LinkButton::class)
                          ->setHref('#')
                          ->setDataAttributes([
                              'action-button' => 'fullscreen',
                              'action-url' => $fullscreenLink,
                          ])
                          ->setTitle($this->translator->translateBe('plfe.action.fullscreen'))
                          ->setShowLabelText(true)
                          ->setIcon($this->iconFactory->getIcon('actions-window-open', Icon::SIZE_SMALL))
                          ->render();
        
        if ($expandButton) {
            $buttons[] = $this->makeInstance(LinkButton::class)
                              ->setHref('#')
                              ->setDataAttributes([
                                  'action-button' => 'expand',
                              ])
                              ->setTitle($this->translator->translateBe('plfe.action.toggleSize'))
                              ->setShowLabelText(true)
                              ->setIcon($this->iconFactory->getIcon('actions-expand', Icon::SIZE_SMALL))
                              ->render();
        }
        
        $buttons[] = $this->makeInstance(LinkButton::class)
                          ->setHref('#')
                          ->setDataAttributes([
                              'action-button' => 'delete',
                              'action-url' => $deleteLink,
                              'error-label' => $this->translator->translateBe('plfe.error.pageDeletionFailed'),
                          ])
                          ->setClasses('btn-danger')
                          ->setTitle($this->translator->translateBe('plfe.action.delete'))
                          ->setShowLabelText(true)
                          ->setIcon($this->iconFactory->getIcon('actions-delete', Icon::SIZE_SMALL))
                          ->render();
        
        return
            '<script type="text/javascript">
require(["TYPO3/CMS/T3plfe/IframeActions"], function(module){
module("' . $renderId . '");
});
</script>' .
            '<div class="btn-group btn-group-sm mb" role="group" id="' . $renderId . '_buttons">' .
            implode(PHP_EOL, $buttons) .
            '</div>';
    }
}