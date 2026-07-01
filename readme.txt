=== ART Starter ===
Contributors: artbashlykov
Tags: starter, onboarding, landing page, link in bio, 404
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.10
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Configure technical site settings, a link-in-bio homepage with 7 templates, and a custom 404 page.

== Description ==

ART Starter helps you quickly configure the technical side of a new WordPress site, set up a homepage as a handy link-in-bio page with seven templates, and customize the 404 page.

Main features:

* Initial setup wizard: site title, favicon, technical options, and demo content cleanup
* Technical settings: permalinks, HTTPS URLs, comments, pingbacks, and registration
* Link-in-bio style homepage with profile, CTA, links, recommendation block, and socials
* Seven color templates with live admin preview
* Custom 404 page with image, text, and buttons
* Front-end layouts isolated from the active theme styles

== Installation ==

1. Upload the `art-starter` folder to `/wp-content/plugins/` or install the plugin through the WordPress admin.
2. Activate the plugin on the Plugins screen.
3. Open **Settings → ART Starter** in the admin and configure the homepage or 404 page.

== Frequently Asked Questions ==

= What is ART Starter for? =

ART Starter is for people who want a fast launch: technical WordPress settings, a simple homepage with seven templates, and a polished 404 page without building a full theme.

= Does the plugin depend on my theme? =

The homepage and custom 404 page use self-contained layouts. When enabled, ART Starter removes active theme styles on those pages so the design stays consistent.

== Changelog ==

= 1.0.10 =
* Fix false success when Hello Dolly and other default plugins were not actually deleted.
* Verify plugin removal on disk, clear plugin cache, and retry filesystem delete.

= 1.0.9 =
* Fix demo plugin and theme cleanup (active Akismet, filesystem init, theme detection).
* Fix «Hello world» / «Привет, мир!» post detection on Russian WordPress installs.

= 1.0.8 =
* Fix duplicate save notice on homepage and 404 settings tabs.

= 1.0.7 =
* Tabbed admin UI: setup, homepage, and 404 on one screen.
* Move ART Starter to **Settings** in the WordPress admin.
* Optional social network labels under homepage icons (12px).
* Optional full data cleanup on plugin uninstall.
* Remove the old dashboard and unused legacy admin code.

= 1.0.6 =
* Align GitHub update checker with ART PUC standards (User-Agent, release zip asset filter).

= 1.0.5 =
* Sync plugin descriptions on the dashboard, README, and readme.txt.
* Technical settings checkboxes are unchecked by default.
* Shorten the HTTPS option hint in initial setup.

= 1.0.4 =
* Add optional HTTPS switch for site and WordPress URLs in initial setup (Settings → General).
* Update plugin description.
* Add deploy-exclude.txt for WordPress.org packaging; fix Plugin Check issues in the release build script.

= 1.0.3 =
* Rename admin screen «Первичные настройки» to «Настройки».
* Add optional removal of inactive default plugins (Akismet, Hello Dolly) in initial setup.
* Auto-uncheck «use as homepage» when a static page is chosen in Settings → Reading.
* Prevent homepage mode from fighting WordPress Reading settings.

= 1.0.2 =
* Fix homepage links saving with split label/URL rows after form submit.
* Re-index link fields on add, remove, reorder, and save.
* Auto-repair legacy split link rows when loading settings.

= 1.0.1 =
* Fix 404 extra button icon and URL persistence when adding buttons.
* Fix admin icon picker selection on homepage and 404 settings.
* Add site favicon picker to the initial setup wizard.
* Add PHP release zip build script for GitHub releases.

= 1.0.0 =
* Initial release: admin dashboard, initial setup wizard, homepage and 404 settings, color templates, and update checker hooks.
