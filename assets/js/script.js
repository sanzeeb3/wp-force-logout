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
});
