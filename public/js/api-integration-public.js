(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).ready(function() {
		$('#user-settings-form').on('submit', function(event) {
			event.preventDefault();
			
			var user_preferences = $('#user_preferences').val();
            var user_id = $('#user_id').val();

            var userData = {
                user_preferences: user_preferences,
                user_id: user_id,
				nonce: wpApiSettings.nonce,
			};

			if( user_preferences != '' && user_id != '' ) {
				var ajax_running = 0;
				ajax_running++;
				if(ajax_running == 1) {
					$('#loading').show();
	    			$('body').addClass('bodyoverlay');
					$.ajax({
						url: wpApiSettings.root + 'customapi/v1/userdata',
						type: 'POST',
						data: JSON.stringify(userData),
						contentType: 'application/json',
						dataType: 'json', 
						beforeSend: function ( xhr ) { 
      						xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce ); 
						},
						success: function(response) {
							$('#loading').hide();
	          				$('body').removeClass('bodyoverlay');
							$('.api-message').html('<p>' + response.message + '</p>');
							if (response.user_preferences && response.user_preferences.length > 0) {
								var preferencesList = '';
								response.user_preferences.forEach(function(pref) {
									preferencesList += '<li>' + pref + '</li>';
								});
								if($('.userdata ul').length > 0) {
									$('.userdata ul').html(preferencesList);
								} else {
									$('.user-tab-content').append('<div class="userdata"><h3>User Preferences</h3><ul>'+preferencesList+'</ul></div>');
								}
							} else {
								$('.userdata ul').html('No user preferences available.');
							}
							$('.error').hide();
							$('#user_preferences').val('');
							ajax_running = 0;
						},
						error: function(xhr, status, error) {
							console.error('API Request Error: ' + error);
						}
					});
				}
			} else {
				$('.error').text('This field is required');
			}
		});
	});
})( jQuery );
