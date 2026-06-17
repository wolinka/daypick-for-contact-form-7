<?php
defined( 'ABSPATH' ) || exit;

/**
 * Main bootstrap class. Dependency checks and hook registration live here;
 * the business logic lives in the sub-classes.
 */
final class DayPick {

	private static ?self $instance = null;

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		if ( ! class_exists( 'WPCF7' ) ) {
			add_action( 'admin_notices', [ $this, 'render_cf7_missing_notice' ] );
			return;
		}

		$this->load_dependencies();

		DayPick_Form_Tag::register();
		DayPick_Validation::register();
		DayPick_Assets::register();
		DayPick_Mail::register();

		if ( is_admin() ) {
			DayPick_Tag_Generator::register();
		}
	}

	private function load_dependencies(): void {
		require_once DAYPICK_DIR . 'includes/class-daypick-form-tag.php';
		require_once DAYPICK_DIR . 'includes/class-daypick-validation.php';
		require_once DAYPICK_DIR . 'includes/class-daypick-assets.php';
		require_once DAYPICK_DIR . 'includes/class-daypick-mail.php';

		// The tag generator is only needed in the admin panel.
		if ( is_admin() ) {
			require_once DAYPICK_DIR . 'includes/class-daypick-tag-generator.php';
		}
	}

	/**
	 * Notify the administrator when Contact Form 7 is not active.
	 */
	public function render_cf7_missing_notice(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html__( 'DayPick requires the Contact Form 7 plugin to be installed and activated.', 'daypick-for-contact-form-7' )
		);
	}
}
