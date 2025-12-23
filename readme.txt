=== qTap Starter ===
Contributors: kdctek
Tags: starter, template, development, dashboard
Requires at least: 5.8
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.4.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A starter template for building qTap App child plugins. Requires qTap App.

== Description ==

qTap Starter is a template plugin for developers building child plugins for the qTap App ecosystem. It provides a complete starting point with:

* Tabbed settings interface (General, Import/Export, Data Management)
* Import/Export functionality for settings backup
* Data retention controls with user consent
* WordPress Coding Standards compliance
* WooCommerce HPOS compatibility
* Parent plugin integration hooks
* WCAG Level AAA accessibility support (when enabled in parent)

**Requires qTap App**

This plugin requires the qTap App parent plugin to be installed and activated. It will display an admin notice if the parent plugin is missing.

**For Developers**

Use this template as a starting point for your own qTap App child plugins. The template includes:

* Proper dependency checking
* Integration with qTap App dashboard
* Parent plugin hooks for export/import
* Accessibility mode integration
* Comprehensive inline documentation

== Installation ==

1. Ensure qTap App is installed and activated
2. Upload the `kdc-qtap-starter` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure settings under qTap → Starter

== Frequently Asked Questions ==

= Does this plugin require qTap App? =

Yes, qTap Starter requires the qTap App parent plugin to be installed and activated.

= How do I enable accessibility mode? =

Accessibility mode is controlled by the parent qTap App plugin. Enable it in qTap → Common Settings → Accessibility Mode.

= Will my data be deleted when I uninstall? =

By default, data is preserved when the plugin is uninstalled. You can enable data deletion in the Data Management tab or through the parent qTap App settings.

== Changelog ==

= 1.4.0 =
* Changed: Removed Import/Export and Data Management tabs (now centralized in parent qTap App)
* Changed: Settings page now only shows plugin-specific settings
* Added: Link to parent plugin's Data Management for export/import features
* Updated: Child plugins should use parent plugin's hooks for data management

= 1.3.2 =
* Fixed: WordPress 6.7+ translation loading notice (_load_textdomain_just_in_time)
* Changed: Moved register_with_qtap from kdc_qtap_loaded hook to init hook
* Removed: Unused load_textdomain method

= 1.3.1 =
* Fixed: Removed load_plugin_textdomain() per WordPress.org guidelines
* Fixed: Removed Domain Path header (not needed for WordPress.org hosted plugins)
* Fixed: Prefixed all global variables in uninstall.php
* Fixed: Removed hidden files (.gitkeep, .gitignore) from plugin package
* Added: readme.txt for WordPress.org compliance

= 1.3.0 =
* Added: Parent plugin integration for export/import
* Added: Accessibility mode integration with qTap App
* Added: WCAG Level AAA compliant CSS (admin-accessible.css)
* Added: Conditional CSS loading based on parent settings
* Added: Parent data removal hook support
* Improved: Data Management tab shows parent setting status
* Updated: CLAUDE.md with parent plugin integration documentation

= 1.2.0 =
* Added: Dependency requirement for qTap App
* Added: Admin notice when parent plugin is missing
* Added: Integration with qTap App dashboard
* Added: WooCommerce HPOS compatibility declaration

= 1.0.4 =
* Added: Tabbed settings interface
* Added: Import/Export functionality
* Added: Data Management tab with retention controls
* Added: Backup prompts for data deletion

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.4.0 =
Data Management features moved to parent qTap App plugin. Requires qTap App v1.4.0+.

= 1.3.2 =
Fixes WordPress 6.7+ translation loading notice. Recommended update for all users.

= 1.3.1 =
WordPress.org Plugin Checker compliance fixes. No functional changes.

= 1.3.0 =
New parent plugin integration for centralized export/import and accessibility mode. Update qTap App to v1.4.0 for full compatibility.
