<?php
defined( 'ABSPATH' ) || exit;

/**
 * Adds a "DayPick" tag generator panel to the CF7 form editor (v2 API, CF7 5.9+).
 * The panel HTML works with CF7's own composer JS:
 *   data-tag-part="option" + data-tag-option="min:" → value is appended as "min:...",
 *   for checkboxes, data-tag-option is the fixed option name (e.g. "disable:weekends").
 */
final class DayPick_Tag_Generator {

	public static function register(): void {
		add_action( 'wpcf7_admin_init', [ __CLASS__, 'add_tag_generator' ], 30, 0 );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_styles' ], 10, 1 );
	}

	/**
	 * Loads the panel style only on the CF7 form editor page.
	 *
	 * @param string $hook The hook name of the current admin page.
	 */
	public static function enqueue_styles( $hook ): void {
		if ( false === strpos( (string) $hook, 'wpcf7' ) ) {
			return;
		}

		wp_enqueue_style(
			'daypick-admin',
			DAYPICK_URL . 'assets/css/daypick-admin.css',
			[],
			DAYPICK_VERSION
		);
	}

	public static function add_tag_generator(): void {
		if ( ! class_exists( 'WPCF7_TagGenerator' ) ) {
			return;
		}

		WPCF7_TagGenerator::get_instance()->add(
			'daypick',
			__( 'date/time picker (DayPick)', 'daypick-for-contact-form-7' ),
			[ __CLASS__, 'render' ],
			[ 'version' => '2' ]
		);
	}

	/**
	 * @param WPCF7_ContactForm $contact_form
	 * @param array             $options
	 */
	public static function render( $contact_form, $options ): void {
		?>
<div class="daypick-taggen">
<header class="description-box daypick-desc">
	<h3><?php echo esc_html__( 'Date/time picker form-tag generator (DayPick)', 'daypick-for-contact-form-7' ); ?></h3>
	<p><?php echo esc_html__( 'Generates a form-tag for a date and/or time picker field. The visitor sees the date in their local format; the form always submits an ISO value.', 'daypick-for-contact-form-7' ); ?></p>
</header>

<div class="control-box">
	<div class="daypick-fields">
	<fieldset>
		<legend><?php echo esc_html__( 'Field type', 'daypick-for-contact-form-7' ); ?></legend>
		<select data-tag-part="basetype">
			<option value="daypick"><?php echo esc_html__( 'Date/time picker (DayPick)', 'daypick-for-contact-form-7' ); ?></option>
		</select>
		<label class="daypick-check">
			<input type="checkbox" data-tag-part="type-suffix" value="*" />
			<?php echo esc_html__( 'This is a required field.', 'daypick-for-contact-form-7' ); ?>
		</label>
	</fieldset>

	<fieldset>
		<legend><?php echo esc_html__( 'Field name', 'daypick-for-contact-form-7' ); ?></legend>
		<input type="text" data-tag-part="name" pattern="[A-Za-z][A-Za-z0-9_\-]*" />
	</fieldset>

	<fieldset>
		<legend><?php echo esc_html__( 'Picker mode', 'daypick-for-contact-form-7' ); ?></legend>
		<select data-tag-part="option" data-tag-option="mode:">
			<option value=""><?php echo esc_html__( 'Date (default)', 'daypick-for-contact-form-7' ); ?></option>
			<option value="time"><?php echo esc_html__( 'Time only', 'daypick-for-contact-form-7' ); ?></option>
			<option value="datetime"><?php echo esc_html__( 'Date and time', 'daypick-for-contact-form-7' ); ?></option>
		</select>
	</fieldset>

	<fieldset>
		<legend><?php echo esc_html__( 'Date range', 'daypick-for-contact-form-7' ); ?></legend>
		<div class="daypick-range">
			<label>
				<?php echo esc_html__( 'Min', 'daypick-for-contact-form-7' ); ?>
				<input type="date" data-tag-part="option" data-tag-option="min:" />
			</label>
			<span class="daypick-range-sep" aria-hidden="true">&#8660;</span>
			<label>
				<?php echo esc_html__( 'Max', 'daypick-for-contact-form-7' ); ?>
				<input type="date" data-tag-part="option" data-tag-option="max:" />
			</label>
		</div>
		<label class="daypick-check">
			<input type="checkbox" data-tag-part="option" data-tag-option="min:today" />
			<?php echo esc_html__( 'Disallow past dates (min: today)', 'daypick-for-contact-form-7' ); ?>
		</label>
		<span class="description"><?php echo esc_html__( 'Relative values such as max:+90d can be added by editing the tag manually.', 'daypick-for-contact-form-7' ); ?></span>
	</fieldset>

	<fieldset>
		<legend><?php echo esc_html__( 'Allowed hours (time and date-time modes)', 'daypick-for-contact-form-7' ); ?></legend>
		<div class="daypick-row">
			<label>
				<?php echo esc_html__( 'Range', 'daypick-for-contact-form-7' ); ?>
				<input type="text" data-tag-part="option" data-tag-option="hours:" pattern="([01][0-9]|2[0-3]):[0-5][0-9]-([01][0-9]|2[0-3]):[0-5][0-9]" placeholder="09:00-18:00" />
			</label>
			<label class="daypick-step">
				<?php echo esc_html__( 'Minute step', 'daypick-for-contact-form-7' ); ?>
				<input type="number" data-tag-part="option" data-tag-option="step:" min="1" max="60" />
			</label>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo esc_html__( 'Disabled days', 'daypick-for-contact-form-7' ); ?></legend>
		<label class="daypick-check">
			<input type="checkbox" data-tag-part="option" data-tag-option="disable:weekends" />
			<?php echo esc_html__( 'Disable weekends', 'daypick-for-contact-form-7' ); ?>
		</label>
		<label>
			<?php echo esc_html__( 'Disable specific dates (comma separated)', 'daypick-for-contact-form-7' ); ?>
			<input type="text" data-tag-part="option" data-tag-option="disable:" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}(,[0-9]{4}-[0-9]{2}-[0-9]{2})*" placeholder="2026-07-15,2026-07-16" />
		</label>
	</fieldset>

	<fieldset>
		<legend><?php echo esc_html__( 'First day of week', 'daypick-for-contact-form-7' ); ?></legend>
		<select data-tag-part="option" data-tag-option="firstday:">
			<option value=""><?php echo esc_html__( 'Site default', 'daypick-for-contact-form-7' ); ?></option>
			<option value="0"><?php echo esc_html__( 'Sunday', 'daypick-for-contact-form-7' ); ?></option>
			<option value="1"><?php echo esc_html__( 'Monday', 'daypick-for-contact-form-7' ); ?></option>
		</select>
	</fieldset>

	<fieldset>
		<legend><?php echo esc_html__( 'Displayed format', 'daypick-for-contact-form-7' ); ?></legend>
		<input type="text" data-tag-part="option" data-tag-option="format:" pattern="[A-Za-z0-9.,:/\-_]*" placeholder="d.m.Y" />
		<span class="description"><?php echo esc_html__( 'PHP date format for display only; defaults to the site date/time format. Use an underscore instead of a space (e.g. d.m.Y_H:i). The submitted value is always ISO.', 'daypick-for-contact-form-7' ); ?></span>
	</fieldset>

	<fieldset>
		<legend><?php echo esc_html__( 'Default value', 'daypick-for-contact-form-7' ); ?></legend>
		<input type="text" data-tag-part="value" />
		<label class="daypick-check">
			<input type="checkbox" data-tag-part="option" data-tag-option="placeholder" />
			<?php echo esc_html__( 'Use this text as the placeholder of the field', 'daypick-for-contact-form-7' ); ?>
		</label>
	</fieldset>

	<fieldset>
		<legend><?php echo esc_html__( 'Class attribute', 'daypick-for-contact-form-7' ); ?></legend>
		<input type="text" data-tag-part="option" data-tag-option="class:" pattern="[A-Za-z0-9_\-\s]*" />
	</fieldset>
	</div>
</div>

<footer class="insert-box daypick-insert">
	<div class="flex-container">
		<input type="text" class="code selectable" readonly="readonly" data-tag-part="tag"
			aria-label="<?php echo esc_attr__( 'The form-tag to be inserted into the form template', 'daypick-for-contact-form-7' ); ?>" />
		<button type="button" class="button button-primary" data-taggen="insert-tag"><?php
			echo esc_html__( 'Insert Tag', 'daypick-for-contact-form-7' );
		?></button>
	</div>
	<p class="mail-tag-tip"><?php
		printf(
			/* translators: %s: mail-tag corresponding to the form-tag */
			esc_html__( 'To use the user input in the email, insert the corresponding mail-tag %s into the email template.', 'daypick-for-contact-form-7' ),
			'<strong data-tag-part="mail-tag"></strong>'
		);
	?></p>
</footer>
</div>
		<?php
	}
}
