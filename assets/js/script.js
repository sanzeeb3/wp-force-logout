/* global wpfl_plugins_params */

document.addEventListener('DOMContentLoaded', function() {

    // Review notice
    document.body.addEventListener('click', function(e) {
        if (e.target.closest('#wp-force-logout-review-notice .notice-dismiss')) {
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
                body: data
            })
            .then(response => response.text())
            .then(() => {
                // Success. Silence is golden.
            });
        }
    });

    // Settings
    const idleNode = document.getElementById('wp-force-logout-idle-logout');
    let nodeValue = idleNode ? idleNode.checked : false;

    idleLogoutChange(nodeValue);

    if (idleNode) {
        idleNode.addEventListener('change', function() {
            idleLogoutChange(idleNode.checked);
        });
    }

    function idleLogoutChange(value) {
        const nodes = document.querySelectorAll('.wp-force-logout-idle-logout-period');
        nodes.forEach(node => {
            node.style.display = value ? '' : 'none';
        });
    }

});


let timeout;

function wakeup_resetTimer(){
    clearTimeout(timeout);

    timeout = setTimeout(function(){
		var data = {
			action: 'wp_force_logout_maybe_logout_idle_users',
			security: wpfl_plugins_params.review_nonce,
		}

		jQuery.post( wpfl_plugins_params.ajax_url, data, function( response ) {
			// Success. Do nothing. Silence is golden.
		});
    }, wpfl_plugins_params.idle_user_timeout ); // timeouts
}

document.onmousemove = wakeup_resetTimer;
document.onkeypress = wakeup_resetTimer;

/**
 * The Auto Logout on Browser close functionality inside this block.
 *
 * @since 2.1.1
 */
document.addEventListener('DOMContentLoaded', (event) => {
	let inactivityTimer;
	let mouseLeftWindow = false;

	function startInactivityTimer() {
		clearTimeout(inactivityTimer);
		inactivityTimer = setTimeout(() => {
			if (mouseLeftWindow) {
				var data = {
					action: 'wp_force_logout_maybe_logout_on_browser_closure',
					security: wpfl_plugins_params.review_nonce,
				}
		
				jQuery.post( wpfl_plugins_params.ajax_url, data, function( response ) {
					// Success. Do nothing. Silence is golden.
				});
			}
		}, 120000); // 2 minutes.
	}

	function resetInactivityTimer() {
		clearTimeout(inactivityTimer);
	}

	document.addEventListener('mouseleave', function(event) {
		if (event.clientY <= 0) {
			mouseLeftWindow = true;
			startInactivityTimer();
		}
	});

	document.addEventListener('mousemove', function() {
		mouseLeftWindow = false;
		resetInactivityTimer();
	});

	document.addEventListener('keydown', function() {
		mouseLeftWindow = false;
		resetInactivityTimer();
	});

	document.addEventListener('scroll', function() {
		mouseLeftWindow = false;
		resetInactivityTimer();
	});
});

document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('wp-force-logout-browser-close-logout');

    if (!checkbox) return;

    checkbox.addEventListener('click', function (e) {
        if (checkbox.checked) {
            const confirmed = confirm(
                wpfl_plugins_params.heads_up_auto_logout
            );

            if (!confirmed) {
                e.preventDefault();
                checkbox.checked = false;
            }
        }
    });
});