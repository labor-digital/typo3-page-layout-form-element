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
 * Last modified: 2021.07.23 at 19:09
 */

define([
    'TYPO3/CMS/Backend/Modal',
    'TYPO3/CMS/Backend/Enum/Severity',
    'TYPO3/CMS/Backend/Severity'
], function (modal, sev, Severity) {
    sev = sev.SeverityEnum;
    return function (err, context) {
        err = err || 'Something went wrong!';
        console.error(err, context);
        modal.confirm('', err, sev.error, [
            {
                text: TYPO3.lang['button.ok'] || 'OK',
                btnClass: 'btn-' + Severity.getCssClass(sev.error),
                name: 'ok',
                trigger: function () {
                    modal.currentModal.trigger('modal-dismiss');
                }
            }
        ]);
    };
});