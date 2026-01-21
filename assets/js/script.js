/* global wpfl_plugins_params */

(function () {
    'use strict';

    /* ------------------------------------
     * Helpers
     * ------------------------------------ */
    function postAjax(action, extraData = {}) {
        const data = new FormData();
        data.append('action', action);
        data.append('security', wpfl_plugins_params.review_nonce);

        Object.keys(extraData).forEach(key => {
            data.append(key, extraData[key]);
        });

        return fetch(wpfl_plugins_params.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: data
        });
    }

    /* ------------------------------------
     * DOM Ready
     * ------------------------------------ */
    document.addEventListener('DOMContentLoaded', function () {

        /* ------------------------------------
         * Review notice dismiss
         * ------------------------------------ */
        document.body.addEventListener('click', function (e) {
            const dismissBtn = e.target.closest('#wp-force-logout-review-notice .notice-dismiss');
            if (!dismissBtn) return;

            e.preventDefault();

            const notice = document.getElementById('wp-force-logout-review-notice');
            if (notice) {
                notice.style.display = 'none';
            }

            postAjax('wp_force_logout_dismiss_review_notice', {
                dismissed: true
            });
        });

        /* ------------------------------------
         * Idle logout settings toggle
         * ------------------------------------ */
        const idleCheckbox = document.getElementById('wp-force-logout-idle-logout');

        function toggleIdleFields(enabled) {
            document
                .querySelectorAll('.wp-force-logout-idle-logout-period')
                .forEach(node => {
                    node.style.display = enabled ? '' : 'none';
                });
        }

        if (idleCheckbox) {
            toggleIdleFields(idleCheckbox.checked);

            idleCheckbox.addEventListener('change', function () {
                toggleIdleFields(idleCheckbox.checked);
            });
        }

        /* ------------------------------------
         * Browser close confirmation
         * ------------------------------------ */
        const closeCheckbox = document.getElementById('wp-force-logout-browser-close-logout');

        if (closeCheckbox) {
            closeCheckbox.addEventListener('click', function (e) {
                if (!closeCheckbox.checked) return;

                const confirmed = confirm(wpfl_plugins_params.heads_up_auto_logout);
                if (!confirmed) {
                    e.preventDefault();
                    closeCheckbox.checked = false;
                }
            });
        }
    });

    /* ------------------------------------
     * Idle user logout
     * ------------------------------------ */
    let idleTimeout;

    function resetIdleTimer() {
        clearTimeout(idleTimeout);

        idleTimeout = setTimeout(function () {
            postAjax('wp_force_logout_maybe_logout_idle_users');
        }, wpfl_plugins_params.idle_user_timeout); // MUST be milliseconds
    }

    ['mousemove', 'keydown', 'scroll', 'click', 'touchstart'].forEach(event => {
        document.addEventListener(event, resetIdleTimer, { passive: true });
    });

    resetIdleTimer();

    /* ------------------------------------
     * Auto logout on browser close (mouse leave)
     * ------------------------------------ */
    let inactivityTimer;
    let mouseLeftWindow = false;

    function startInactivityTimer() {
        clearTimeout(inactivityTimer);

        inactivityTimer = setTimeout(function () {
            if (!mouseLeftWindow) return;

            postAjax('wp_force_logout_maybe_logout_on_browser_closure');
        }, 120000); // 2 minutes
    }

    function resetInactivityTimer() {
        clearTimeout(inactivityTimer);
    }

    document.addEventListener('mouseleave', function (event) {
        if (event.clientY <= 0) {
            mouseLeftWindow = true;
            startInactivityTimer();
        }
    });

    ['mousemove', 'keydown', 'scroll'].forEach(event => {
        document.addEventListener(event, function () {
            mouseLeftWindow = false;
            resetInactivityTimer();
        });
    });

})();
