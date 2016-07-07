/* global Modernizr */

/**
 * Sticky menu.
 */

define( ['jquery', 'underscore'], function ( $, _ ) {
	'use strict';

	var config = {
		adminBarHeight:       $( 'body' ).hasClass( 'admin-bar' ) ? 32 : 0,
		bodyStickyClass:      'sticky-navigation',
		stickyOffsetClass:    'js-sticky-offset',
	};

	var StickyMenu = function() {

		// Set the initial config.stickyOffsetClass class to the appropriate DOM element.
		this.setStickyOffsetClass();

		// Get the initial offset.
		this.stickyOffset = this.getStickyMenuOffset();

		// Register the Event listeners.
		this.registerUpdateEventListener();
		this.registerResizeEventListener();

		// Trigger for the initialization.
		$( window ).trigger( 'resize.ptStickyMenu' );
	};

	_.extend( StickyMenu.prototype, {
		/**
		 * Set the config.stickyOffsetClass class to the appropriate DOM element.
		 */
		setStickyOffsetClass: function () {
			$( '.js-sticky-desktop-option' ).toggleClass( config.stickyOffsetClass, Modernizr.mq( '(min-width: 992px)' ) );
			$( '.js-sticky-mobile-option' ).toggleClass( config.stickyOffsetClass, Modernizr.mq( '(max-width: 991px)' ) );
		},

		/**
		 * Register the sticky menu update event listener. Everything goes though here.
		 */
		registerUpdateEventListener: function () {
			$( 'body' ).on( 'update.ptStickyMenu', _.bind( function () {
				if ( $( 'body' ).hasClass( config.bodyStickyClass ) ) {
					this.addStickyNavbar();
					$( window).trigger( 'scroll.ptStickyMenu' );
				}
				else {
					this.removeStickyNavbar();
				}
			}, this ) );
		},

		/**
		 * Display the sticky menu (add a class '.is-sticky-nav' to the body).
		 */
		addStickyNavbar: function () {
			$( window ).on( 'scroll.ptStickyMenu', _.bind( _.throttle( function() {
				$( 'body' ).toggleClass( 'is-sticky-nav', $( window ).scrollTop() > ( this.stickyOffset - config.adminBarHeight ) );
			}, 20 ), this ) ); // Only trigered once every 20ms = 50 fps = very cool for performance.
		},

		/**
		 * Remove the sticky menu (remove the class '.is-sticky-nav' from the body).
		 */
		removeStickyNavbar: function () {
			$( window ).off( 'scroll.ptStickyMenu' );
			$( 'body' ).removeClass( 'is-sticky-nav' );
		},

		/**
		 * Get the sticky menu offset.
		 */
		getStickyMenuOffset: function () {
			if ( 0 < $( '.' + config.stickyOffsetClass ).length ) {
				return $( '.' + config.stickyOffsetClass ).offset().top;
			}

			return 0;
		},

		/**
		 * Register the sticky menu window resize event listener.
		 */
		registerResizeEventListener: function () {
			$( window ).on( 'resize.ptStickyMenu', _.bind( _.debounce( function() {
				// Update sticky offset.
				this.setStickyOffsetClass();
				this.stickyOffset = this.getStickyMenuOffset();

				// Turn on or off the sticky behaviour, depending if the <body> has class config.bodyStickyClass.
				$( 'body' ).trigger( 'update.ptStickyMenu' );
			}, 100 ), this ) );
		},

		/**
		 * Remove the sticky menu (remove the class '.is-sticky-nav' from the body).
		 */
		getAdminBarHeight: function () {

		},
	} );

	new StickyMenu();

} );