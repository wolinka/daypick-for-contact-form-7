<?php
defined( 'ABSPATH' ) || exit;

/**
 * Asset management. All scripts/styles are registered early,
 * but only enqueued when a daypick field is rendered on the page.
 */
final class DayPick_Assets {

	private const FLATPICKR_VERSION = '4.6.13';

	private static bool $enqueued = false;

	public static function register(): void {
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_assets' ] );
	}

	public static function register_assets(): void {
		wp_register_style(
			'daypick-flatpickr',
			DAYPICK_URL . 'assets/vendor/flatpickr/flatpickr.min.css',
			[],
			self::FLATPICKR_VERSION
		);

		wp_register_style(
			'daypick',
			DAYPICK_URL . 'assets/css/daypick.css',
			[ 'daypick-flatpickr' ],
			DAYPICK_VERSION
		);

		$script_args = [
			'strategy'  => 'defer',
			'in_footer' => true,
		];

		wp_register_script(
			'daypick-flatpickr',
			DAYPICK_URL . 'assets/vendor/flatpickr/flatpickr.min.js',
			[],
			self::FLATPICKR_VERSION,
			$script_args
		);

		$deps   = [ 'daypick-flatpickr' ];
		$locale = self::flatpickr_locale();

		if ( '' !== $locale ) {
			wp_register_script(
				'daypick-flatpickr-l10n',
				DAYPICK_URL . 'assets/vendor/flatpickr/l10n/' . $locale . '.js',
				[ 'daypick-flatpickr' ],
				self::FLATPICKR_VERSION,
				$script_args
			);
			$deps[] = 'daypick-flatpickr-l10n';
		}

		wp_register_script(
			'daypick',
			DAYPICK_URL . 'assets/js/daypick.js',
			$deps,
			DAYPICK_VERSION,
			$script_args
		);

		$date_format = DayPick_Form_Tag::to_flatpickr_format( (string) get_option( 'date_format', 'F j, Y' ) );
		$time_format = DayPick_Form_Tag::to_flatpickr_format( (string) get_option( 'time_format', 'H:i' ) );

		$settings = [
			'locale'   => $locale,
			'firstDay' => (int) get_option( 'start_of_week', 1 ),
			'time24'   => ( false === strpos( $time_format, 'K' ) ),
			'formats'  => [
				'date'     => $date_format,
				'time'     => $time_format,
				'datetime' => $date_format . ' ' . $time_format,
			],
		];

		wp_add_inline_script(
			'daypick',
			'window.daypickSettings = ' . wp_json_encode( $settings ) . ';',
			'before'
		);
	}

	/**
	 * Called by the form tag handler during rendering; nothing loads if there is no field on the page.
	 */
	public static function enqueue(): void {
		if ( self::$enqueued ) {
			return;
		}
		self::$enqueued = true;

		wp_enqueue_style( 'daypick' );
		wp_enqueue_script( 'daypick' );
	}

	/**
	 * Maps the WP locale (e.g. tr_TR) to a bundled flatpickr l10n file name.
	 * Returns empty if there is no match (English default).
	 */
	private static function flatpickr_locale(): string {
		$wp_locale = get_locale();
		$lower     = strtolower( $wp_locale );
		$lang      = strtok( $lower, '_' );

		// Cases where the flatpickr file name diverges from the language code.
		$special = [
			'ca'    => 'cat',
			'el'    => 'gr',
			'nb_no' => 'no',
			'vi'    => 'vn',
			'kk'    => 'kz',
			'sr_rs' => 'sr-cyr',
			'zh_cn' => 'zh',
			'zh_tw' => 'zh-tw',
			'zh_hk' => 'zh-tw',
			'de_at' => 'at',
			'pt_br' => 'pt',
		];

		$candidates = array_unique( array_filter( [
			$special[ $lower ] ?? '',
			$special[ $lang ] ?? '',
			str_replace( '_', '-', $lower ),
			$lang,
		] ) );

		$found = '';
		foreach ( $candidates as $candidate ) {
			if ( file_exists( DAYPICK_DIR . 'assets/vendor/flatpickr/l10n/' . $candidate . '.js' ) ) {
				$found = $candidate;
				break;
			}
		}

		/**
		 * Filters the flatpickr locale file name to use.
		 *
		 * @param string $found     The locale found (empty = English).
		 * @param string $wp_locale WP locale (e.g. tr_TR).
		 */
		return (string) apply_filters( 'daypick_locale', $found, $wp_locale );
	}
}
