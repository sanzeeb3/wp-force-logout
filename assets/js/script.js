/* global wpfl_plugins_params */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        /* ------------------------------------
         * Upgrade notice dismiss
         * ------------------------------------ */
        document.body.addEventListener('click', function (e) {
            const dismissBtn = e.target.closest('#wp-force-logout-review-notice .notice-dismiss');
            if (!dismissBtn) return;

            e.preventDefault();

            const notice = document.getElementById('wp-force-logout-review-notice');
            if (notice) {
                notice.style.display = 'none';
            }

            const data = new FormData();
            data.append('action', 'wp_force_logout_dismiss_review_notice');
            data.append('security', wpfl_plugins_params.review_nonce);
            data.append('dismissed', true);

            fetch(wpfl_plugins_params.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: data
            });
        });
    });

})();
