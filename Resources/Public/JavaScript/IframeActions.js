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
 * Last modified: 2021.07.23 at 19:39
 */

define([
    'jquery',
    'TYPO3/CMS/Backend/Modal',
    'TYPO3/CMS/Backend/FormEngine',
    'TYPO3/CMS/T3plfe/ErrorHandler',
    'TYPO3/CMS/Backend/Utility/MessageUtility'
], function (j, modal, formEngine, showError, messageUtility) {
    return function (renderId, renderName) {
        var iframe = j('#' + renderId + '_iframe')[0] || null;
        
        // We need to proxy all global messages into our iframe in order for inline file references to work correctly
        if (iframe.contentWindow) {
            window.addEventListener('message', function (m) {
                messageUtility.MessageUtility.send(m.data, iframe.contentWindow);
            });
        }
        
        if (document.editform) {
            var submittable = true;
            if (iframe) {
                var $eForm = j(document.editform);
                
                function childHasChanges()
                {
                    return iframe &&
                        iframe.contentWindow &&
                        iframe.contentWindow.document.editform &&
                        iframe.contentWindow.TYPO3.FormEngine &&
                        iframe.contentWindow.TYPO3.FormEngine.hasChange();
                }
                
                // Prevent closing the form if child record has changes
                const hasChangeOrg = formEngine.hasChange;
                formEngine.hasChange = function () {
                    return hasChangeOrg() || childHasChanges();
                };
                
                // Save the child form if the parent record is saved
                $eForm.submit(function (e) {
                    if (childHasChanges()) {
                        submittable = false;
                        
                        iframe.contentWindow.onunload = function () {
                            clearTimeout(window.t3plfCloseTimeout);
                            window.t3plfCloseTimeout = setTimeout(function () {
                                submittable = true;
                                $eForm.submit();
                            }, 500);
                        };
                        iframe.contentWindow.TYPO3.FormEngine.saveDocument();
                    }
                    
                    if (!submittable) {
                        e.preventDefault();
                    }
                });
            }
        }
        
        var $buttons = j('#' + renderId + '_buttons');
        var $fullscreenButton = $buttons.find('*[data-action-button=fullscreen]');
        $fullscreenButton.click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            formEngine.preventFollowLinkIfNotSaved($fullscreenButton.data('actionUrl'));
            
            // This is a bugfix because preventFollowLinkIfNotSaved does not work as
            // expected when the "save" button is clicked -> it redirects to the parent folder instead
            if (modal.currentModal) {
                modal.currentModal.off('button.clicked');
                modal.currentModal.on('button.clicked', function (e) {
                    if (e.target.name === 'no') {
                        modal.dismiss();
                    } else if (e.target.name === 'yes') {
                        modal.dismiss();
                        window.location.href = $fullscreenButton.data('actionUrl');
                    } else if (e.target.name === 'save') {
                        document.editform.returnUrl.value = $fullscreenButton.data('actionUrl');
                        $('form[name=' + formEngine.formName + ']').append(
                            $('<input />').attr('type', 'hidden').attr('name', '_saveandclosedok').attr('value', '1')
                        );
                        modal.dismiss();
                        formEngine.saveDocument();
                    }
                });
            }
        });
        
        var $expandButton = $buttons.find('*[data-action-button=expand]');
        $expandButton.click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            j('#' + renderId + '_iframe').toggleClass('iframe--expanded');
        });
        
        var $deleteButton = $buttons.find('*[data-action-button=delete]');
        $deleteButton.click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            formEngine.showDeleteModal(j(e.target), function (type) {
                modal.dismiss();
                if (type !== 'yes') {
                    return;
                }
                
                j('#' + renderId + '_empty').css({display: 'block'});
                j('#' + renderId + '_container').html('').css({display: 'none'});
                j('input[name="' + renderName + '"]').val(0);
                j.get($deleteButton.data('actionUrl'))
                 .fail(function (err) {
                     var error = j(e.target).data('error-label') || 'Error:';
                     error += ' | ' + err.statusText + ' (' + err.status + ')';
                     showError(error, err);
                 });
            });
        });
    };
});