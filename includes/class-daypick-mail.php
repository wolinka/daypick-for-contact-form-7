<?php
defined( 'ABSPATH' ) || exit;

/**
 * Formats DayPick values in the email (mail) layer.
 *
 * The raw value submitted to the form is always ISO (Y-m-d / H:i / Y-m-d H:i);
 * this keeps the database and exports such as CFDB7 consistent. Only in the
 * email, and only if the field has a `format:` option, is it converted to a
 * human-readable format. Without `format:` the legacy behavior (ISO) is kept.
 */
final class DayPick_Mail {

	public static function register(): void {
		add_filter( 'wpcf7_mail_tag_replaced', [ __CLASS__, 'format_value' ], 10, 4 );
	}

	/**
	 * @param string         $replaced  The value produced so far (escaped if needed).
	 * @param string|array   $submitted The raw submitted value (ISO).
	 * @param bool           $html      Whether the mail body is HTML.
	 * @param WPCF7_MailTag  $mail_tag  The mail tag object.
	 * @return string
	 */
	public static function format_value( $replaced, $submitted, $html, $mail_tag ) {
		// Multiple values (e.g. an array) never occur in this plugin; stay on the safe side.
		if ( ! is_string( $submitted ) || '' === $submitted ) {
			return $replaced;
		}

		if ( ! is_object( $mail_tag ) || ! method_exists( $mail_tag, 'field_name' ) ) {
			return $replaced;
		}

		$contact_form = WPCF7_ContactForm::get_current();
		if ( ! $contact_form ) {
			return $replaced;
		}

		$tags = $contact_form->scan_form_tags( [ 'name' => $mail_tag->field_name() ] );
		if ( empty( $tags ) ) {
			return $replaced;
		}

		$tag = $tags[0];
		if ( 'daypick' !== $tag->basetype ) {
			return $replaced;
		}

		// If there is no `format:`, leave it untouched — ISO behavior is kept.
		$format = (string) $tag->get_option( 'format', '', true );
		if ( '' === $format ) {
			return $replaced;
		}
		$format = str_replace( '_', ' ', sanitize_text_field( $format ) );

		$config = DayPick_Form_Tag::get_config( $tag );
		$mode   = $config['mode'];

		$iso_formats = [
			'date'     => '!Y-m-d',
			'time'     => '!H:i',
			'datetime' => '!Y-m-d H:i',
		];

		$dt = DateTimeImmutable::createFromFormat( $iso_formats[ $mode ], $submitted, wp_timezone() );
		if ( false === $dt ) {
			return $replaced;
		}

		// wp_date: formats using the site time zone + locale (translated month/day names).
		$formatted = wp_date( $format, $dt->getTimestamp() );
		if ( false === $formatted ) {
			return $replaced;
		}

		return $html ? esc_html( $formatted ) : $formatted;
	}
}
