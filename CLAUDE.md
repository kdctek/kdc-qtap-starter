# Claude AI Prompt Template

Use this prompt to get Claude to help you create a new qTap App child plugin based on this starter template.

**GitHub Repository:** [https://github.com/kdctek/kdc-qtap-starter](https://github.com/kdctek/kdc-qtap-starter)

---

## ⚠️ Important: Check Latest Guidelines

**Before creating or modifying any qTap App child plugin, Claude MUST fetch and review the latest CLAUDE.md from GitHub:**

```
https://raw.githubusercontent.com/kdctek/kdc-qtap-starter/refs/heads/main/CLAUDE.md
```

This ensures:
- Compatibility with the latest qTap Starter template
- Adherence to current WordPress Coding Standards
- Use of updated hooks, filters, and best practices
- Awareness of any new features or deprecated patterns

**Claude should:**
1. Fetch the latest CLAUDE.md from the URL above
2. Compare with any local/provided version
3. Follow the most recent guidelines
4. Notify the user if significant differences exist

---

## Starting Prompt

Copy and paste this prompt to Claude:

```
I want to create a new qTap App child plugin for WordPress. Please help me build it based on the qTap Starter template.

**IMPORTANT:** First, fetch and review the latest guidelines from:
https://raw.githubusercontent.com/kdctek/kdc-qtap-starter/refs/heads/main/CLAUDE.md

## My App Details

- **App Name:** [Your App Name]
- **App Slug:** [your-app-slug] (lowercase, hyphens only)
- **Description:** [What your app does]
- **Icon:** [Emoji like 📧 or dashicon like dashicons-email]
- **Category:** [woocommerce / communication / security / analytics / utilities]

## Features I Need

[Describe the features your app should have. Be specific about:]

1. [Feature 1 - what it does]
2. [Feature 2 - what it does]
3. [etc.]

## Settings Required

[List the settings your app needs:]

- [Setting 1]: [type: text/checkbox/select/number] - [description]
- [Setting 2]: [type] - [description]

## Additional Context

[Any other requirements, integrations, or context Claude should know]

---

Please create the plugin following the latest CLAUDE.md guidelines, including:
1. WordPress Coding Standards compliance (TABS, PHPDoc, escaping, sanitization)
2. Plugin URI format: https://qtap.app/app/{slug} (e.g., kdc-qtap-mobile → https://qtap.app/app/mobile)
3. Proper integration with qTap App dashboard (register with dashboard when available)
4. Shared fallback menu support (uses kdc_qtap_ensure_fallback_menu() - no duplicate menus)
5. Tabbed settings interface (General, Import/Export, Data Management)
6. Import/Export functionality for settings backup as JSON
7. Data retention: preserve user data on uninstall by default
8. "Delete data on uninstall" option with backup prompt and irreversible warning
9. Use WordPress core CSS classes for admin UI (button, form-table, notice, nav-tab, etc.)
10. Minimal custom CSS - only when WordPress core classes don't exist
11. All necessary files: main plugin file, admin class, shared menu helper, CSS, JS, uninstall.php
12. Proper sanitization, escaping, and nonce verification
13. Translation-ready strings
14. Appropriate hooks and filters for extensibility
15. Author: KDC (https://kdc.in)
```

---

## Example Prompt

Here's a filled-in example:

```
I want to create a new qTap App child plugin for WordPress. Please help me build it based on the qTap Starter template.

**IMPORTANT:** First, fetch and review the latest guidelines from:
https://raw.githubusercontent.com/kdctek/kdc-qtap-starter/refs/heads/main/CLAUDE.md

## My App Details

- **App Name:** Email
- **App Slug:** email
- **Description:** Send transactional emails via SMTP with logging and templates
- **Icon:** 📧
- **Category:** communication

## Features I Need

1. SMTP configuration (host, port, username, password, encryption)
2. Email logging with status tracking (sent, failed, pending)
3. Test email functionality
4. Email templates with shortcode support
5. Retry failed emails

## Settings Required

- smtp_host: text - SMTP server hostname
- smtp_port: number - SMTP port (25, 465, 587)
- smtp_username: text - SMTP authentication username
- smtp_password: password - SMTP authentication password
- smtp_encryption: select (none/ssl/tls) - Connection encryption
- from_email: email - Default sender email
- from_name: text - Default sender name
- enable_logging: checkbox - Enable email logging
- log_retention_days: number - Days to keep logs

## Additional Context

- Should hook into wp_mail for all WordPress emails
- Need a custom database table for email logs
- Admin page should have tabs: Settings, Logs, Test, Templates
- Compatible with WooCommerce order emails

---

Please create the plugin with:
1. WordPress Coding Standards compliance
2. Proper integration with qTap App dashboard
3. Standalone mode
4. All necessary files
5. Proper security
6. Translation-ready
7. Extensible hooks
```

---

## Follow-up Prompts

After Claude generates the initial plugin, you can use these follow-up prompts.

> **Tip:** For any modifications, remind Claude to check the latest guidelines:
> `First, check https://raw.githubusercontent.com/kdctek/kdc-qtap-starter/refs/heads/main/CLAUDE.md for the latest standards.`

### Add a New Feature
```
First, check https://raw.githubusercontent.com/kdctek/kdc-qtap-starter/refs/heads/main/CLAUDE.md for the latest standards.

Please add [feature description] to the qTap [App Name] plugin. 
It should [detailed requirements].
```

### Add Database Table
```
I need a custom database table for the qTap [App Name] plugin to store [data type].
Fields needed: [list fields with types].
Include proper table creation on activation and cleanup on uninstall (respecting delete_data_on_uninstall setting).
```

### Add REST API Endpoint
```
Please add a REST API endpoint to the qTap [App Name] plugin.
- Route: /wp-json/kdc-qtap-[slug]/v1/[endpoint]
- Method: [GET/POST/PUT/DELETE]
- Purpose: [what it does]
- Parameters: [list parameters]
- Response: [expected response format]
```

### Add WooCommerce Integration
```
Please add WooCommerce integration to the qTap [App Name] plugin.
It should [describe integration] and hook into [specific WooCommerce hooks].
```

### Add AJAX Handler
```
Please add an AJAX handler to the qTap [App Name] plugin.
- Action: kdc_qtap_[slug]_[action]
- Purpose: [what it does]
- Parameters: [list parameters]
- Include proper nonce verification and capability checks.
```

### Add Cron Job
```
Please add a scheduled task to the qTap [App Name] plugin.
- Schedule: [hourly/daily/weekly or custom interval]
- Purpose: [what it does]
- Include proper scheduling on activation and cleanup on deactivation.
```

### Extend Import/Export
```
Please extend the Import/Export functionality for qTap [App Name] plugin.
I need to also export/import:
- [Data type 1]: [description]
- [Data type 2]: [description]

Use the existing hooks:
- kdc_qtap_[slug]_export_data filter to add data to export
- kdc_qtap_[slug]_before_import action to process custom import data
- kdc_qtap_[slug]_after_import action to finalize import
```

### Add Custom Data to Backup
```
Please add [data type] to the backup/export functionality.
The data is stored in: [option name / custom table / etc.]
Ensure it's properly included in JSON export and restored on import.
```

### Extend Data Management
```
Please extend the Data Management tab to include:
- [Additional option 1]: [description]
- [Additional option 2]: [description]

Also add cleanup for [additional data] in uninstall.php when delete option is enabled.
```

---

## Built-in Features (v1.0.4+)

The starter template includes these features out of the box:

### Tabbed Settings Interface

Three default tabs:
1. **General** - Main plugin settings
2. **Import / Export** - Backup and restore functionality
3. **Data Management** - Data retention controls

Add custom tabs using the filter:
```php
add_filter( 'kdc_qtap_starter_admin_tabs', function( $tabs ) {
    $tabs['custom'] = __( 'Custom Tab', 'kdc-qtap-starter' );
    return $tabs;
} );
```

### Import/Export System

**Export Features:**
- Download settings as JSON file
- Copy settings to clipboard
- Includes plugin version and site URL for reference

**Import Features:**
- Upload JSON backup file
- Validates file format and plugin compatibility
- Confirms before overwriting existing settings

**Extend Export Data:**
```php
add_filter( 'kdc_qtap_starter_export_data', function( $data ) {
    // Add custom data to export
    $data['data']['custom_table'] = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}my_table" );
    return $data;
} );
```

**Process Custom Import:**
```php
add_action( 'kdc_qtap_starter_after_import', function( $import_data ) {
    if ( isset( $import_data['data']['custom_table'] ) ) {
        // Restore custom table data
        foreach ( $import_data['data']['custom_table'] as $row ) {
            $wpdb->insert( "{$wpdb->prefix}my_table", $row );
        }
    }
} );
```

### Data Retention System

**Default Behavior:** Data is PRESERVED on uninstall (safe by default)

**User Control:** "Delete data on uninstall" option in Data Management tab

**When Delete is Enabled:**
1. Warning box appears explaining what will be deleted
2. Backup download link is prominently displayed
3. Confirmation dialogs warn about irreversible action
4. `uninstall.php` checks the setting before deleting

**Add Custom Data to Cleanup:**
```php
// In uninstall.php, add your cleanup code:

// Delete custom options
delete_option( 'kdc_qtap_myapp_custom_option' );

// Delete custom tables
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}kdc_qtap_myapp_logs" );

// Delete user meta
delete_metadata( 'user', 0, 'kdc_qtap_myapp_prefs', '', true );
```

---

## File Naming Convention (WordPress Coding Standards)

All files in qTap App child plugins MUST follow WordPress Coding Standards and use the `kdc-qtap-{slug}-` prefix consistently.

### Plugin Folder Structure

```
kdc-qtap-{slug}/
├── kdc-qtap-{slug}.php              # Main plugin file
├── uninstall.php                     # Uninstall handler
├── includes/
│   ├── kdc-qtap-shared-menu.php     # Shared fallback menu (same in all plugins)
│   ├── class-kdc-qtap-{slug}-admin.php
│   ├── class-kdc-qtap-{slug}-settings.php
│   ├── class-kdc-qtap-{slug}-{feature}.php
│   └── helper-functions.php          # Optional helper functions
├── assets/
│   ├── css/
│   │   ├── kdc-qtap-{slug}-admin.css
│   │   └── kdc-qtap-{slug}-frontend.css
│   └── js/
│       ├── kdc-qtap-{slug}-admin.js
│       └── kdc-qtap-{slug}-frontend.js
├── templates/
│   └── kdc-qtap-{slug}-{template-name}.php
└── languages/
    └── kdc-qtap-{slug}.pot
```

### File Naming Rules

| File Type | Pattern | Example (for `mobile` slug) |
|-----------|---------|----------------------------|
| **Main plugin file** | `kdc-qtap-{slug}.php` | `kdc-qtap-mobile.php` |
| **Class files** | `class-kdc-qtap-{slug}-{name}.php` | `class-kdc-qtap-mobile-admin.php` |
| **Shared menu** | `kdc-qtap-shared-menu.php` | `kdc-qtap-shared-menu.php` (same in all) |
| **Admin CSS** | `kdc-qtap-{slug}-admin.css` | `kdc-qtap-mobile-admin.css` |
| **Admin JS** | `kdc-qtap-{slug}-admin.js` | `kdc-qtap-mobile-admin.js` |
| **Frontend CSS** | `kdc-qtap-{slug}-frontend.css` | `kdc-qtap-mobile-frontend.css` |
| **Frontend JS** | `kdc-qtap-{slug}-frontend.js` | `kdc-qtap-mobile-frontend.js` |
| **Templates** | `kdc-qtap-{slug}-{name}.php` | `kdc-qtap-mobile-form.php` |
| **Helper functions** | `helper-functions.php` | `helper-functions.php` |

### Class Naming (matches file names)

```php
// File: class-kdc-qtap-mobile-admin.php
class KDC_qTap_Mobile_Admin { ... }

// File: class-kdc-qtap-mobile-settings.php
class KDC_qTap_Mobile_Settings { ... }

// File: class-kdc-qtap-mobile-api.php
class KDC_qTap_Mobile_API { ... }
```

### Constants (defined in main plugin file)

```php
// All constants MUST be prefixed with KDC_QTAP_{SLUG}_
define( 'KDC_QTAP_MOBILE_VERSION', '1.0.0' );
define( 'KDC_QTAP_MOBILE_PLUGIN_FILE', __FILE__ );
define( 'KDC_QTAP_MOBILE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'KDC_QTAP_MOBILE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'KDC_QTAP_MOBILE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
```

### Function Naming

```php
// Global functions (if any) MUST be prefixed
function kdc_qtap_mobile_get_settings() { ... }
function kdc_qtap_mobile_is_enabled() { ... }

// Helper functions can use shorter prefix if in namespaced file
function qtap_mobile_format_phone( $phone ) { ... }
```

### Hook/Filter Naming

```php
// Actions
do_action( 'kdc_qtap_mobile_loaded' );
do_action( 'kdc_qtap_mobile_settings_saved', $settings );
do_action( 'kdc_qtap_mobile_before_render' );

// Filters
apply_filters( 'kdc_qtap_mobile_admin_tabs', $tabs );
apply_filters( 'kdc_qtap_mobile_settings', $settings );
apply_filters( 'kdc_qtap_mobile_capability', 'manage_options' );
```

### Option Names (stored in wp_options)

```php
// Use underscores, prefixed with kdc_qtap_{slug}_
'kdc_qtap_mobile_settings'
'kdc_qtap_mobile_version'
'kdc_qtap_mobile_delete_data_on_uninstall'
```

### User Meta Keys

```php
// Prefixed with kdc_qtap_{slug}_
'kdc_qtap_mobile_numbers'
'kdc_qtap_mobile_preferences'
```

### Database Tables

```php
// Use {$wpdb->prefix}kdc_qtap_{slug}_{table}
$wpdb->prefix . 'kdc_qtap_mobile_logs'
$wpdb->prefix . 'kdc_qtap_mobile_tokens'
```

### Enqueue Handles

```php
// CSS handles
wp_enqueue_style( 'kdc-qtap-mobile-admin', ... );
wp_enqueue_style( 'kdc-qtap-mobile-frontend', ... );

// JS handles
wp_enqueue_script( 'kdc-qtap-mobile-admin', ... );
wp_enqueue_script( 'kdc-qtap-mobile-frontend', ... );

// Localization object name
wp_localize_script( 'kdc-qtap-mobile-admin', 'kdcQtapMobileAdmin', array( ... ) );
```

### Nonce Actions

```php
// Prefixed nonce actions
wp_nonce_field( 'kdc_qtap_mobile_save_settings' );
wp_verify_nonce( $_POST['_wpnonce'], 'kdc_qtap_mobile_save_settings' );

// AJAX nonces
check_ajax_referer( 'kdc_qtap_mobile_ajax', 'nonce' );
```

### AJAX Actions

```php
// Admin AJAX
add_action( 'wp_ajax_kdc_qtap_mobile_save', array( $this, 'ajax_save' ) );

// Frontend AJAX (logged in)
add_action( 'wp_ajax_kdc_qtap_mobile_submit', array( $this, 'ajax_submit' ) );

// Frontend AJAX (not logged in)
add_action( 'wp_ajax_nopriv_kdc_qtap_mobile_submit', array( $this, 'ajax_submit' ) );
```

### REST API Namespaces

```php
// Namespace: kdc-qtap-{slug}/v1
register_rest_route( 'kdc-qtap-mobile/v1', '/settings', array( ... ) );
register_rest_route( 'kdc-qtap-mobile/v1', '/verify', array( ... ) );
```

### Transient Names

```php
// Prefixed transients
set_transient( 'kdc_qtap_mobile_cache', $data, HOUR_IN_SECONDS );
get_transient( 'kdc_qtap_mobile_cache' );
```

### Cron Hook Names

```php
// Prefixed cron hooks
add_action( 'kdc_qtap_mobile_cleanup', array( $this, 'run_cleanup' ) );
wp_schedule_event( time(), 'daily', 'kdc_qtap_mobile_cleanup' );
```

### Quick Reference Table

| Element | Prefix/Format | Example |
|---------|--------------|---------|
| Plugin folder | `kdc-qtap-{slug}` | `kdc-qtap-mobile` |
| Main file | `kdc-qtap-{slug}.php` | `kdc-qtap-mobile.php` |
| Class files | `class-kdc-qtap-{slug}-*.php` | `class-kdc-qtap-mobile-admin.php` |
| Class names | `KDC_qTap_{Slug}_*` | `KDC_qTap_Mobile_Admin` |
| Constants | `KDC_QTAP_{SLUG}_*` | `KDC_QTAP_MOBILE_VERSION` |
| Functions | `kdc_qtap_{slug}_*()` | `kdc_qtap_mobile_init()` |
| Hooks | `kdc_qtap_{slug}_*` | `kdc_qtap_mobile_loaded` |
| Options | `kdc_qtap_{slug}_*` | `kdc_qtap_mobile_settings` |
| CSS/JS handles | `kdc-qtap-{slug}-*` | `kdc-qtap-mobile-admin` |
| Text domain | `kdc-qtap-{slug}` | `kdc-qtap-mobile` |
| REST namespace | `kdc-qtap-{slug}/v1` | `kdc-qtap-mobile/v1` |
| DB tables | `{prefix}kdc_qtap_{slug}_*` | `wp_kdc_qtap_mobile_logs` |

---

## qTap Dashboard Registration

When qTap App (the main dashboard plugin) is installed, child plugins can register themselves to appear as **App Cards** on the qTap Dashboard.

### Registration Mechanism

The main qTap App plugin provides a global function `kdc_qtap_register_plugin()` that child plugins use to register themselves.

**How It Works:**

```
┌─────────────────────────────────────────────────────────────┐
│                      qTap App (Parent)                       │
│  ┌─────────────────────────────────────────────────────────┐│
│  │  Provides: kdc_qtap_register_plugin() function          ││
│  │  Stores: Registered apps in global array                ││
│  │  Displays: App Cards on Dashboard                       ││
│  └─────────────────────────────────────────────────────────┘│
│                            ▲                                 │
│           ┌────────────────┼────────────────┐               │
│           │                │                │               │
│  ┌────────┴───────┐ ┌──────┴──────┐ ┌──────┴──────┐        │
│  │ kdc-qtap-mobile│ │kdc-qtap-email│ │kdc-qtap-school│       │
│  │   (Child App)  │ │  (Child App) │ │  (Child App) │       │
│  └────────────────┘ └──────────────┘ └──────────────┘       │
└─────────────────────────────────────────────────────────────┘
```

### Registration Code

Add this to your main plugin class:

```php
/**
 * Initialize hooks.
 */
private function init_hooks() {
    // Register with qTap App dashboard.
    add_action( 'init', array( $this, 'register_with_qtap' ), 20 );
    
    // ... other hooks
}

/**
 * Register this app with qTap App dashboard.
 *
 * @since 1.0.0
 */
public function register_with_qtap() {
    // Only register if qTap App is active.
    if ( ! function_exists( 'kdc_qtap_register_plugin' ) ) {
        return;
    }

    kdc_qtap_register_plugin(
        array(
            'id'           => 'my-app',                                    // Unique ID (slug)
            'name'         => __( 'My App', 'kdc-qtap-my-app' ),           // Display name
            'description'  => __( 'Description of what app does.', 'kdc-qtap-my-app' ),
            'icon'         => '📧',                                        // Emoji or dashicon
            'settings_url' => admin_url( 'admin.php?page=kdc-qtap-my-app' ),
            'version'      => KDC_QTAP_MY_APP_VERSION,
            'is_active'    => true,                                        // App status
            'priority'     => 50,                                          // Display order (lower = first)
        )
    );
}
```

### Registration Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | string | ✅ | Unique identifier (slug), e.g., `'mobile'`, `'email'`, `'school'` |
| `name` | string | ✅ | Display name shown on App Card, e.g., `'Mobile'`, `'Email'` |
| `description` | string | ✅ | Short description shown on App Card |
| `icon` | string | ✅ | Emoji (e.g., `'📧'`) or Dashicon class (e.g., `'dashicons-email'`) |
| `settings_url` | string | ✅ | URL to the app's settings page |
| `version` | string | ❌ | Plugin version (shown on card) |
| `is_active` | bool | ❌ | Whether app is active, default `true` |
| `priority` | int | ❌ | Display order on dashboard, default `50` (lower = first) |
| `category` | string | ❌ | Category for grouping: `'woocommerce'`, `'communication'`, `'security'`, `'analytics'`, `'utilities'` |

### Icon Options

**Emoji Icons (Recommended):**
```php
'icon' => '📧',  // Email
'icon' => '📱',  // Mobile
'icon' => '🏫',  // School
'icon' => '🔒',  // Security
'icon' => '📊',  // Analytics
```

**Dashicon Icons:**
```php
'icon' => 'dashicons-email',
'icon' => 'dashicons-smartphone',
'icon' => 'dashicons-welcome-learn-more',
'icon' => 'dashicons-lock',
'icon' => 'dashicons-chart-bar',
```

### Priority Values

| Priority | Usage |
|----------|-------|
| 10-30 | Core/essential apps (shown first) |
| 40-60 | Standard apps (default) |
| 70-90 | Utility/secondary apps |
| 100+ | Low priority apps |

### Standalone Mode & Fallback Dashboard

When qTap App is NOT installed, child plugins should still show their cards on a fallback dashboard. This requires **two registrations**:

1. **qTap App Dashboard** - via `kdc_qtap_register_plugin()` (when qTap App installed)
2. **Fallback Dashboard** - via `kdc_qtap_fallback_dashboard_cards` filter (when qTap App NOT installed)

### Fallback Dashboard Registration

The fallback dashboard uses a filter hook for child plugins to register their cards:

```php
/**
 * Register with both dashboards.
 */
public function register_with_qtap() {
    // Register with qTap App dashboard (if installed).
    if ( function_exists( 'kdc_qtap_register_plugin' ) ) {
        kdc_qtap_register_plugin(
            array(
                'id'           => 'email',
                'name'         => __( 'Email', 'kdc-qtap-email' ),
                'description'  => __( 'SMTP email with logging.', 'kdc-qtap-email' ),
                'icon'         => '📧',
                'settings_url' => admin_url( 'admin.php?page=kdc-qtap-email' ),
                'version'      => KDC_QTAP_EMAIL_VERSION,
                'is_active'    => true,
                'priority'     => 30,
            )
        );
    }

    // ALWAYS register with fallback dashboard (shown when qTap App NOT installed).
    add_filter( 'kdc_qtap_fallback_dashboard_cards', array( $this, 'register_fallback_card' ) );
}

/**
 * Register card for fallback dashboard.
 *
 * @param  array $cards Existing cards.
 * @return array        Updated cards.
 */
public function register_fallback_card( $cards ) {
    $cards['email'] = array(
        'id'           => 'email',
        'name'         => __( 'Email', 'kdc-qtap-email' ),
        'description'  => __( 'SMTP email with logging.', 'kdc-qtap-email' ),
        'icon'         => '📧',
        'settings_url' => admin_url( 'admin.php?page=kdc-qtap-email' ),
        'version'      => KDC_QTAP_EMAIL_VERSION,
        'is_active'    => true,
        'priority'     => 30,
    );
    return $cards;
}
```

### Fallback Dashboard Functions

The shared menu file (`kdc-qtap-shared-menu.php`) provides these functions:

| Function | Description |
|----------|-------------|
| `kdc_qtap_render_fallback_dashboard()` | Renders the fallback dashboard with all registered cards |
| `kdc_qtap_render_dashboard_card( $card )` | Renders a single app card |
| `kdc_qtap_register_fallback_card( $cards, $card )` | Helper to add a card to the array |

### Fallback Dashboard Hooks

| Hook | Type | Description |
|------|------|-------------|
| `kdc_qtap_fallback_dashboard_cards` | Filter | Register app cards for fallback dashboard |
| `kdc_qtap_fallback_dashboard_after_cards` | Action | Add content after the cards grid |

### Complete Example

```php
<?php
class KDC_qTap_Email {

    public function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        // Register with dashboards at priority 20 (after qTap initializes)
        add_action( 'init', array( $this, 'register_with_qtap' ), 20 );
        
        // Admin menu
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
    }

    /**
     * Register with both qTap App and fallback dashboards.
     */
    public function register_with_qtap() {
        // qTap App dashboard (when installed)
        if ( function_exists( 'kdc_qtap_register_plugin' ) ) {
            kdc_qtap_register_plugin(
                array(
                    'id'           => 'email',
                    'name'         => __( 'Email', 'kdc-qtap-email' ),
                    'description'  => __( 'SMTP email with logging and templates.', 'kdc-qtap-email' ),
                    'icon'         => '📧',
                    'settings_url' => admin_url( 'admin.php?page=kdc-qtap-email' ),
                    'version'      => '1.0.0',
                    'is_active'    => true,
                    'priority'     => 30,
                    'category'     => 'communication',
                )
            );
        }

        // Fallback dashboard (when qTap App NOT installed)
        add_filter( 'kdc_qtap_fallback_dashboard_cards', array( $this, 'register_fallback_card' ) );
    }

    /**
     * Register card for fallback dashboard.
     */
    public function register_fallback_card( $cards ) {
        $cards['email'] = array(
            'id'           => 'email',
            'name'         => __( 'Email', 'kdc-qtap-email' ),
            'description'  => __( 'SMTP email with logging and templates.', 'kdc-qtap-email' ),
            'icon'         => '📧',
            'settings_url' => admin_url( 'admin.php?page=kdc-qtap-email' ),
            'version'      => '1.0.0',
            'is_active'    => true,
            'priority'     => 30,
        );
        return $cards;
    }
}
```

### Dashboard Appearance

When registered, the app appears as a card on the qTap Dashboard (or fallback):

```
┌─────────────────────────────┐
│  📧  Email           v1.0.0 │
│                             │
│  SMTP email with logging    │
│  and templates.             │
│                             │
│  [Settings]      [Active]   │
└─────────────────────────────┘
```

The fallback dashboard displays all registered cards in a responsive grid layout.

---

## Plugin Header Convention

All qTap App child plugins MUST use the following header format:

```php
<?php
/**
 * Plugin Name: qTap {App Name}
 * Plugin URI:  https://qtap.app/app/{slug}
 * Description: {Description of what the app does}
 * Version:     1.0.0
 * Author:      KDC
 * Author URI:  https://kdc.in
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kdc-qtap-{slug}
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.4
 */
```

### Plugin URI Schema

**Format:** `https://qtap.app/app/{slug}`

The `{slug}` is the **last part** of the plugin folder name (after `kdc-qtap-`).

| Plugin Folder | Plugin URI |
|---------------|------------|
| `kdc-qtap-mobile` | `https://qtap.app/app/mobile` |
| `kdc-qtap-school` | `https://qtap.app/app/school` |
| `kdc-qtap-email` | `https://qtap.app/app/email` |
| `kdc-qtap-analytics` | `https://qtap.app/app/analytics` |
| `kdc-qtap-starter` | `https://github.com/kdctek/kdc-qtap-starter` (exception - starter template) |

### Plugin Name Format

**Format:** `qTap {App Name}`

| Slug | Plugin Name |
|------|-------------|
| `mobile` | `qTap Mobile` |
| `school` | `qTap School` |
| `email` | `qTap Email` |

### Text Domain Format

**Format:** `kdc-qtap-{slug}`

Must match the plugin folder name exactly.

---

## WordPress Coding Standards

All qTap App child plugins MUST follow WordPress Coding Standards. This ensures consistency, maintainability, and compatibility.

### PHP Standards

**Indentation & Formatting:**
- Use **TABS** for indentation (not spaces)
- Opening braces on the same line for functions/classes
- Space after control structure keywords (`if`, `foreach`, `while`)
- Space around operators (`=`, `===`, `.`, `+`)

```php
// ✅ CORRECT
if ( $condition ) {
	do_something();
}

// ❌ WRONG
if($condition){
    do_something();
}
```

**Naming Conventions:**
- Functions: `lowercase_with_underscores()`
- Classes: `Class_Name_With_Underscores`
- Constants: `UPPERCASE_WITH_UNDERSCORES`
- Hooks: `{prefix}_{plugin}_{action}` (e.g., `kdc_qtap_starter_settings_saved`)

**Security Requirements:**
- Escape ALL output: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses()`
- Sanitize ALL input: `sanitize_text_field()`, `absint()`, `sanitize_email()`
- Verify nonces on ALL form submissions
- Check capabilities before ALL admin actions
- Use `$wpdb->prepare()` for ALL database queries

**⚠️ Translation Loading (WordPress 6.7+) - CRITICAL:**

WordPress 6.7+ requires translations to load AFTER the `init` action. The error:
```
Translation loading for the `kdc-qtap-{slug}` domain was triggered too early.
```

**Never call `__()`, `_e()`, `esc_html__()`, etc. in:**
- Class constructors that run on plugin load
- Property initializations
- Global scope or constants
- Anything that executes before `init` hook

**The Problem - Tabs initialized in constructor:**
```php
// ❌ WRONG - Called in constructor before init
private $tabs = array();  // Property

private function __construct() {
    $this->init_tabs();  // Called too early!
    $this->init_hooks();
}

private function init_tabs() {
    $this->tabs = array(
        'general' => __( 'General', 'my-plugin' ), // ERROR!
    );
}
```

**The Solution - Lazy initialization:**
```php
// ✅ CORRECT - Lazy initialization (called only when needed)
private $tabs = null;  // Start as null

private function __construct() {
    $this->init_hooks();  // No translations here!
}

private function get_tabs() {
    if ( null === $this->tabs ) {
        $this->tabs = array(
            'general'       => __( 'General', 'my-plugin' ),
            'import-export' => __( 'Import / Export', 'my-plugin' ),
            'data'          => __( 'Data Management', 'my-plugin' ),
        );
        $this->tabs = apply_filters( 'my_plugin_admin_tabs', $this->tabs );
    }
    return $this->tabs;
}

// Then replace ALL $this->tabs with $this->get_tabs()
// EXCEPT inside the get_tabs() method itself
```

**Quick Fix Steps for Existing Child Plugins:**

1. **Change property:** `private $tabs = array();` → `private $tabs = null;`

2. **Remove from constructor:** Delete `$this->init_tabs();` line

3. **Rename method:** `init_tabs()` → `get_tabs()`

4. **Add lazy loading wrapper:**
   ```php
   private function get_tabs() {
       if ( null === $this->tabs ) {
           // existing code here
       }
       return $this->tabs;
   }
   ```

5. **Find & Replace:** `$this->tabs` → `$this->get_tabs()` (except inside get_tabs())

**Files to fix in each child plugin:**
- `includes/class-kdc-qtap-{slug}-admin.php`

**Documentation:**
- PHPDoc blocks for all functions, classes, and files
- Inline comments for complex logic
- `@since` tags for version tracking

### CSS Standards - CRITICAL

**Use WordPress Core CSS Classes First!**

WordPress admin already provides extensive styling. Do NOT write custom CSS when WordPress core classes exist.

**Admin Pages - Use These Core Classes:**

```html
<!-- Buttons -->
<button class="button">Default Button</button>
<button class="button button-primary">Primary Button</button>
<button class="button button-secondary">Secondary Button</button>
<button class="button button-link">Link Button</button>
<button class="button button-link-delete">Delete Link</button>
<button class="button button-hero">Hero Button</button>

<!-- Form Tables (standard WP settings layout) -->
<table class="form-table" role="presentation">
    <tr>
        <th scope="row"><label for="field">Label</label></th>
        <td><input type="text" id="field" class="regular-text" /></td>
    </tr>
</table>

<!-- Input Sizes -->
<input type="text" class="small-text" />   <!-- ~50px -->
<input type="text" class="regular-text" /> <!-- ~250px -->
<input type="text" class="large-text" />   <!-- 100% width -->

<!-- Notices -->
<div class="notice notice-success is-dismissible"><p>Success!</p></div>
<div class="notice notice-error"><p>Error!</p></div>
<div class="notice notice-warning"><p>Warning!</p></div>
<div class="notice notice-info"><p>Info!</p></div>

<!-- Cards/Boxes -->
<div class="card"><p>Card content</p></div>
<div class="postbox"><div class="inside">Postbox content</div></div>

<!-- Tabs -->
<nav class="nav-tab-wrapper">
    <a href="#" class="nav-tab nav-tab-active">Tab 1</a>
    <a href="#" class="nav-tab">Tab 2</a>
</nav>

<!-- Description Text -->
<p class="description">Helper text goes here.</p>

<!-- Submit Button -->
<p class="submit">
    <input type="submit" class="button button-primary" value="Save Changes" />
</p>
```

**When to Write Custom CSS:**

Only write custom CSS when:
1. WordPress core classes don't provide the needed styling
2. You need plugin-specific branding/colors
3. You need custom layouts not available in core
4. You're building custom UI components

**Custom CSS Rules:**

```css
/* ✅ CORRECT - Scoped to plugin, minimal, uses CSS variables */
.kdc-qtap-starter-settings .custom-element {
    margin-bottom: 20px;
}

/* ❌ WRONG - Too generic, might conflict */
.settings-page .element {
    margin-bottom: 20px;
}

/* ✅ CORRECT - Extends WP core */
.kdc-qtap-starter-settings .notice {
    margin: 15px 0;
}

/* ❌ WRONG - Overrides WP core globally */
.notice {
    margin: 15px 0 !important;
}
```

**CSS Best Practices:**
- Prefix ALL custom classes with `kdc-qtap-{slug}-`
- Never use `!important` unless absolutely necessary
- Use WordPress color variables when available
- Keep specificity low
- Mobile-first responsive design
- Use `rem` or `em` for font sizes, `px` for borders/spacing

### Frontend CSS Standards

For frontend/theme CSS, follow the same principles:

**Use Theme CSS First:**
- Check if the active theme provides the class/style you need
- Use common WordPress classes: `.wp-block-*`, `.alignwide`, `.alignfull`
- Use standard HTML elements that themes style: `<button>`, `<input>`, `<table>`

**Only Add Custom Frontend CSS When:**
- Theme doesn't provide needed styling
- You need plugin-specific UI elements
- You're creating shortcode/block output that needs consistent styling

```css
/* ✅ Frontend CSS - Scoped and minimal */
.kdc-qtap-starter-widget {
    padding: 1em;
    border: 1px solid currentColor;
}

.kdc-qtap-starter-widget .title {
    font-size: 1.2em;
    margin-bottom: 0.5em;
}
```

### JavaScript Standards

**Use WordPress Dependencies:**
```javascript
// ✅ CORRECT - Use WordPress jQuery
( function( $ ) {
    'use strict';
    // Code here
}( jQuery ) );

// ✅ CORRECT - Use wp.ajax for AJAX calls
wp.ajax.post( 'my_action', { data: value } );

// ✅ CORRECT - Use wp.i18n for translations
const { __, _x, sprintf } = wp.i18n;
```

**Localization:**
```php
// Always localize scripts with wp_localize_script()
wp_localize_script( 'my-script', 'myScriptData', array(
    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    'nonce'   => wp_create_nonce( 'my_nonce' ),
    'i18n'    => array(
        'confirm' => __( 'Are you sure?', 'text-domain' ),
    ),
) );
```

**⚠️ IMPORTANT: Newlines in Localized Strings**

In PHP, `\n` behaves differently in single vs double quotes:
```php
// ❌ WRONG - \n is literal (shows as \n in browser)
'message' => __( 'Line 1\nLine 2', 'text-domain' ),

// ✅ CORRECT - Use double quotes for actual newlines
'message' => __( "Line 1\nLine 2", 'text-domain' ),

// ✅ BETTER - Keep messages simple, single line
'message' => __( 'This is a clear, single-line message.', 'text-domain' ),
```

Browser `confirm()` and `alert()` dialogs don't render newlines well anyway.
Keep confirmation messages concise and on a single line.

---

## Code Standards Checklist

Ask Claude to verify:

### PHP
- [ ] Uses TABS for indentation (not spaces)
- [ ] All strings use `__()`, `_e()`, `esc_html__()`, etc. with text domain
- [ ] All output is escaped: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses()`
- [ ] All input is sanitized: `sanitize_text_field()`, `absint()`, etc.
- [ ] Nonces used for all form submissions
- [ ] Capability checks on all admin functions
- [ ] Direct file access prevented with `ABSPATH` check
- [ ] Database queries use `$wpdb->prepare()`
- [ ] Hooks follow WordPress naming: `kdc_qtap_{slug}_{action}`
- [ ] PHPDoc blocks on all functions and classes

### CSS
- [ ] Uses WordPress core admin classes where possible
- [ ] Custom CSS is minimal and necessary
- [ ] All custom classes prefixed with `kdc-qtap-{slug}-`
- [ ] No `!important` unless absolutely required
- [ ] No global overrides of WordPress core styles
- [ ] Responsive design considered

### JavaScript
- [ ] Uses WordPress jQuery (not CDN)
- [ ] Wrapped in IIFE with `'use strict'`
- [ ] Localized via `wp_localize_script()`
- [ ] No inline JavaScript in PHP
- [ ] Console.log statements removed for production

---

## Testing Checklist

Before deploying, test:

### Basic Functionality
- [ ] Plugin activates without errors
- [ ] Plugin works with qTap App installed
- [ ] Plugin works WITHOUT qTap App (standalone)
- [ ] Settings save correctly
- [ ] Settings display correctly after save
- [ ] All tabs navigate correctly
- [ ] No PHP notices/warnings/errors
- [ ] JavaScript console has no errors

### Import/Export
- [ ] Export downloads valid JSON file
- [ ] Export filename includes date/time
- [ ] Copy to clipboard works
- [ ] Import accepts valid JSON file
- [ ] Import rejects invalid files (wrong format, wrong plugin)
- [ ] Import confirmation dialog appears
- [ ] Settings restored correctly after import
- [ ] Import from different site works (portable)

### Data Management
- [ ] "Delete data on uninstall" is OFF by default
- [ ] Warning box appears when enabling delete option
- [ ] Backup download link works in warning box
- [ ] Confirmation dialogs appear when enabling
- [ ] Setting saves correctly
- [ ] With delete OFF: data preserved after uninstall
- [ ] With delete ON: data removed after uninstall

### Compatibility
- [ ] Compatible with latest WordPress
- [ ] Compatible with latest WooCommerce (if applicable)
- [ ] Works in multisite (if applicable)

---

## Releasing New Versions with Claude

### Quick Release Prompt

```
Release a new [patch/minor/major] version of kdc-qtap-starter to GitHub.

GitHub Repository: https://github.com/kdctek/kdc-qtap-starter
GitHub Token: ghp_xxxxxxxxxxxxxxxxxxxx

Changes:
- [Describe what changed]
```

### Full Release Prompt (with changelog)

```
Please release version [X.X.X] of kdc-qtap-starter.

GitHub Token: [your-token]

Changelog for this version:
- Added [feature]
- Fixed [bug]
- Updated [component]

Please:
1. Update version in all files
2. Update changelog in README.md
3. Commit and push to GitHub
4. The GitHub Actions workflow will create the release
```

### What Claude Will Do

1. Clone or update the repository
2. Run `./scripts/version-bump.sh [version]`
3. Update README.md changelog
4. Commit changes with message "Release version X.X.X"
5. Create and push git tag
6. GitHub Actions automatically creates the release with ZIP

### Getting a GitHub Token

1. Go to https://github.com/settings/tokens
2. Click "Generate new token (classic)"
3. Name: "Claude Releases" (or anything)
4. Expiration: Set as needed
5. Scopes: Select `repo` (Full control of private repositories)
6. Click "Generate token"
7. Copy the token (starts with `ghp_`)

**Security Note:** Only share your token with Claude in a private conversation. Never commit tokens to repositories.
