<?php
defined( 'ABSPATH' ) || exit;

/**
 * Server-side validation. Even if the client (flatpickr) is bypassed,
 * min/max, disabled days, time range and format are enforced here.
 */
final class DayPick_Validation {

	public static function register(): void {
		add_filter( 'wpcf7_validate_daypick', [ __CLASS__, 'validate' ], 10, 2 );
		add_filter( 'wpcf7_validate_daypick*', [ __CLASS__, 'validate' ], 10, 2 );
		add_filter( 'wpcf7_messages', [ __CLASS__, 'messages' ], 10, 1 );
	}

	/**
	 * @param WPCF7_Validation $result
	 * @param WPCF7_FormTag    $tag
	 * @return WPCF7_Validation
	 */
	public static function validate( $result, $tag ) {
		// CF7 form submission is public; there is no nonce/capability context.
		// The value is only validated against a pattern, never used raw.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$value = isset( $_POST[ $tag->name ] ) && is_string( $_POST[ $tag->name ] )
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			? trim( sanitize_text_field( wp_unslash( $_POST[ $tag->name ] ) ) )
			: '';

		if ( '' === $value ) {
			if ( $tag->is_required() ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
			}
			return $result;
		}

		$config = DayPick_Form_Tag::get_config( $tag );
		$mode   = $config['mode'];

		$patterns = [
			'date'     => '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/',
			'time'     => '/^([01][0-9]|2[0-3]):([0-5][0-9])$/',
			'datetime' => '/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([01][0-9]|2[0-3]):([0-5][0-9])$/',
		];

		if ( ! preg_match( $patterns[ $mode ], $value, $m ) ) {
			$result->invalidate( $tag, wpcf7_get_message( 'daypick_invalid_format' ) );
			return $result;
		}

		if ( 'time' !== $mode ) {
			$date_part = substr( $value, 0, 10 );

			if ( ! checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) ) {
				$result->invalidate( $tag, wpcf7_get_message( 'daypick_invalid_format' ) );
				return $result;
			}

			// In ISO format, lexicographic order = chronological order.
			if ( ! empty( $config['min'] ) && $date_part < $config['min'] ) {
				$result->invalidate( $tag, wpcf7_get_message( 'daypick_date_too_early' ) );
				return $result;
			}

			if ( ! empty( $config['max'] ) && $date_part > $config['max'] ) {
				$result->invalidate( $tag, wpcf7_get_message( 'daypick_date_too_late' ) );
				return $result;
			}

			if ( ! empty( $config['disable'] ) && self::is_disabled_date( $date_part, $config['disable'] ) ) {
				$result->invalidate( $tag, wpcf7_get_message( 'daypick_date_disabled' ) );
				return $result;
			}
		}

		if ( 'date' !== $mode ) {
			$time_part = substr( $value, -5 );

			if ( ! empty( $config['minTime'] ) && $time_part < $config['minTime'] ) {
				$result->invalidate( $tag, wpcf7_get_message( 'daypick_time_out_of_range' ) );
				return $result;
			}

			if ( ! empty( $config['maxTime'] ) && $time_part > $config['maxTime'] ) {
				$result->invalidate( $tag, wpcf7_get_message( 'daypick_time_out_of_range' ) );
				return $result;
			}

			if ( ! empty( $config['step'] ) && ( (int) substr( $time_part, 3 ) % $config['step'] ) !== 0 ) {
				$result->invalidate( $tag, wpcf7_get_message( 'daypick_time_out_of_range' ) );
				return $result;
			}
		}

		return $result;
	}

	private static function is_disabled_date( string $date, array $disable ): bool {
		if ( in_array( $date, $disable, true ) ) {
			return true;
		}

		if ( in_array( 'weekends', $disable, true ) ) {
			$dt = DateTimeImmutable::createFromFormat( '!Y-m-d', $date, wp_timezone() );
			if ( $dt && in_array( (int) $dt->format( 'w' ), [ 0, 6 ], true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Adds the DayPick messages to the CF7 message pool; editable from the form editor.
	 */
	public static function messages( array $messages ): array {
		return array_merge( $messages, [
			'daypick_invalid_format'    => [
				'description' => __( 'Submitted date/time value is malformed', 'daypick-for-contact-form-7' ),
				'default'     => __( 'Please pick a valid date and time.', 'daypick-for-contact-form-7' ),
			],
			'daypick_date_too_early'    => [
				'description' => __( 'Date is earlier than the allowed minimum', 'daypick-for-contact-form-7' ),
				'default'     => __( 'Please pick a later date.', 'daypick-for-contact-form-7' ),
			],
			'daypick_date_too_late'     => [
				'description' => __( 'Date is later than the allowed maximum', 'daypick-for-contact-form-7' ),
				'default'     => __( 'Please pick an earlier date.', 'daypick-for-contact-form-7' ),
			],
			'daypick_date_disabled'     => [
				'description' => __( 'Date falls on a disabled day', 'daypick-for-contact-form-7' ),
				'default'     => __( 'This date is not available, please pick another one.', 'daypick-for-contact-form-7' ),
			],
			'daypick_time_out_of_range' => [
				'description' => __( 'Time is outside the allowed range or step', 'daypick-for-contact-form-7' ),
				'default'     => __( 'This time is not available, please pick another one.', 'daypick-for-contact-form-7' ),
			],
		] );
	}
}
