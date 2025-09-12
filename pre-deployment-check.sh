#!/bin/bash

# 🔍 Pre-Deployment Safety Check
# Run this before any server operations to prevent data loss

echo "🔍 PRE-DEPLOYMENT SAFETY CHECK..."

# Check if we're about to do something dangerous
DANGEROUS_COMMANDS=("git reset --hard" "rm -rf" "git checkout --force")
COMMAND_LINE="$@"

for dangerous in "${DANGEROUS_COMMANDS[@]}"; do
    if [[ "$COMMAND_LINE" == *"$dangerous"* ]]; then
        echo "🚨 DANGEROUS COMMAND DETECTED: $dangerous"
        echo "⛔ This command could cause data loss!"
        echo ""
        echo "✅ Safer alternatives:"
        if [[ "$dangerous" == "git reset --hard" ]]; then
            echo "   Use: ./safe-deploy.sh"
        elif [[ "$dangerous" == "rm -rf" ]]; then
            echo "   Use: git clean -fd (for git files)"
        elif [[ "$dangerous" == "git checkout --force" ]]; then
            echo "   Use: git stash + git checkout"
        fi
        echo ""
        echo "❓ Do you want to run ./emergency-backup.sh first?"
        read -p "Type 'yes' to create backup, 'skip' to proceed dangerously: " choice
        
        if [[ "$choice" == "yes" ]]; then
            ./emergency-backup.sh
            echo "✅ Backup complete. You may now proceed."
        elif [[ "$choice" == "skip" ]]; then
            echo "⚠️  Proceeding without backup - you have been warned!"
        else
            echo "❌ Aborted for safety"
            exit 1
        fi
        break
    fi
done

# Check server status before deployment
echo ""
echo "📡 Checking server status..."
SERVER_STATUS=$(ssh thilacoloma "cd /var/www/thilacoloma && git status --porcelain" 2>/dev/null || echo "SERVER_UNREACHABLE")

if [[ "$SERVER_STATUS" == "SERVER_UNREACHABLE" ]]; then
    echo "⚠️  Cannot reach server - deployment may fail"
elif [[ -n "$SERVER_STATUS" ]]; then
    echo "⚠️  Server has uncommitted changes:"
    echo "$SERVER_STATUS"
    echo ""
    echo "💡 Recommendation: Use ./safe-deploy.sh instead of manual git commands"
fi

echo "✅ Safety check complete"