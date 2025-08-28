# 🚀 GitHub Push Instructions

## Quick Push to GitHub

### Option 1: Use Automation Script (Recommended)
**For Windows:**
```bash
# Double-click this file or run in terminal:
push_to_github.bat
```

**For Mac/Linux:**
```bash
# Make executable and run:
chmod +x push_to_github.sh
./push_to_github.sh
```

### Option 2: Manual Commands
```bash
# Navigate to project directory
cd c:\xampp\htdocs\sky_2

# Add all changes
git add .

# Commit with message
git commit -m "Your commit message here"

# Push to GitHub
git push origin main
```

### Option 3: Quick One-Liner
```bash
git add . && git commit -m "Quick update" && git push origin main
```

## Repository Information
- **Repository URL**: https://github.com/karthikeyaReddy22/sky-hawk-agro.git
- **Branch**: main
- **Owner**: karthikeyaReddy22

## Common Git Commands
```bash
# Check status
git status

# View commit history
git log --oneline -10

# View remote repositories
git remote -v

# Pull latest changes
git pull origin main

# View differences
git diff
```

## Troubleshooting
If push fails:
1. Check internet connection
2. Verify GitHub credentials
3. Try: `git pull origin main` first
4. Then push again

---
**Last Updated**: August 28, 2025
**Theme Update**: ✅ Complete
