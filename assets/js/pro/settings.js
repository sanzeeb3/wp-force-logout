/* global wpfl_settings_params */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        /* ------------------------------------
         * Show dependent rows only when their checkbox is checked
         * ------------------------------------ */
        document.querySelectorAll('tr[data-show-if]').forEach(function (row) {
            const checkbox = document.getElementById(row.dataset.showIf);
            if (!checkbox) return;

            function toggle() {
                row.style.display = checkbox.checked ? '' : 'none';
            }

            toggle();
            checkbox.addEventListener('change', toggle);
        });

        /* ------------------------------------
         * Browser close confirmation
         * ------------------------------------ */
        const closeCheckbox = document.getElementById('wp-force-logout-browser-close-logout');

        if (closeCheckbox) {
            closeCheckbox.addEventListener('click', function (e) {
                if (!closeCheckbox.checked) return;

                const confirmed = confirm(wpfl_settings_params.heads_up_auto_logout);
                if (!confirmed) {
                    e.preventDefault();
                    closeCheckbox.checked = false;
                }
            });
        }
    });

})();
