# Debugging Auto-Deployment Issues

## Current Status

✅ **GitHub webhook is configured and working** - All deliveries show 200 OK  
✅ **Webhook script receives requests** - Signature verification passes  
⚠️ **Deployment may not be executing** - Need to verify deploy.sh is running

## How to Diagnose

### Step 1: Check Webhook Logs

Check if webhook is receiving and processing requests:

```bash
ssh root@atlas.itomic.com "tail -20 /home/stats/logs/webhook-deploy.log"
```

**Look for:**
- `Deployment triggered by [username]` - Webhook received push event
- `Executing deployment command` - Command is being executed
- `Background process started` - Script execution initiated
- Any `[ERROR]` messages

### Step 2: Check Deployment Logs

Check if deployment script is actually running:

```bash
ssh root@atlas.itomic.com "tail -50 /home/stats/logs/deploy-output.log"
```

**Look for:**
- `🚀 Starting deployment...` - Script started
- `📥 Pulling latest changes` - Git pull happening
- `✅ Pulled commit: [hash]` - Successfully pulled
- `📋 Syncing changes` - Files being synced
- Any error messages

### Step 3: Verify Deploy Script Exists and is Executable

```bash
ssh root@atlas.itomic.com "ls -la /home/stats/deploy.sh"
```

**Should show:**
- File exists
- Executable permissions: `-rwxr-xr-x` or similar
- Owned by appropriate user

### Step 4: Test Manual Execution

Test if the deploy script runs manually:

```bash
ssh root@atlas.itomic.com "bash /home/stats/deploy.sh"
```

**Expected:**
- Script runs without errors
- Deployment completes successfully
- Logs show successful deployment

## Common Issues

### Issue 1: Deploy Script Not Executable

**Symptom:** Webhook log shows "Deploy script is not executable"

**Fix:**
```bash
ssh root@atlas.itomic.com "chmod +x /home/stats/deploy.sh"
```

### Issue 2: Deploy Script Not Found

**Symptom:** Webhook log shows "Deploy script not found"

**Fix:**
- Verify script exists: `ls -la /home/stats/deploy.sh`
- Check path in `webhook-deploy.php` line 9
- Ensure script is in the correct location

### Issue 3: Background Process Not Starting

**Symptom:** Webhook receives request but deployment log is empty

**Possible Causes:**
- PHP `exec()` function disabled
- Permission issues
- Script path incorrect

**Check:**
```bash
# Check PHP exec() function
ssh root@atlas.itomic.com "php -r 'exec(\"echo test\", \$o); print_r(\$o);'"

# Check script permissions
ssh root@atlas.itomic.com "ls -la /home/stats/deploy.sh"

# Check log directory permissions
ssh root@atlas.itomic.com "ls -la /home/stats/logs/"
```

### Issue 4: Deployment Script Fails Silently

**Symptom:** Script starts but fails without logging

**Check:**
- Review `deploy.sh` script for errors
- Check if `set -euo pipefail` is causing early exits
- Verify all paths exist and are writable

## Verification Checklist

After a push to `main`:

- [ ] GitHub webhook shows 200 OK delivery
- [ ] Webhook log shows "Deployment triggered"
- [ ] Webhook log shows "Background process started"
- [ ] Deployment log shows "🚀 Starting deployment..."
- [ ] Deployment log shows "✅ Pulled commit: [hash]"
- [ ] Deployment log shows successful completion
- [ ] Production site shows latest changes

## Next Steps

If deployment still isn't working:

1. **Check recent webhook deliveries** in GitHub (Settings → Webhooks → Recent Deliveries)
2. **Review both logs** (webhook-deploy.log and deploy-output.log)
3. **Test manual deployment** to isolate the issue
4. **Check file permissions** on deploy.sh and log directory
5. **Verify PHP can execute shell commands** in web server context

