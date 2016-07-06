/**
 * Sticky menu.
 */

define( ['jquery', 'underscore'], function ( $, _ ) {
	'use strict';

	// Flag var, null for the start before we test.
	var adminBarHeight = $( 'body' ).hasClass( 'admin-bar' ) ? 32 : 0,
		stickyOffset     = $( '.js-sticky-offset' ).offset().top,
		bodyStickyClass  = 'sticky-navigation';

	// Events listeners, everything goes trough here.
	$( 'body' ).on( 'update_sticky_state.pt', function () {
		if ( $( 'body' ).hasClass( bodyStickyClass ) ) {
			addStickyNavbar();
			$( window).trigger( 'scroll.stickyNavbar' );
		}
		else {
			removeStickyNavbar();
		}
	} );

	// Add sticky navbar events and classes.
	var addStickyNavbar = function () {
		$( window).on( 'scroll.stickyNavbar', _.throttle( function() {
			$( 'body' ).toggleClass( 'is-sticky-nav', $( window ).scrollTop() > ( stickyOffset - adminBarHeight ) );
		}, 20 ) ); // Only trigered once every 20ms = 50 fps = very cool for performance.
	};

	// Cleanup for events and classes.
	var removeStickyNavbar = function () {
		$( window ).off( 'scroll.stickyNavbar' );
		$( 'body' ).removeClass( 'is-sticky-nav' );
	};

	// Event listener on the window resizing.
	$( window ).on( 'resize.stickyNavbar', _.debounce( function() {
		// Update sticky offset.
		stickyOffset = $( '.js-sticky-offset' ).offset().top;

		// Turn on or off the sticky behaviour, depending if the <body> has class "sticky-navigation".
		$( 'body' ).trigger( 'update_sticky_state.pt' );
	}, 40 ) );

	// Trigger for the initialization.
	$( window ).trigger( 'resize.stickyNavbar' );
} );