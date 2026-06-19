=== DayPick: Date Fields for Contact Form 7 ===
Contributors:      wolinka
Tags:              contact form 7, date picker, time picker, datetime, booking
Requires at least: 6.0
Tested up to:      7.0
Requires PHP:      7.4
Requires Plugins:  contact-form-7
Stable tag:        1.0.1
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Modern, mobile-friendly date & time picker for Contact Form 7. All features free, all translations included, no license keys.

== Description ==

DayPick adds a `[daypick]` form-tag to Contact Form 7: a fast, mobile-friendly date and time picker built on the flatpickr library.

**Everything is free.** No pro version, no license keys, no per-domain restrictions, no paid translations. 50+ languages ship with the plugin and are picked automatically from your site language.

= Why DayPick? =

* **Local display, ISO storage** – the visitor sees the date in their own format (e.g. 15.07.2026), but the form always submits a clean ISO value (2026-07-15). No more AM/PM confusion or broken database exports.
* **Server-side validation** – min/max dates, disabled days and hour ranges are enforced on the server too, not just in the browser.
* **Automatic language** – the picker follows your WordPress site language out of the box. All 50+ flatpickr locales are bundled.
* **Visual tag generator** – add a field from the Contact Form 7 editor without writing any code.
* **Loads only when needed** – assets are enqueued only on pages that actually render a DayPick field, with `defer` strategy.
* **Theme-safe styling** – isolated popup CSS prevents theme conflicts.

= Usage =

Add a tag to your form, either with the **DayPick** button in the form editor or manually:

`[daypick* appointment mode:datetime min:today max:+90d hours:09:00-18:00 step:30 disable:weekends firstday:1]`

Available options:

* `mode:date` / `mode:time` / `mode:datetime` – picker type (default: date)
* `min:today`, `min:2026-07-01`, `min:+7d` – earliest selectable date
* `max:+90d`, `max:2026-09-30` – latest selectable date
* `hours:09:00-18:00` – allowed time range
* `step:30` – minute increment
* `disable:weekends`, `disable:2026-07-15,2026-07-16` – disabled days (can be combined)
* `firstday:1` – first day of week (0 = Sunday, 1 = Monday; default: site setting)
* `format:d.m.Y` – display format (PHP date tokens; use `_` for a space, e.g. `format:d.m.Y_H:i`)
* `"Select a date" placeholder` – show the quoted text as the field placeholder; without `placeholder` the same text becomes a pre-filled default value

The submitted value is always ISO: `Y-m-d`, `H:i` or `Y-m-d H:i` depending on the mode. Use the regular mail-tag (e.g. `[appointment]`) in your email template.

= Credits =

DayPick bundles the [flatpickr](https://flatpickr.js.org/) library (v4.6.13), released under the MIT license, which is GPL-compatible. The non-minified source code is available in the [flatpickr GitHub repository](https://github.com/flatpickr/flatpickr/tree/v4.6.13).

== Installation ==

1. Install and activate Contact Form 7.
2. Go to Plugins → Add New, search for "DayPick", install and activate.
3. Edit a form and use the **date/time picker (DayPick)** button, or add a `[daypick your-field]` tag manually.

== Frequently Asked Questions ==

= Do I need a license key or a pro version? =

No. Everything – all features, all translations – is free.

= Which languages are supported? =

All 50+ locales bundled with flatpickr (Turkish, German, French, Spanish, Arabic, Japanese and many more). The picker automatically follows your site language. You can override it with the `daypick_locale` filter.

= What value is sent in the email? =

By default the ISO value (e.g. `2026-07-15 14:30`). If you set a display format on the tag (e.g. `format:d.m.Y_H:i`), the email uses that same format (e.g. `15.07.2026 14:30`), localized to your site language and timezone. Stored/exported data (database, CFDB7) always stays ISO for consistency.

= Can visitors bypass the date restrictions? =

No. min/max, disabled days, hour range and minute step are validated on the server during submission, not only in the browser.

= Does it work on mobile? =

Yes. DayPick renders the same picker on mobile devices so that your rules (disabled days, hour ranges) keep working everywhere.

= Can I add placeholder text? =

Yes. In the tag generator, fill in **Default value** and tick *Use this text as the placeholder of the field*, or add it manually: `[daypick your-field "Select a date" placeholder]`. The placeholder is shown on the visible field; the submitted value stays ISO.

== Screenshots ==

1. Date picker on the front end.
2. Date & time picker with a restricted hour range.
3. Tag generator panel in the Contact Form 7 editor.

== Changelog ==

= 1.0.1 =
* Updated the plugin display name.

= 1.0.0 =
* Initial release: date, time and datetime modes; min/max limits; disabled weekends/dates; hour range and minute step; automatic locale; visual tag generator; server-side validation; ISO submission values.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
