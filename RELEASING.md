# Releasing qTap Starter

This document explains how to release new versions of qTap Starter.

## Quick Release (Recommended)

### Option 1: One-Command Release

```bash
# Bump patch version and release (e.g., 1.0.2 → 1.0.3)
./scripts/release.sh patch

# Bump minor version and release (e.g., 1.0.2 → 1.1.0)
./scripts/release.sh minor

# Set specific version and release
./scripts/release.sh 1.2.3
```

This single command will:
1. Update version in all files
2. Commit changes
3. Create git tag
4. Push to GitHub
5. GitHub Actions creates the release with ZIP

### Option 2: Step-by-Step Release

```bash
# 1. Bump version
./scripts/version-bump.sh patch

# 2. Edit README.md to add changelog notes

# 3. Commit and push
git add -A
git commit -m "Bump version to 1.0.3"
git tag -a v1.0.3 -m "Version 1.0.3"
git push origin main --tags
```

## Using Claude AI to Release

You can ask Claude to release a new version directly:

### Prompt Template

```
Please release a new [patch/minor/major] version of kdc-qtap-starter.

GitHub Token: ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

Changes in this release:
- [Change 1]
- [Change 2]
```

Claude will:
1. Update all version references
2. Update the changelog
3. Commit and push to GitHub
4. The GitHub Actions workflow handles the rest

### Getting a GitHub Token

1. Go to https://github.com/settings/tokens
2. Click "Generate new token (classic)"
3. Select scopes: `repo` (full control)
4. Copy the token (starts with `ghp_`)
5. Provide to Claude when releasing

## Files Updated During Version Bump

The `version-bump.sh` script updates:

| File | What's Updated |
|------|----------------|
| `kdc-qtap-starter.php` | `Version:` header |
| `kdc-qtap-starter.php` | `KDC_QTAP_STARTER_VERSION` constant |
| `README.md` | Changelog entry placeholder |

## GitHub Actions Workflow

When you push a version tag (e.g., `v1.0.3`), GitHub Actions automatically:

1. Verifies version matches tag
2. Creates clean plugin ZIP (excludes dev files)
3. Extracts changelog from README.md
4. Creates GitHub Release with:
   - Release notes from changelog
   - ZIP file attached for download

### Excluded from ZIP

- `.git/`
- `.github/`
- `scripts/`
- `node_modules/`
- `vendor/`
- `composer.json`
- `phpcs.xml.dist`
- Development markdown files

### Included in ZIP

- All PHP files
- `assets/` directory
- `includes/` directory
- `languages/` directory
- `templates/` directory
- `README.md`

## Versioning Guidelines

Follow [Semantic Versioning](https://semver.org/):

- **MAJOR** (1.0.0 → 2.0.0): Breaking changes
- **MINOR** (1.0.0 → 1.1.0): New features, backwards compatible
- **PATCH** (1.0.0 → 1.0.1): Bug fixes, minor updates

## Troubleshooting

### Tag Already Exists

```bash
# Delete local tag
git tag -d v1.0.3

# Delete remote tag
git push origin :refs/tags/v1.0.3

# Try again
./scripts/release.sh 1.0.3
```

### Push Permission Denied

Ensure you have push access to the repository:
- For HTTPS: Use a Personal Access Token
- For SSH: Ensure your SSH key is added to GitHub

### GitHub Actions Failed

Check the Actions tab: https://github.com/kdctek/kdc-qtap-starter/actions

Common issues:
- Version mismatch between tag and plugin file
- Missing `GITHUB_TOKEN` permissions
