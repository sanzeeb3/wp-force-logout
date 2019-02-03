/* global wpfl_plugins_params
 *
 * Modal is adapted from w3schools.
 *
 * @link https://www.w3schools.com/howto/howto_css_modals.asp
*/
jQuery(document).ready(function( $ ){

	  // Deactivation feedback.
 	$( document.body ).on( 'click' ,'tr[data-plugin="wp-force-logout/wp-force-logout.php"] span.deactivate a', function( e ) {

		e.preventDefault();

		var data = {
			action: 'wp_force_logout_deactivation_notice',
			security: wpfl_plugins_params.deactivation_nonce
		};

		$.post( wpfl_plugins_params.ajax_url, data, function( response ) {
			jQuery('#wpbody-content .wrap').append( response );
			var modal = document.getElementById('wp-force-logout-modal');

	  		// Open the modal.
	  		modal.style.display = "block";

	  		// On click on send email button on the modal.
		    $("#wpfl-send-deactivation-email").click( function( e ) {
		    	e.preventDefault();

		    	this.value 		= wpfl_plugins_params.deactivating;
		    	var form 		= $("#wp-force-logout-send-deactivation-email");

				var message		= form.find( ".row .col-75 textarea#message" ).val();
				var nonce 		= form.find( ".row #wp_force_logout_send_deactivation_email").val();

				var data = {
					action: 'wp_force_logout_send_deactivation_email',
					security: nonce,
					message: message,
				}

				$.post( wpfl_plugins_params.ajax_url, data, function( response ) {

					if( response.success === false ) {
						swal( wpfl_plugins_params.error, response.data.message, "error" );
					} else {
						swal( {title: wpfl_plugins_params.deactivated, text: wpfl_plugins_params.sad_to_see, icon: "success", allowOutsideClick: false, closeOnClickOutside: false });
						$('.swal-button--confirm').click( function (e) {
							location.reload();
						});
					}

					modal.remove();
				}).fail( function( xhr ) {
					swal( wpfl_plugins_params.error, wpfl_plugins_params.wrong, "error" );
				});

		    });

		}).fail( function( xhr ) {
			window.console.log( xhr.responseText );
		});
   });
});
