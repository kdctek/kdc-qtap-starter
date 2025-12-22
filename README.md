# qTap Starter

A starter template for building [qTap App](https://qtap.app) child plugins. Use this as a foundation for creating your own qTap apps for WordPress and WooCommerce.

**Author:** [KDC](https://kdc.in)  
**GitHub:** [https://github.com/kdctek/kdc-qtap-starter](https://github.com/kdctek/kdc-qtap-starter)

## Features

- ✅ WordPress Coding Standards compliant
- ✅ Seamless integration with qTap App dashboard
- ✅ Shared fallback menu (no duplicates when multiple apps active)
- ✅ WooCommerce HPOS compatible
- ✅ Translation ready (i18n)
- ✅ Proper activation/deactivation/uninstall hooks
- ✅ Settings API with sanitization and validation
- ✅ Admin assets (CSS/JS) with localization
- ✅ Extensible with hooks and filters

## Requirements

- WordPress 5.8+
- PHP 7.4+
- WooCommerce 5.0+ (if using WC features)

## Installation

### As a Starting Point

1. Clone or download this repository
2. Rename the folder from `kdc-qtap-starter` to `kdc-qtap-{your-app-name}`
3. Run the search and replace (see below)
4. Start building your app!

### Search and Replace

Replace the following strings throughout all files:

| Find | Replace With |
|------|--------------|
| `kdc-qtap-starter` | `kdc-qtap-{your-app-slug}` |
| `kdc_qtap_starter` | `kdc_qtap_{your_app_slug}` |
| `KDC_QTAP_STARTER` | `KDC_QTAP_{YOUR_APP_SLUG}` |
| `KDC_qTap_Starter` | `KDC_qTap_{YourAppName}` |
| `Starter` | `{Your App Name}` |
| `starter` | `{your-app-slug}` |
| `🚀` | `{your-emoji}` |

## File Structure

```
kdc-qtap-starter/
├── kdc-qtap-starter.php              # Main plugin file
├── uninstall.php                     # Cleanup on deletion
├── composer.json                     # Dev dependencies
├── phpcs.xml.dist                    # WordPress Coding Standards config
├── .gitignore                        # Git ignore rules
├── README.md                         # This file
├── CLAUDE.md                         # Claude AI prompt template
├── assets/
│   ├── css/
│   │   └── admin.css                 # Admin styles
│   └── js/
│       └── admin.js                  # Admin scripts
├── includes/
│   ├── kdc-qtap-shared-menu.php      # Shared fallback menu functions
│   └── class-kdc-qtap-starter-admin.php  # Admin class
├── languages/
│   └── .gitkeep
└── templates/
    └── .gitkeep
```

## How It Works

### qTap App Integration

When qTap App is installed, this plugin:
1. Registers with the dashboard using `kdc_qtap_register_plugin()`
2. Appears in the qTap apps grid with icon, name, and settings link
3. Adds its settings page as a submenu under "qTap"

### Shared Fallback Menu (No Duplicates!)

When qTap App is NOT installed:
1. The first child plugin to load creates a shared "qTap" fallback menu
2. All other child plugins attach as submenus to this shared menu
3. **No duplicate menus** - even with multiple child plugins active
4. Uses `kdc_qtap_ensure_fallback_menu()` helper function

```php
// In your add_admin_menu() method:
$parent_slug = kdc_qtap_ensure_fallback_menu();

add_submenu_page(
    $parent_slug,  // Always use this - works with or without qTap App
    __( 'My App Settings', 'my-app' ),
    __( 'My App', 'my-app' ),
    'manage_options',
    'kdc-qtap-my-app',
    array( $this, 'render_settings_page' )
);
```

## Available Hooks

### Actions

| Hook | Description |
|------|-------------|
| `kdc_qtap_starter_activated` | Fires on plugin activation |
| `kdc_qtap_starter_deactivated` | Fires on plugin deactivation |
| `kdc_qtap_starter_uninstall` | Fires on plugin deletion |
| `kdc_qtap_starter_settings_saved` | Fires after settings are saved |
| `kdc_qtap_starter_before_settings_form` | Before settings form output |
| `kdc_qtap_starter_settings_fields` | Inside settings form (add fields) |
| `kdc_qtap_starter_after_settings_form` | After settings form output |
| `kdc_qtap_starter_admin_enqueue_scripts` | When admin scripts are enqueued |

### Filters

| Filter | Description |
|--------|-------------|
| `kdc_qtap_starter_save_settings` | Modify settings before saving |

## Development

### Coding Standards

This plugin follows WordPress Coding Standards. To check:

```bash
# Install PHP_CodeSniffer
composer global require squizlabs/php_codesniffer

# Install WordPress standards
composer global require wp-coding-standards/wpcs

# Set installed paths
phpcs --config-set installed_paths ~/.composer/vendor/wp-coding-standards/wpcs

# Check the plugin
phpcs --standard=WordPress /path/to/kdc-qtap-starter
```

### Building for Production

```bash
# Create a production ZIP (excludes dev files)
zip -r kdc-qtap-starter.zip kdc-qtap-starter \
  -x "*.git*" \
  -x "*.DS_Store" \
  -x "*node_modules*" \
  -x "*.md" \
  -x "CLAUDE.md"
```

## Changelog

### 1.0.3
- Added automated release scripts (`scripts/version-bump.sh`, `scripts/release.sh`)
- Added GitHub Actions workflow for automated releases
- Added RELEASING.md documentation
- Updated CLAUDE.md with release prompts

### 1.0.2
- Updated Plugin URI to GitHub repository
- Added WordPress Tested up to 6.9

### 1.0.1
- Fixed duplicate menu issue when multiple child plugins active without qTap App
- Added shared fallback menu system (`kdc-qtap-shared-menu.php`)
- Updated author to KDC (https://kdc.in)

### 1.0.0
- Initial release

## License

GPL v2 or later

## Support

- **Website:** [https://qtap.app](https://qtap.app)
- **Documentation:** [https://qtap.app/docs](https://qtap.app/docs)
- **GitHub Issues:** [Report bugs or request features](https://github.com/kdctek/kdc-qtap-starter/issues)

---

## Creating Your First App

1. **Clone the starter:**
   ```bash
   git clone https://github.com/kdctek/kdc-qtap-starter.git kdc-qtap-myapp
   cd kdc-qtap-myapp
   rm -rf .git
   git init
   ```

2. **Run search/replace** (use your editor's find & replace across all files)

3. **Update plugin header** in main PHP file with your app details

4. **Modify the settings page** in `includes/class-kdc-qtap-*-admin.php`

5. **Add your functionality** in new class files under `includes/`

6. **Test with and without qTap App** to ensure both modes work

7. **Submit to qTap registry** at https://qtap.app/submit
