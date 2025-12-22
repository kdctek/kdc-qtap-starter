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
			this.initDeleteWarning();
		},

		/**
		 * Bind event handlers.
		 */
		bindEvents: function() {
			// Delete data checkbox toggle.
			$( '#kdc_qtap_starter_delete_data' ).on( 'change', this.toggleDeleteWarning );

			// Copy to clipboard.
			$( '#kdc-qtap-starter-copy-export' ).on( 'click', this.copyToClipboard );

			// Import confirmation.
			$( '#kdc-qtap-starter-import-btn' ).on( 'click', this.confirmImport );

			// Reset confirmation.
			$( '.kdc-qtap-starter-reset' ).on( 'click', this.confirmReset );
		},

		/**
		 * Initialize delete warning visibility.
		 */
		initDeleteWarning: function() {
			const $checkbox = $( '#kdc_qtap_starter_delete_data' );
			const $warning = $( '#kdc-qtap-starter-delete-warning' );

			if ( $checkbox.length && $warning.length ) {
				if ( $checkbox.is( ':checked' ) ) {
					$warning.show();
				} else {
					$warning.hide();
				}
			}
		},

		/**
		 * Toggle delete warning visibility.
		 *
		 * @param {Event} e Change event.
		 */
		toggleDeleteWarning: function( e ) {
			const $checkbox = $( this );
			const $warning = $( '#kdc-qtap-starter-delete-warning' );

			if ( $checkbox.is( ':checked' ) ) {
				// Show warning with confirmation.
				// eslint-disable-next-line no-alert
				if ( ! window.confirm( kdcQtapStarterAdmin.i18n.confirmDelete ) ) {
					e.preventDefault();
					$checkbox.prop( 'checked', false );
					return;
				}

				// Prompt to export first.
				// eslint-disable-next-line no-alert
				if ( ! window.confirm( kdcQtapStarterAdmin.i18n.exportFirst ) ) {
					e.preventDefault();
					$checkbox.prop( 'checked', false );
					return;
				}

				$warning.slideDown( 300 );
			} else {
				$warning.slideUp( 300 );
			}
		},

		/**
		 * Copy export data to clipboard.
		 *
		 * @param {Event} e Click event.
		 */
		copyToClipboard: function( e ) {
			e.preventDefault();

			const $textarea = $( '#kdc-qtap-starter-export-data' );
			const $button = $( this );

			if ( ! $textarea.length ) {
				return;
			}

			// Get the text content.
			const text = $textarea.val();

			// Use modern clipboard API if available.
			if ( navigator.clipboard && window.isSecureContext ) {
				navigator.clipboard.writeText( text ).then( function() {
					KDCQtapStarterAdmin.showCopiedFeedback( $button );
				} ).catch( function() {
					KDCQtapStarterAdmin.fallbackCopy( $textarea );
				} );
			} else {
				KDCQtapStarterAdmin.fallbackCopy( $textarea );
			}
		},

		/**
		 * Fallback copy method for older browsers.
		 *
		 * @param {jQuery} $textarea The textarea element.
		 */
		fallbackCopy: function( $textarea ) {
			$textarea.show().select();
			document.execCommand( 'copy' );
			$textarea.hide();
			KDCQtapStarterAdmin.showCopiedFeedback( $( '#kdc-qtap-starter-copy-export' ) );
		},

		/**
		 * Show copied feedback on button.
		 *
		 * @param {jQuery} $button The button element.
		 */
		showCopiedFeedback: function( $button ) {
			const originalText = $button.html();

			$button.html(
				'<span class="dashicons dashicons-yes" style="margin-top: 4px;"></span> ' +
				kdcQtapStarterAdmin.i18n.copied
			);

			setTimeout( function() {
				$button.html( originalText );
			}, 2000 );
		},

		/**
		 * Confirm before importing.
		 *
		 * @param {Event} e Click event.
		 * @return {boolean} Whether to proceed.
		 */
		confirmImport: function( e ) {
			const $fileInput = $( '#kdc_qtap_starter_import_file' );

			// Check if file is selected.
			if ( ! $fileInput.val() ) {
				e.preventDefault();
				// eslint-disable-next-line no-alert
				window.alert( kdcQtapStarterAdmin.i18n.invalidFile );
				return false;
			}

			// Check file extension.
			const fileName = $fileInput.val();
			if ( ! fileName.toLowerCase().endsWith( '.json' ) ) {
				e.preventDefault();
				// eslint-disable-next-line no-alert
				window.alert( kdcQtapStarterAdmin.i18n.invalidFile );
				return false;
			}

			// Confirm import.
			// eslint-disable-next-line no-alert
			if ( ! window.confirm( kdcQtapStarterAdmin.i18n.importConfirm ) ) {
				e.preventDefault();
				return false;
			}

			return true;
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
		 * Show a notification.
		 *
		 * @param {string} message Notification message.
		 * @param {string} type    Notification type.
		 */
		showNotification: function( message, type ) {
			type = type || 'info';

			const $notice = $( '<div/>' )
				.addClass( 'kdc-qtap-starter-notice notice-' + type )
				.text( message );

			$( '.kdc-qtap-starter-settings h1' ).after( $notice );

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
