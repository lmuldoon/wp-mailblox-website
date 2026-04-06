/**
 * Stop viewport scroll when fixed element containers are open.
 *
 * Because of the potential for modals within modals, disable() is designed 
 * to be called multiple times. 
 * Scroll will only be reenabled once enable() has been called the 
 * same number of times as disable() was originally called.
 */

( function( $, window, document, undefined ) {

	let ViewportScroll = class ViewportScroll {
		/**
		 * Setup the class and properties.
		 */
		constructor() {
			let _ = this;

			_._disabledCount = 0;
			_._scrollTop = $(window).scrollTop();
			
			_._bindEvents();
		}

		_bindEvents() {
			let _ = this;

			// Restore scroll position when window refreshes
			window.onbeforeunload = () => {
				if ( _.isDisabled() ) {
					$('body').css( {
						'position': '',
						'top': '',
						'overflow-y': '',
					} );
					window.scrollTo( 0, _._scrollTop );
				}
			};

			return _;
		}

		/**
		 * Disable scroll on the viewport.
		 * @param {Boolean} overflowState The type of overflow, 'scroll' or 'hidden'.
		 *                                Defaults to 'hidden'.
		 * @return {self}
		 */
		disable( overflowState = 'hidden' ) {
			let _ = this;

			_._disabledCount++;

			if ( 1 === _._disabledCount ) {
				_._scrollTop = $(window).scrollTop();

				$('body').css( {
					position: 'fixed',
					top: -( _._scrollTop  - parseInt( $('html').css('marginTop') ) ) + 'px',
					overflowY: overflowState,
				} );
			}

			return _._disabledCount;
		}

		/**
		 * Enable scroll on the viewport.
		 * @return {self}
		 */
		enable() {
			let _ = this;

			_._disabledCount--;
			// Constrain to min of 0
			_._disabledCount = Math.max( 0, _._disabledCount );

			if ( 0 === _._disabledCount ) {
				$('body').css( {
					'position': '',
					'top': '',
					'overflow-y': '',
				} );
				$(window).scrollTop( _._scrollTop );
			}

			return _;
		}

		/**
		 * Check if viewport scrolling is disabled.
		 * @return {Boolean}
		 */
		isDisabled() {
			let _ = this;

			return _._disabledCount > 0;
		}

		/**
		 * Get the number of times required to call enable()
		 * before viewport scroll is reenabled.
		 * @return {Number}
		 */
		getDisabledCount() {
			let _ = this;

			return _._disabledCount;
		}
	};
	

	// Expose API
	window.ViewportScroll = new ViewportScroll();

} )( jQuery, window, document );
