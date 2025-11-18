# Deployment Strategy - Best Practices

## Recommended Approach: Hybrid (GitHub Webhook + cPanel Native Deployment)

Based on industry best practices and cPanel documentation, we use a **hybrid approach** that combines:

1. **GitHub Webhook** - Triggers deployment when code is pushed
2. **cPanel's Built-in Git Deployment** - Uses `.cpanel.yml` via UAPI

## Why This Approach?

### ✅ Advantages

1. **Leverages Native Tools**: Uses cPanel's built-in Git Version Control, reducing custom code
2. **Reduced Maintenance**: Less custom script code to maintain
3. **Better Integration**: Works seamlessly with cPanel's interface and features
4. **Standardized**: Follows cPanel's recommended deployment patterns
5. **Reliable**: Uses cPanel's tested deployment mechanisms

### ⚠️ Considerations

1. **UAPI Dependency**: Requires cPanel UAPI to be available
2. **Less Flexibility**: `.cpanel.yml` has some limitations compared to custom scripts
3. **YAML Syntax**: Requires careful YAML formatting

## How It Works

```
1. Developer pushes to GitHub main branch
   ↓
2. GitHub sends webhook to webhook-deploy.php
   ↓
3. Webhook verifies signature and pulls from GitHub
   ↓
4. Webhook triggers cPanel UAPI deployment
   ↓
5. cPanel executes .cpanel.yml tasks
   ↓
6. Site is deployed automatically
```

## Files Involved

### `webhook-deploy.php`
- Receives GitHub webhook requests
- Verifies GitHub signature (security)
- Pulls latest code from GitHub
- Triggers cPanel deployment via UAPI

### `.cpanel.yml`
- Defines deployment tasks
- Executed by cPanel automatically
- Handles file syncing, npm builds, cache clearing

## Alternative: Custom Script Approach

If you prefer more control, you can use `deploy.sh` instead:

**Pros:**
- More flexibility
- Better error handling
- More detailed logging

**Cons:**
- More code to maintain
- Requires handling user permissions manually
- More potential failure points

## Current Implementation

We're using the **hybrid approach** (recommended):

- ✅ GitHub webhook triggers deployment
- ✅ cPanel UAPI handles deployment via `.cpanel.yml`
- ✅ Best of both worlds: automation + native tools

## Troubleshooting

### If UAPI Deployment Fails

Check:
1. Is repository managed by cPanel? (cPanel → Git Version Control)
2. Does `.cpanel.yml` exist and have valid YAML syntax?
3. Check deployment logs: `/home/stats/logs/deploy-output.log`
4. Try manual deployment: cPanel → Git Version Control → Deploy HEAD Commit

### Fallback to Custom Script

If cPanel deployment doesn't work, you can switch back to `deploy.sh`:
- Update `webhook-deploy.php` to call `/home/stats/deploy.sh` instead
- Ensure `deploy.sh` is executable: `chmod +x /home/stats/deploy.sh`

## References

- [cPanel Git Deployment Guide](https://docs.cpanel.net/knowledge-base/web-services/guide-to-git-deployment/)
- [cPanel Git Version Control](https://docs.cpanel.net/cpanel/files/git-version-control/)
- [cPanel UAPI Documentation](https://api.docs.cpanel.net/)

