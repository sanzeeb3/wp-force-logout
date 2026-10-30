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
    });

})();
