<?php
/**
 * Plugin Name:       DayPick Date Fields for Contact Form 7
 * Plugin URI:        https://wolinka.com/plugins/daypick-for-contact-form-7/
 * Description:       Modern, mobile-first date & time picker for Contact Form 7. All features free, all translations free.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Wolinka
 * Author URI:        https://wolinka.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       daypick-for-contact-form-7
 * Domain Path:       /languages
 * Requires Plugins:  contact-form-7
 */

defined( 'ABSPATH' ) || exit;

define( 'DAYPICK_VERSION', '1.0.0' );
define( 'DAYPICK_FILE', __FILE__ );
define( 'DAYPICK_DIR', plugin_dir_path( __FILE__ ) );
define( 'DAYPICK_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load and bootstrap the main class.
 * plugins_loaded @20: CF7 loads itself at @10; only afterwards can we
 * safely check whether its class exists.
 */
add_action( 'plugins_loaded', function () {
	require_once DAYPICK_DIR . 'includes/class-daypick.php';
	DayPick::get_instance();
}, 20 );
