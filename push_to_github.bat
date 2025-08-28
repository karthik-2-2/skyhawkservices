@echo off
echo =================================
echo   SKY HAWK AGRO - GitHub Push
echo =================================
echo.

cd /d "%~dp0"

echo Checking git status...
git status
echo.

echo Adding all changes...
git add .
echo.

echo Current changes to be committed:
git status --short
echo.

set /p commit_message="Enter commit message (or press Enter for default): "
if "%commit_message%"=="" (
    set commit_message=Update: %date% %time%
)

echo.
echo Committing with message: "%commit_message%"
git commit -m "%commit_message%"
echo.

echo Pushing to GitHub...
git push origin main
echo.

if %errorlevel% equ 0 (
    echo ✅ Successfully pushed to GitHub!
    echo Repository: https://github.com/karthikeyaReddy22/sky-hawk-agro.git
) else (
    echo ❌ Push failed. Please check your internet connection and GitHub credentials.
)

echo.
echo Final status:
git status
echo.
pause
