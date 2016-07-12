/* global Modernizr */

/**
 * Sticky menu.
 */

define( ['jquery', 'underscore'], function ( $, _ ) {
	'use strict';

	var config = {
		bodyStickyClass:       'js-sticky-navigation', // Present, when sticky is enabled in customizer.
		stickyOffsetClass:     'js-sticky-offset', // Class used for triggering the sticky menu.
		stickyContainerClass:  'js-pt-sticky-menu', // Class of the main sticky menu container.
	};

	var StickyMenu = function() {

		// Initialize variables.
		this.windowTop    = 0;
		this.stickyOffset = 0;

		// Initialize the sticky menu.
		this.initializeStickyMenu();

		// Register the resize event listeners.
		this.registerResizeEventListener();

		// Register the click event listeners.
		this.registerClickEventListeners();
	};

	_.extend( StickyMenu.prototype, {

		/**
		 * Initialize Sticky menu, if the body has the config.bodyStickyClass class.
		 */
		initializeStickyMenu: function () {
			if ( $( 'body' ).hasClass( config.bodyStickyClass ) ) {

				// Set the initial windowTop position.
				this.windowTop = $( window ).scrollTop();

				// Get the initial offset.
				this.stickyOffset = this.getStickyMenuOffset();

				// Register sticky menu scroll event.
				this.registerScrollEventListner();
				$( window ).trigger( 'scroll.ptStickyMenu' );
			}
		},

		/**
		 * Display the sticky menu (register the scroll event).
		 */
		registerScrollEventListner: function () {
			$( window ).on( 'scroll.ptStickyMenu', _.bind( _.throttle( function() {

				// Display the sticky menu only if scrolling up and if the window top is bellow the offset marker.
				if ( this.isScrollDirectionUp() && this.isWindowTopBellowOffset() ) {
					$( '.' + config.stickyContainerClass ).slideDown();
				}
				else {
					$( '.' + config.stickyContainerClass ).slideUp();
				}
			}, 20 ), this ) ); // 1000/20 = 50fps. Good performance.
		},

		/**
		 * Register click event listeners.
		 */
		registerClickEventListeners: function () {

			// Back to top animation and open the mobile menu.
			$( document ).on( 'click' , '.js-pt-sticky-menu-back-to-top-open-menu', function() {
				$( 'html, body' ).animate( { scrollTop : 0 }, 500, 'swing', function() {
					$( '.js-sticky-mobile-option' ).click();
				} );

				return false;
			} );
		},

		/**
		 * Set the config.stickyOffsetClass class to the appropriate DOM element.
		 */
		setStickyOffsetClass: function () {
			$( '.js-sticky-desktop-option' ).toggleClass( config.stickyOffsetClass, Modernizr.mq( '(min-width: 992px)' ) );
			$( '.js-sticky-mobile-option' ).toggleClass( config.stickyOffsetClass, Modernizr.mq( '(max-width: 991px)' ) );
		},

		/**
		 * Get the sticky menu offset.
		 */
		getStickyMenuOffset: function () {

			// First set the sticky offset class to the appropriate DOM element.
			this.setStickyOffsetClass();

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
				this.stickyOffset = this.getStickyMenuOffset();
			}, 100 ), this ) );
		},

		/**
		 * Get the WP admin bar height.
		 */
		getAdminBarHeight: function () {
			if ( $( 'body' ).hasClass( 'admin-bar' ) && 'fixed' === $( '#wpadminbar' ).css( 'position' ) ) {
				return $( '#wpadminbar' ).height();
			}

			return 0;
		},

		/**
		 * Get the direction of scroll (negative value = up, positive value = down).
		 */
		getScrollDirection: function () {
			var currentWindowTop = $( window ).scrollTop(),
					value            = currentWindowTop - this.windowTop;

			this.windowTop = currentWindowTop;

			return value;
		},

		/**
		 * Is the direction of the scroll = up?
		 */
		isScrollDirectionUp: function () {
			if ( 0 > this.getScrollDirection() ) {
				return true;
			}

			return false;
		},

		/**
		 * Is the top of the window bellow the offset marker?
		 */
		isWindowTopBellowOffset: function () {
			return $( window ).scrollTop() > ( this.stickyOffset - this.getAdminBarHeight() );
		},
	} );

	new StickyMenu();

} );
