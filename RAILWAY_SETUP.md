# Sky Hawk Agro - Railway Deployment

## Quick Setup for Railway.app

### 1. Create GitHub Repository
```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/yourusername/sky-hawk-agro.git
git push -u origin main
```

### 2. Railway Deployment
1. Go to railway.app
2. Sign up with GitHub
3. Click "Deploy from GitHub repo"
4. Select your repository
5. Railway will auto-detect PHP

### 3. Environment Variables (if needed)
- Add any sensitive config in Railway dashboard
- Database credentials are already in your code

### 4. Custom Domain Setup
1. In Railway dashboard, go to Settings
2. Click "Domains"
3. Add custom domain: skyhawkservices.in
4. Copy the CNAME record
5. In Hostinger DNS settings:
   - Type: CNAME
   - Name: @
   - Value: [Railway provided URL]

### 5. SSL Certificate
- Railway automatically provides SSL
- Your site will be https://skyhawkservices.in

## Benefits
✅ Keep your Supabase database
✅ Professional hosting
✅ Easy updates via Git
✅ Automatic HTTPS
✅ No code changes needed
