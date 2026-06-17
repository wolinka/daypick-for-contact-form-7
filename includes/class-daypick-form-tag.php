<?php
defined( 'ABSPATH' ) || exit;

/**
 * Registration and rendering of the [daypick] / [daypick*] form tag.
 *
 * Tag parameters:
 *   mode:date|time|datetime   (default: date)
 *   min:today | min:2026-07-01 | min:+90d
 *   max:today | max:2026-09-30 | max:+90d
 *   hours:09:00-18:00
 *   step:30                   (minute step, 1-60)
 *   disable:weekends | disable:2026-07-15,2026-07-16
 *   firstday:1                (0=Sunday, 1=Monday)
 *   format:d.m.Y              (displayed format; the form always submits ISO)
 */
final class DayPick_Form_Tag {

	public static function register(): void {
		add_action( 'wpcf7_init', [ __CLASS__, 'add_form_tag' ], 10, 0 );
	}

	public static function add_form_tag(): void {
		wpcf7_add_form_tag(
			[ 'daypick', 'daypick*' ],
			[ __CLASS__, 'handler' ],
			[ 'name-attr' => true ]
		);
	}

	/**
	 * @param WPCF7_FormTag $tag
	 */
	public static function handler( $tag ): string {
		if ( empty( $tag->name ) ) {
			return '';
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type );
		$class .= ' daypick-input';

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$config = self::get_config( $tag );

		$atts = [
			'class'        => $tag->get_class_option( $class ),
			'id'           => $tag->get_id_option(),
			'tabindex'     => $tag->get_option( 'tabindex', 'signed_int', true ),
			'autocomplete' => 'off',
			'type'         => 'text',
			'name'         => $tag->name,
			'data-daypick' => wp_json_encode( $config ),
		];

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		if ( $validation_error ) {
			$atts['aria-invalid']     = 'true';
			$atts['aria-describedby'] = wpcf7_get_validation_error_reference( $tag->name );
		} else {
			$atts['aria-invalid'] = 'false';
		}

		$value = (string) reset( $tag->values );

		if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
			$atts['placeholder'] = $value;
			$value               = '';
		}

		$value = $tag->get_default_option( $value );
		$value = wpcf7_get_hangover( $tag->name, $value );

		$atts['value'] = $value;

		// Assets are loaded only when a daypick field is actually rendered on the page.
		DayPick_Assets::enqueue();

		return sprintf(
			'<span class="wpcf7-form-control-wrap" data-name="%1$s"><input %2$s />%3$s</span>',
			esc_attr( $tag->name ),
			wpcf7_format_atts( $atts ), // wpcf7_format_atts escapes values with esc_attr
			$validation_error
		);
	}

	/**
	 * Converts tag options into a validated config array.
	 * Both rendering (data-daypick JSON) and server-side validation use this single source.
	 *
	 * @param WPCF7_FormTag $tag
	 */
	public static function get_config( $tag ): array {
		$mode = (string) $tag->get_option( 'mode', '', true );
		if ( ! in_array( $mode, [ 'date', 'time', 'datetime' ], true ) ) {
			$mode = 'date';
		}

		$config = [ 'mode' => $mode ];

		if ( 'time' !== $mode ) {
			$min = self::resolve_date( (string) $tag->get_option( 'min', '', true ) );
			$max = self::resolve_date( (string) $tag->get_option( 'max', '', true ) );

			if ( '' !== $min ) {
				$config['min'] = $min;
			}
			if ( '' !== $max ) {
				$config['max'] = $max;
			}

			$disable = self::parse_disable( (array) $tag->get_option( 'disable' ) );
			if ( $disable ) {
				$config['disable'] = $disable;
			}
		}

		if ( 'date' !== $mode ) {
			$hours = (string) $tag->get_option( 'hours', '', true );
			if ( preg_match( '/^([01][0-9]|2[0-3]):([0-5][0-9])-([01][0-9]|2[0-3]):([0-5][0-9])$/', $hours, $m ) ) {
				$min_time = $m[1] . ':' . $m[2];
				$max_time = $m[3] . ':' . $m[4];
				if ( $min_time < $max_time ) {
					$config['minTime'] = $min_time;
					$config['maxTime'] = $max_time;
				}
			}

			$step = absint( $tag->get_option( 'step', 'int', true ) );
			if ( $step >= 1 && $step <= 60 ) {
				$config['step'] = $step;
			}
		}

		$firstday = $tag->get_option( 'firstday', '[0-6]', true );
		if ( false !== $firstday && '' !== $firstday ) {
			$config['firstday'] = (int) $firstday;
		}

		$format = (string) $tag->get_option( 'format', '', true );
		if ( '' !== $format ) {
			// Tag options cannot contain spaces; underscores are converted to spaces (format:d.m.Y_H:i).
			$format           = str_replace( '_', ' ', sanitize_text_field( $format ) );
			$config['format'] = self::to_flatpickr_format( $format );
			$config['time24'] = ( false === strpos( $config['format'], 'K' ) );
		}

		return $config;
	}

	/**
	 * Resolves a min/max value into a concrete Y-m-d date.
	 * Accepted formats: today, +Nd, -Nd, YYYY-MM-DD.
	 */
	private static function resolve_date( string $value ): string {
		if ( '' === $value ) {
			return '';
		}

		if ( 'today' === $value ) {
			return wp_date( 'Y-m-d' );
		}

		if ( preg_match( '/^([+-])([0-9]{1,4})d$/', $value, $m ) ) {
			$offset = (int) $m[2] * ( '-' === $m[1] ? -1 : 1 );
			return wp_date( 'Y-m-d', time() + $offset * DAY_IN_SECONDS );
		}

		if ( preg_match( '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $value, $m )
			&& checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) ) {
			return $value;
		}

		return '';
	}

	/**
	 * Normalizes the disable options: ['weekends', 'YYYY-MM-DD', ...]
	 * Multiple disable: options and comma-separated lists are supported.
	 */
	private static function parse_disable( array $options ): array {
		$out = [];

		foreach ( $options as $option ) {
			foreach ( explode( ',', (string) $option ) as $item ) {
				$item = trim( $item );

				if ( 'weekends' === $item ) {
					$out[] = 'weekends';
				} elseif ( preg_match( '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $item, $m )
					&& checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) ) {
					$out[] = $item;
				}
			}
		}

		return array_values( array_unique( $out ) );
	}

	/**
	 * Converts a PHP date() format into a flatpickr format.
	 * Most tokens are shared; the differing ones are mapped in a single pass (strtr):
	 *   a/A → K (AM/PM), g → h (12h, no leading zero), h → G (12h, leading zero),
	 *   G → H (24h), s → S (seconds), jS → J (ordinal day suffix).
	 */
	public static function to_flatpickr_format( string $format ): string {
		return strtr( $format, [
			'jS' => 'J',
			'a'  => 'K',
			'A'  => 'K',
			'g'  => 'h',
			'h'  => 'G',
			'G'  => 'H',
			's'  => 'S',
		] );
	}
}
