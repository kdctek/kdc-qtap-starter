#!/bin/bash
#
# Release Script for qTap Starter
#
# This script automates the full release process:
# 1. Bumps version (if specified)
# 2. Commits changes
# 3. Creates git tag
# 4. Pushes to GitHub
# 5. GitHub Actions creates the release with ZIP
#
# Usage: ./scripts/release.sh [version]
#
# Examples:
#   ./scripts/release.sh           # Release current version
#   ./scripts/release.sh patch     # Bump patch and release
#   ./scripts/release.sh 1.2.3     # Set version and release
#

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="$(dirname "$SCRIPT_DIR")"
PLUGIN_FILE="$PLUGIN_DIR/kdc-qtap-starter.php"

# Get current version from plugin file
get_version() {
    grep -oP "Version:\s*\K[0-9]+\.[0-9]+\.[0-9]+" "$PLUGIN_FILE"
}

# Check for uncommitted changes
check_clean_tree() {
    if [ -n "$(git status --porcelain)" ]; then
        return 1
    fi
    return 0
}

# Main release function
release() {
    local version_arg=$1
    
    echo -e "${BLUE}╔════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║     qTap Starter Release Script        ║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════╝${NC}"
    echo ""
    
    cd "$PLUGIN_DIR"
    
    # Check if we're in a git repo
    if [ ! -d ".git" ]; then
        echo -e "${RED}Error: Not a git repository${NC}"
        exit 1
    fi
    
    # If version argument provided, bump version first
    if [ -n "$version_arg" ]; then
        echo -e "${YELLOW}Bumping version...${NC}"
        ./scripts/version-bump.sh "$version_arg"
        echo ""
    fi
    
    # Get the version to release
    local version=$(get_version)
    echo -e "${GREEN}Releasing version: $version${NC}"
    echo ""
    
    # Check if tag already exists
    if git rev-parse "v$version" >/dev/null 2>&1; then
        echo -e "${RED}Error: Tag v$version already exists${NC}"
        echo "Use a different version or delete the existing tag:"
        echo "  git tag -d v$version"
        echo "  git push origin :refs/tags/v$version"
        exit 1
    fi
    
    # Check for uncommitted changes
    if ! check_clean_tree; then
        echo -e "${YELLOW}Uncommitted changes detected. Committing...${NC}"
        git add -A
        git commit -m "Release version $version"
        echo -e "${GREEN}✓ Changes committed${NC}"
    else
        echo -e "${GREEN}✓ Working tree clean${NC}"
    fi
    
    # Create annotated tag
    echo -e "${YELLOW}Creating tag v$version...${NC}"
    git tag -a "v$version" -m "Version $version"
    echo -e "${GREEN}✓ Tag created${NC}"
    
    # Push to GitHub
    echo -e "${YELLOW}Pushing to GitHub...${NC}"
    git push origin main --tags
    echo -e "${GREEN}✓ Pushed to GitHub${NC}"
    
    echo ""
    echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║         Release Successful!            ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "Version ${BLUE}v$version${NC} has been pushed to GitHub."
    echo ""
    echo "GitHub Actions will now:"
    echo "  1. Build the plugin ZIP"
    echo "  2. Create a GitHub Release"
    echo "  3. Attach the ZIP as a downloadable asset"
    echo ""
    echo -e "View release: ${BLUE}https://github.com/kdctek/kdc-qtap-starter/releases/tag/v$version${NC}"
}

# Run
release "$@"
