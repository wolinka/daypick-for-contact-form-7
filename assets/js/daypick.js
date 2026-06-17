/**
 * DayPick – Date & Time Picker for Contact Form 7
 *
 * Initializes input[data-daypick] fields with flatpickr.
 * altInput: the visitor sees their local format, the form always submits an ISO value.
 */
( function () {
	'use strict';

	var settings = window.daypickSettings || {};

	var ISO_FORMATS = {
		date: 'Y-m-d',
		time: 'H:i',
		datetime: 'Y-m-d H:i'
	};

	function resolveLocale() {
		var key = settings.locale;
		if ( ! key || typeof flatpickr === 'undefined' || ! flatpickr.l10ns ) {
			return null;
		}
		// The file name and the l10ns key may differ: zh-tw → zh_tw, sr-cyr → sr.
		return flatpickr.l10ns[ key ] ||
			flatpickr.l10ns[ key.replace( /-/g, '_' ) ] ||
			flatpickr.l10ns[ key.split( '-' )[ 0 ] ] ||
			null;
	}

	function buildOptions( cfg ) {
		var mode = ISO_FORMATS[ cfg.mode ] ? cfg.mode : 'date';

		var opts = {
			altInput: true,
			altFormat: cfg.format || ( settings.formats && settings.formats[ mode ] ) || ISO_FORMATS[ mode ],
			dateFormat: ISO_FORMATS[ mode ],
			enableTime: 'date' !== mode,
			noCalendar: 'time' === mode,
			time_24hr: 'time24' in cfg ? !! cfg.time24 : false !== settings.time24,
			// The native mobile picker has no rule (hours/disable/step) support; use flatpickr everywhere.
			disableMobile: true
		};

		var locale = resolveLocale() || {};
		var firstDay = 'firstday' in cfg ? cfg.firstday : settings.firstDay;
		if ( 'undefined' !== typeof firstDay ) {
			locale = Object.assign( {}, locale, { firstDayOfWeek: parseInt( firstDay, 10 ) || 0 } );
		}
		if ( Object.keys( locale ).length ) {
			opts.locale = locale;
		}

		if ( cfg.min ) {
			opts.minDate = cfg.min;
		}
		if ( cfg.max ) {
			opts.maxDate = cfg.max;
		}
		if ( cfg.minTime ) {
			opts.minTime = cfg.minTime;
		}
		if ( cfg.maxTime ) {
			opts.maxTime = cfg.maxTime;
		}
		if ( cfg.step ) {
			opts.minuteIncrement = cfg.step;
		}

		// Add the class that scopes the DayPick theme to the calendar container; the
		// whole theme is anchored to it so it does not leak into other flatpickr instances site-wide.
		opts.onReady = function ( selectedDates, dateStr, instance ) {
			if ( instance.calendarContainer ) {
				instance.calendarContainer.classList.add( 'daypick-calendar' );
			}
		};

		if ( cfg.disable && cfg.disable.length ) {
			opts.disable = cfg.disable.map( function ( rule ) {
				if ( 'weekends' === rule ) {
					return function ( date ) {
						return 0 === date.getDay() || 6 === date.getDay();
					};
				}
				return rule;
			} );
		}

		return opts;
	}

	function init( scope ) {
		if ( typeof flatpickr === 'undefined' ) {
			return;
		}

		var inputs = ( scope || document ).querySelectorAll( 'input[data-daypick]' );

		Array.prototype.forEach.call( inputs, function ( el ) {
			if ( el._flatpickr ) {
				return;
			}

			var cfg;
			try {
				cfg = JSON.parse( el.getAttribute( 'data-daypick' ) ) || {};
			} catch ( e ) {
				cfg = {};
			}

			var opts = buildOptions( cfg );
			opts.altInputClass = el.className + ' daypick-alt-input';

			flatpickr( el, opts );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		init( document );

		// Forms added later in popup/dynamic content.
		if ( 'undefined' !== typeof MutationObserver ) {
			new MutationObserver( function ( mutations ) {
				mutations.forEach( function ( m ) {
					Array.prototype.forEach.call( m.addedNodes, function ( node ) {
						if ( 1 === node.nodeType && node.querySelector && node.querySelector( 'input[data-daypick]' ) ) {
							init( node );
						}
					} );
				} );
			} ).observe( document.body, { childList: true, subtree: true } );
		}
	} );

	// After a successful submission CF7 resets the form; also clear the stale value in altInput.
	document.addEventListener( 'wpcf7mailsent', function ( event ) {
		var wrapper = event.target;
		if ( ! wrapper || ! wrapper.querySelectorAll ) {
			return;
		}
		Array.prototype.forEach.call( wrapper.querySelectorAll( 'input[data-daypick]' ), function ( el ) {
			if ( el._flatpickr ) {
				el._flatpickr.clear();
			}
		} );
	} );

	// Public initializer for theme/plugin integrations.
	window.daypickInit = init;
} )();
