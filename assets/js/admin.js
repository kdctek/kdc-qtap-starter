/**
 * qTap Starter Admin Scripts
 *
 * @package KDC_qTap_Starter
 * @since   1.0.0
 */

/* global jQuery, kdcQtapStarterAdmin */

( function( $ ) {
	'use strict';

	/**
	 * qTap Starter Admin Module
	 */
	const KDCQtapStarterAdmin = {

		/**
		 * Initialize the module.
		 */
		init: function() {
			this.bindEvents();
		},

		/**
		 * Bind event handlers.
		 */
		bindEvents: function() {
			// Example: Confirm before reset.
			$( '.kdc-qtap-starter-reset' ).on( 'click', this.confirmReset );

			// Example: Toggle dependent fields.
			$( '#kdc_qtap_starter_example_checkbox' ).on( 'change', this.toggleDependentFields );

			// Initialize dependent fields on page load.
			this.toggleDependentFields();
		},

		/**
		 * Confirm before resetting settings.
		 *
		 * @param {Event} e Click event.
		 * @return {boolean} Whether to proceed.
		 */
		confirmReset: function( e ) {
			// eslint-disable-next-line no-alert
			if ( ! window.confirm( kdcQtapStarterAdmin.i18n.confirmReset ) ) {
				e.preventDefault();
				return false;
			}
			return true;
		},

		/**
		 * Toggle dependent fields based on checkbox state.
		 */
		toggleDependentFields: function() {
			const isChecked = $( '#kdc_qtap_starter_example_checkbox' ).is( ':checked' );
			$( '.kdc-qtap-starter-dependent-field' ).toggle( isChecked );
		},

		/**
		 * Show a notification.
		 *
		 * @param {string} message Notification message.
		 * @param {string} type    Notification type: success, error, warning, info.
		 */
		showNotification: function( message, type ) {
			type = type || 'info';

			const $notice = $( '<div/>' )
				.addClass( 'kdc-qtap-starter-notice notice-' + type )
				.text( message );

			$( '.kdc-qtap-starter-settings h1' ).after( $notice );

			// Auto-dismiss after 5 seconds.
			setTimeout( function() {
				$notice.fadeOut( function() {
					$( this ).remove();
				} );
			}, 5000 );
		},

		/**
		 * Make an AJAX request.
		 *
		 * @param {string}   action   AJAX action name.
		 * @param {Object}   data     Data to send.
		 * @param {Function} callback Callback function.
		 */
		ajax: function( action, data, callback ) {
			data = data || {};
			data.action = action;
			data.nonce = kdcQtapStarterAdmin.nonce;

			$.ajax( {
				url: kdcQtapStarterAdmin.ajaxUrl,
				type: 'POST',
				data: data,
				success: function( response ) {
					if ( typeof callback === 'function' ) {
						callback( response );
					}
				},
				error: function( xhr, status, error ) {
					// eslint-disable-next-line no-console
					console.error( 'AJAX Error:', error );
					KDCQtapStarterAdmin.showNotification(
						kdcQtapStarterAdmin.i18n.error,
						'error'
					);
				},
			} );
		},
	};

	// Initialize when DOM is ready.
	$( function() {
		KDCQtapStarterAdmin.init();
	} );

}( jQuery ) );
