#!/bin/bash

echo "================================="
echo "   SKY HAWK AGRO - GitHub Push"
echo "================================="
echo

# Change to script directory
cd "$(dirname "$0")"

echo "Checking git status..."
git status
echo

echo "Adding all changes..."
git add .
echo

echo "Current changes to be committed:"
git status --short
echo

read -p "Enter commit message (or press Enter for default): " commit_message
if [ -z "$commit_message" ]; then
    commit_message="Update: $(date)"
fi

echo
echo "Committing with message: \"$commit_message\""
git commit -m "$commit_message"
echo

echo "Pushing to GitHub..."
git push origin main
echo

if [ $? -eq 0 ]; then
    echo "✅ Successfully pushed to GitHub!"
    echo "Repository: https://github.com/karthikeyaReddy22/sky-hawk-agro.git"
else
    echo "❌ Push failed. Please check your internet connection and GitHub credentials."
fi

echo
echo "Final status:"
git status
echo

read -p "Press Enter to continue..."
