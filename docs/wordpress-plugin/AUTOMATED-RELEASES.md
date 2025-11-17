# Automated GitHub Releases

## Overview

We've set up GitHub Actions to automatically create releases when you push code to the `main` branch. This eliminates manual steps and reduces human error.

## How It Works

### Automatic Trigger

The workflow triggers when:
- Code is pushed to `main` branch
- AND the `squash-court-stats.php` file changes (version number update)

### What Happens Automatically

1. **Version Extraction**
   - Reads version from `squash-court-stats.php` (e.g., `Version: 1.6.0`)
   - Creates tag: `v1.6.0`

2. **Duplicate Check**
   - Checks if release with that tag already exists
   - Skips if release exists (prevents duplicates)

3. **Plugin Packaging**
   - Packages plugin into `squash-court-stats.zip`
   - Includes all necessary files:
     - `squash-court-stats.php`
     - `readme.txt`
     - `README.md`
     - `includes/class-plugin-updater.php`

4. **Release Creation**
   - Creates GitHub release with tag
   - Attaches ZIP file
   - Adds release notes

5. **WordPress Detection**
   - WordPress detects new release within 12 hours
   - Users see "Update available" notification

## Workflow

```
You update version in squash-court-stats.php
    ↓
You commit and push to main
    ↓
GitHub Actions triggers automatically
    ↓
Workflow extracts version (e.g., 1.6.0)
    ↓
Checks if release v1.6.0 exists
    ↓
If new: Packages plugin → Creates release → Attaches ZIP
    ↓
WordPress detects update (within 12 hours)
```

## Usage

### Normal Release Process

1. **Update version number** in `squash-court-stats.php`:
   ```php
   * Version: 1.6.0
   ```

2. **Commit and push:**
   ```bash
   git add squash-court-stats.php
   git commit -m "Bump version to 1.6.0"
   git push origin main
   ```

3. **GitHub Actions automatically:**
   - Detects version change
   - Packages plugin
   - Creates release `v1.6.0`
   - Attaches ZIP file

4. **That's it!** No manual release creation needed.

### Manual Trigger

You can also trigger the workflow manually:

1. Go to: https://github.com/itomic/squash-court-stats/actions
2. Select "Create Release" workflow
3. Click "Run workflow"
4. Select branch (usually `main`)
5. Click "Run workflow"

This is useful if:
- You want to re-create a release
- You need to test the workflow
- The automatic trigger didn't fire

## Preventing Duplicate Releases

The workflow automatically checks if a release with the same tag already exists. If it does:
- Release creation is skipped
- Workflow completes successfully (no error)
- You'll see a message: "Release already exists, skipping..."

To update an existing release:
1. Delete the release on GitHub
2. Push again (or manually trigger workflow)

## Version Number Format

The version must follow semantic versioning:
- `1.6.0` ✅
- `1.6.1` ✅
- `2.0.0` ✅
- `1.6` ❌ (must be x.y.z)
- `v1.6.0` ❌ (no 'v' prefix in plugin file)

The workflow automatically adds the `v` prefix to the tag.

## Files Included in Release

The packaged ZIP includes:
```
squash-court-stats/
├── squash-court-stats.php
├── readme.txt
├── README.md
└── includes/
    └── class-plugin-updater.php
```

## Troubleshooting

### Release Not Created?

1. **Check workflow run:**
   - Go to: https://github.com/itomic/squash-court-stats/actions
   - Look for failed runs
   - Check error messages

2. **Verify version format:**
   - Must be `x.y.z` format
   - Must be in `squash-court-stats.php` header

3. **Check file changes:**
   - Workflow only triggers if `squash-court-stats.php` changed
   - Or trigger manually if needed

4. **Verify permissions:**
   - GitHub Actions needs `contents: write` permission
   - This is set in the workflow file

### Release Created But ZIP Missing?

1. Check workflow logs for packaging errors
2. Verify all required files exist:
   - `squash-court-stats.php`
   - `readme.txt`
   - `includes/class-plugin-updater.php`
   - `docs/wordpress-plugin/PLUGIN-README.md`

### Duplicate Release Error?

- This is normal if release already exists
- Workflow skips creation (prevents duplicates)
- Delete existing release if you need to recreate

## Workflow File Location

The workflow is defined in:
```
.github/workflows/release.yml
```

## Benefits

✅ **Fully Automated** - No manual release creation  
✅ **Error Prevention** - Checks for duplicates  
✅ **Consistent** - Same process every time  
✅ **Fast** - Release created within minutes  
✅ **Reliable** - Uses GitHub's infrastructure  

## Next Steps

1. ✅ Automated releases are set up
2. ⏳ Test by updating version and pushing to main
3. ⏳ Verify release is created automatically
4. ⏳ Confirm WordPress detects the update

## Summary

**Just update the version number and push to main** - GitHub Actions handles the rest!

No more:
- ❌ Manual ZIP creation
- ❌ Manual release creation
- ❌ Manual file attachment
- ❌ Forgetting to create releases

Just:
- ✅ Update version
- ✅ Push to main
- ✅ Release created automatically

