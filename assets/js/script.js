/* global wpfl_plugins_params
 */
jQuery( function( $ ) {

	// Review notice.
	jQuery('body').on('click', '#wp-force-logout-review-notice .notice-dismiss', function(e) {
	    e.preventDefault();

        jQuery("#wp-force-logout-review-notice").hide();

		var data = {
			action: 'wp_force_logout_dismiss_review_notice',
			security: wpfl_plugins_params.review_nonce,
			dismissed: true,
		};

		$.post( wpfl_plugins_params.ajax_url, data, function( response ) {
			// Success. Do nothing. Silence is golden.
    	});
	});

	$(window).on( 'beforeunload', function() {
		var data = {
			action: 'wp_force_logout_maybe_logout_on_browser_closure',
			security: wpfl_plugins_params.review_nonce,	
		}

		$.post( wpfl_plugins_params.ajax_url, data, function( response ) {
			// Success. Do nothing. Silence is golden.
		});
	});

	let idle_node = $('#wp-force-logout-idle-logout');
	let node_value = idle_node.is(':checked');

	idleLogoutChange( node_value );

	idle_node.on( 'change', function() {

		let node_value = $('#wp-force-logout-idle-logout').is(':checked');
		idleLogoutChange( node_value );
	});

	function idleLogoutChange( value ) {

		let node = $( '.wp-force-logout-idle-logout-period ');

		true === value ? node.show() : node.hide();
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
