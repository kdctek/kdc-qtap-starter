# Claude AI Prompt Template

Use this prompt to get Claude to help you create a new qTap App child plugin based on this starter template.

**GitHub Repository:** [https://github.com/kdctek/kdc-qtap-starter](https://github.com/kdctek/kdc-qtap-starter)

---

## Starting Prompt

Copy and paste this prompt to Claude:

```
I want to create a new qTap App child plugin for WordPress. Please help me build it based on the qTap Starter template.

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

Please create the plugin with:
1. WordPress Coding Standards compliance
2. Proper integration with qTap App dashboard (register with dashboard when available)
3. Shared fallback menu support (uses kdc_qtap_ensure_fallback_menu() - no duplicate menus)
4. All necessary files: main plugin file, admin class, shared menu helper, CSS, JS, uninstall.php
5. Proper sanitization, escaping, and nonce verification
6. Translation-ready strings
7. Appropriate hooks and filters for extensibility
8. Author: KDC (https://kdc.in)
```

---

## Example Prompt

Here's a filled-in example:

```
I want to create a new qTap App child plugin for WordPress. Please help me build it based on the qTap Starter template.

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

After Claude generates the initial plugin, you can use these follow-up prompts:

### Add a New Feature
```
Please add [feature description] to the qTap [App Name] plugin. 
It should [detailed requirements].
```

### Add Database Table
```
I need a custom database table for the qTap [App Name] plugin to store [data type].
Fields needed: [list fields with types].
Include proper table creation on activation and cleanup on uninstall.
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

---

## File Naming Convention

When Claude creates files, ensure they follow this pattern:

| File Type | Naming Pattern |
|-----------|----------------|
| Main plugin | `kdc-qtap-{slug}.php` |
| Classes | `class-kdc-qtap-{slug}-{name}.php` |
| Admin CSS | `assets/css/admin.css` |
| Admin JS | `assets/js/admin.js` |
| Frontend CSS | `assets/css/frontend.css` |
| Frontend JS | `assets/js/frontend.js` |
| Templates | `templates/{template-name}.php` |

---

## Code Standards Checklist

Ask Claude to verify:

- [ ] All strings use `__()`, `_e()`, `esc_html__()`, etc. with text domain
- [ ] All output is escaped: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses()`
- [ ] All input is sanitized: `sanitize_text_field()`, `absint()`, etc.
- [ ] Nonces used for all form submissions
- [ ] Capability checks on all admin functions
- [ ] Direct file access prevented with `ABSPATH` check
- [ ] Database queries use `$wpdb->prepare()`
- [ ] Hooks follow WordPress naming: `kdc_qtap_{slug}_{action}`
- [ ] Inline documentation with PHPDoc blocks

---

## Testing Checklist

Before deploying, test:

- [ ] Plugin activates without errors
- [ ] Plugin works with qTap App installed
- [ ] Plugin works WITHOUT qTap App (standalone)
- [ ] Settings save correctly
- [ ] Settings display correctly after save
- [ ] All features work as expected
- [ ] No PHP notices/warnings/errors
- [ ] JavaScript console has no errors
- [ ] Uninstall removes all plugin data
- [ ] Compatible with latest WordPress
- [ ] Compatible with latest WooCommerce (if applicable)

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
