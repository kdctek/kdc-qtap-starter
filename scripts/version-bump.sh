#!/bin/bash
#
# Version Bump Script for qTap Starter
# 
# Usage: ./scripts/version-bump.sh [major|minor|patch|x.x.x]
#
# Examples:
#   ./scripts/version-bump.sh patch     # 1.0.2 -> 1.0.3
#   ./scripts/version-bump.sh minor     # 1.0.2 -> 1.1.0
#   ./scripts/version-bump.sh major     # 1.0.2 -> 2.0.0
#   ./scripts/version-bump.sh 1.2.3     # Set specific version
#

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Get script directory and plugin root
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="$(dirname "$SCRIPT_DIR")"

# Main plugin file
PLUGIN_FILE="$PLUGIN_DIR/kdc-qtap-starter.php"

# Get current version
get_current_version() {
    grep -oP "Version:\s*\K[0-9]+\.[0-9]+\.[0-9]+" "$PLUGIN_FILE"
}

# Bump version based on type
bump_version() {
    local current=$1
    local type=$2
    
    IFS='.' read -r major minor patch <<< "$current"
    
    case $type in
        major)
            echo "$((major + 1)).0.0"
            ;;
        minor)
            echo "$major.$((minor + 1)).0"
            ;;
        patch)
            echo "$major.$minor.$((patch + 1))"
            ;;
        *)
            # Assume it's a specific version number
            if [[ $type =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
                echo "$type"
            else
                echo ""
            fi
            ;;
    esac
}

# Update version in files
update_version() {
    local old_version=$1
    local new_version=$2
    
    echo -e "${YELLOW}Updating version from $old_version to $new_version...${NC}"
    
    # 1. Main plugin file - header
    sed -i "s/Version:           $old_version/Version:           $new_version/" "$PLUGIN_FILE"
    echo -e "  ${GREEN}✓${NC} kdc-qtap-starter.php (header)"
    
    # 2. Main plugin file - constant
    sed -i "s/KDC_QTAP_STARTER_VERSION', '$old_version'/KDC_QTAP_STARTER_VERSION', '$new_version'/" "$PLUGIN_FILE"
    echo -e "  ${GREEN}✓${NC} kdc-qtap-starter.php (constant)"
    
    # 3. README.md - add changelog entry placeholder if not exists
    if ! grep -q "### $new_version" "$PLUGIN_DIR/README.md"; then
        # Add new version entry after "## Changelog"
        sed -i "/## Changelog/a\\
\\
### $new_version\\
- " "$PLUGIN_DIR/README.md"
        echo -e "  ${GREEN}✓${NC} README.md (changelog placeholder added)"
    else
        echo -e "  ${YELLOW}→${NC} README.md (changelog entry already exists)"
    fi
    
    # 4. Shared menu file (if version reference exists)
    local SHARED_MENU="$PLUGIN_DIR/includes/kdc-qtap-shared-menu.php"
    if [ -f "$SHARED_MENU" ]; then
        sed -i "s/@since   $old_version/@since   $new_version/g" "$SHARED_MENU" 2>/dev/null || true
        echo -e "  ${GREEN}✓${NC} kdc-qtap-shared-menu.php"
    fi
}

# Main
main() {
    local bump_type=$1
    
    if [ -z "$bump_type" ]; then
        echo -e "${RED}Error: Please specify version bump type${NC}"
        echo ""
        echo "Usage: $0 [major|minor|patch|x.x.x]"
        echo ""
        echo "Examples:"
        echo "  $0 patch     # 1.0.2 -> 1.0.3"
        echo "  $0 minor     # 1.0.2 -> 1.1.0"
        echo "  $0 major     # 1.0.2 -> 2.0.0"
        echo "  $0 1.2.3     # Set specific version"
        exit 1
    fi
    
    # Check if plugin file exists
    if [ ! -f "$PLUGIN_FILE" ]; then
        echo -e "${RED}Error: Plugin file not found at $PLUGIN_FILE${NC}"
        exit 1
    fi
    
    # Get current version
    local current_version=$(get_current_version)
    if [ -z "$current_version" ]; then
        echo -e "${RED}Error: Could not determine current version${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}Current version: $current_version${NC}"
    
    # Calculate new version
    local new_version=$(bump_version "$current_version" "$bump_type")
    if [ -z "$new_version" ]; then
        echo -e "${RED}Error: Invalid version bump type '$bump_type'${NC}"
        exit 1
    fi
    
    if [ "$current_version" = "$new_version" ]; then
        echo -e "${YELLOW}Version unchanged${NC}"
        exit 0
    fi
    
    # Update all files
    update_version "$current_version" "$new_version"
    
    echo ""
    echo -e "${GREEN}✓ Version updated to $new_version${NC}"
    echo ""
    echo -e "${YELLOW}Next steps:${NC}"
    echo "  1. Update changelog in README.md"
    echo "  2. git add -A"
    echo "  3. git commit -m 'Bump version to $new_version'"
    echo "  4. git tag -a v$new_version -m 'Version $new_version'"
    echo "  5. git push origin main --tags"
    echo ""
    echo -e "${GREEN}Or run: ./scripts/release.sh $new_version${NC}"
}

main "$@"
