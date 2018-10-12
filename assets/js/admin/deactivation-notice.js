/* global wpfl_plugins_params */
jQuery( function( $ ) {
   $( document.body ).on( 'click' ,'tr[data-plugin="wp-force-logout/wp-force-logout.php"] span.deactivate a', function( e ) {
		e.preventDefault();

		var data = {
			action: 'wpforce_logout_deactivation_notice',
			security: wpfl_plugins_params.deactivation_nonce
		};

		$.post( wpfl_plugins_params.ajax_url, data, function( response ) {
			$( 'tr[data-plugin="wp-force-logout/wp-force-logout.php"] span.deactivate a' ).addClass( 'hasNotice' );

			if( $( 'tr[id="wp-force-logout-license-row"]' ).length !== 0 ) {
				$( 'tr[id="wp-force-logout-license-row"]' ).addClass( 'update wp-force-logout-deactivation-notice' ).after( response  );
			} else {
				$( 'tr[data-plugin="wp-force-logout/wp-force-logout.php"]' ).addClass( 'update wp-force-logout-deactivation-notice' ).after( response  );
			}
		}).fail( function( xhr ) {
			window.console.log( xhr.responseText );
		});
   });
});
