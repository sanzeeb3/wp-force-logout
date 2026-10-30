/* global wpfl_pro_params */

(function () {
    'use strict';

    /* ------------------------------------
     * Helpers
     * ------------------------------------ */
    function postAjax(action) {
        const data = new FormData();
        data.append('action', action);
        data.append('security', wpfl_pro_params.nonce);

        return fetch(wpfl_pro_params.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: data
        });
    }

    /* ------------------------------------
     * Idle user logout
     *
     * Activity is shared between tabs through localStorage, so an idle background tab
     * doesn't log out a user who is working in another tab.
     * ------------------------------------ */
    if (wpfl_pro_params.idle_logout) {
        const STORAGE_KEY = 'wpflLastActivity';
        const timeout = parseInt(wpfl_pro_params.idle_timeout, 10); // Milliseconds.
        let lastActivity = 0;
        let idleTimer;

        function sharedLastActivity() {
            let shared = 0;
            try {
                shared = parseInt(window.localStorage.getItem(STORAGE_KEY), 10) || 0;
            } catch (e) {} // Storage may be blocked.

            return Math.max(lastActivity, shared);
        }

        function scheduleCheck() {
            clearTimeout(idleTimer);
            idleTimer = setTimeout(checkIdle, Math.max(1000, sharedLastActivity() + timeout - Date.now()));
        }

        function checkIdle() {
            if (Date.now() - sharedLastActivity() < timeout) {
                scheduleCheck();
                return;
            }

            postAjax('wp_force_logout_maybe_logout_idle_users').then(function (response) {
                if (response.ok) {
                    window.location.reload();
                }
            });
        }

        function markActivity() {
            const now = Date.now();

            // mousemove fires constantly; a second's precision is plenty.
            if (now - lastActivity < 1000) return;

            lastActivity = now;
            try {
                window.localStorage.setItem(STORAGE_KEY, String(now));
            } catch (e) {}
        }

        ['mousemove', 'keydown', 'scroll', 'click', 'touchstart'].forEach(function (event) {
            document.addEventListener(event, markActivity, { passive: true });
        });

        markActivity();
        scheduleCheck();
    }

    /* ------------------------------------
     * Auto logout on browser close
     *
     * The server logs out sessions that haven't been seen for a while, so open pages report
     * that the browser is still open. Closing the browser stops the pings.
     * ------------------------------------ */
    if (wpfl_pro_params.browser_close_logout) {
        setInterval(function () {
            postAjax('wp_force_logout_browser_ping').then(function (response) {
                // 400 "0": the session was logged out, e.g. the computer slept past the timeout.
                if (400 === response.status) {
                    window.location.reload();
                }
            }).catch(function () {}); // Offline, try again next time.
        }, parseInt(wpfl_pro_params.browser_ping, 10));
    }

})();
