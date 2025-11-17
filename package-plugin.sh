#!/bin/bash
# Cross-platform packaging script for GitHub Actions
# This is a bash version of package-plugin.ps1 for use in CI/CD

PLUGIN_NAME="squash-court-stats"
VERSION=$(grep -oP 'Version: \K[0-9]+\.[0-9]+\.[0-9]+' squash-court-stats.php | head -1)
OUTPUT_FILE="${PLUGIN_NAME}.zip"

echo "Packaging ${PLUGIN_NAME} plugin (v${VERSION})..."

# Create temporary directory with correct structure
mkdir -p "temp/${PLUGIN_NAME}/includes"

# Copy plugin files
cp squash-court-stats.php "temp/${PLUGIN_NAME}/"
cp readme.txt "temp/${PLUGIN_NAME}/"
cp docs/wordpress-plugin/PLUGIN-README.md "temp/${PLUGIN_NAME}/README.md"
cp includes/class-plugin-updater.php "temp/${PLUGIN_NAME}/includes/"

# Create zip file
cd temp
zip -r "../${OUTPUT_FILE}" "${PLUGIN_NAME}/"
cd ..

# Cleanup
rm -rf temp

echo "Plugin packaged successfully: ${OUTPUT_FILE}"
