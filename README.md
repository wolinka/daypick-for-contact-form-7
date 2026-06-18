🇹🇷 [Türkçe (Turkish)](README-tr.md) | 🇬🇧 **English**

---

# DayPick Date Fields for Contact Form 7

A lightweight plugin that adds a modern, mobile-friendly date & time picker to Contact Form 7. Built on [flatpickr](https://flatpickr.js.org/), with no license keys, no pro version, and no paid translations.

## 🚀 Features

- **Local display, ISO storage:** The visitor sees the date in their own format (e.g. `15.07.2026`), but the form always submits a clean ISO value (`2026-07-15`). No more AM/PM confusion or broken database exports.
- **Server-side validation:** min/max dates, disabled days and hour ranges are enforced on the server too, not just in the browser — the rules cannot be bypassed.
- **Automatic language:** The picker follows your WordPress site language out of the box. All 50+ flatpickr locales ship with the plugin.
- **Visual tag generator:** Add a field from the Contact Form 7 editor without writing any code.
- **Loads only when needed:** Assets are enqueued only on pages that actually render a DayPick field, with a `defer` strategy.
- **Theme-safe styling:** Isolated popup CSS prevents theme conflicts.

## ⚙️ Installation

1. Install and activate Contact Form 7.
2. Go to **Plugins → Add New**, search for "DayPick", install and activate (or copy this repository into `wp-content/plugins/`).
3. Edit a form and use the **date/time picker (DayPick)** button, or add a `[daypick your-field]` tag manually.

## 📝 Usage

Add a tag to your form, either with the **DayPick** button in the form editor or manually:

```
[daypick* appointment mode:datetime min:today max:+90d hours:09:00-18:00 step:30 disable:weekends firstday:1]
```

### Available options

| Option | Description |
| --- | --- |
| `mode:date` / `mode:time` / `mode:datetime` | Picker type (default: `date`) |
| `min:today`, `min:2026-07-01`, `min:+7d` | Earliest selectable date |
| `max:+90d`, `max:2026-09-30` | Latest selectable date |
| `hours:09:00-18:00` | Allowed time range |
| `step:30` | Minute increment (1–60) |
| `disable:weekends`, `disable:2026-07-15,2026-07-16` | Disabled days (can be combined) |
| `firstday:1` | First day of week (0 = Sunday, 1 = Monday; default: site setting) |
| `format:d.m.Y` | Display format (PHP date tokens; use `_` for a space, e.g. `format:d.m.Y_H:i`) |
| `"Select a date" placeholder` | Shows the quoted text as the field placeholder; without `placeholder` the same text becomes a pre-filled default value |

The submitted value is always ISO: `Y-m-d`, `H:i` or `Y-m-d H:i` depending on the mode. Use the regular mail-tag (e.g. `[appointment]`) in your email template.

## ❓ Frequently Asked Questions

**Do I need a license key or a pro version?**
No. Everything — all features, all translations — is free.

**What value is sent in the email?**
By default the ISO value (e.g. `2026-07-15 14:30`). If you set a display format on the tag (e.g. `format:d.m.Y_H:i`), the email uses that same format (e.g. `15.07.2026 14:30`), localized to your site language and timezone. Stored/exported data (database, CFDB7) always stays ISO for consistency.

**Can visitors bypass the date restrictions?**
No. min/max, disabled days, hour range and minute step are validated on the server during submission, not only in the browser.

**Does it work on mobile?**
Yes. DayPick renders the same picker on mobile devices so that your rules (disabled days, hour ranges) keep working everywhere.

## 🔌 Credits

DayPick bundles the MIT-licensed (GPL-compatible) [flatpickr](https://flatpickr.js.org/) library (v4.6.13). The non-minified source code is available in the [flatpickr GitHub repository](https://github.com/flatpickr/flatpickr/tree/v4.6.13).

## 📄 License

GPLv2 or later — [GNU GPL v2](https://www.gnu.org/licenses/gpl-2.0.html).

## 🤝 Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change!

---

Built by [Özlem Çimen](https://www.linkedin.com/in/ozlemcimen/) — Enterprise WordPress consulting at [Wolinka](https://wolinka.com)
