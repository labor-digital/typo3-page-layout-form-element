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
 * Last modified: 2021.07.23 at 19:08
 */

define([
    'jquery',
    'TYPO3/CMS/T3plfe/ErrorHandler'
], function (j, showError) {
    return function (renderId, renderName) {
        var $emptyGui = j('#' + renderId + '_empty');
        var $createButton = $emptyGui.find('*[data-action-button=create]');
        $createButton.click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            j.get($createButton.data('actionUrl'))
             .done(function (content) {
                 if (!content.data) {
                     return;
                 }
                
                 if (content.data.iframe) {
                     j('#' + renderId + '_container').html(content.data.iframe).css({display: 'block'});
                     $emptyGui.css({display: 'none'});
                 }
                
                 if (content.data.pid) {
                     j('input[name="' + renderName + '"]').val(content.data.pid);
                 }
             })
             .fail(function (err) {
                 var error = j(e.target).data('error-label') || 'Error:';
                 error += ' | ' + err.statusText + ' (' + err.status + ')';
                 showError(error, err);
             });
        });
    };
});