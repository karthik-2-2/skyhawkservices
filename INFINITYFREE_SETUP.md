# InfinityFree Setup Guide

## Why InfinityFree is Great
- 100% Free forever
- No forced ads
- Custom domain support
- 5GB storage
- Unlimited bandwidth
- PHP + MySQL

## Setup Steps

### 1. Sign up at infinityfree.net
- Create account
- Choose subdomain or add custom domain

### 2. Add Custom Domain
- In control panel, go to "Subdomain"
- Add: skyhawkservices.in
- Wait for DNS propagation

### 3. Update Hostinger DNS
Point your domain to InfinityFree:
- Type: A Record
- Name: @
- Value: [InfinityFree IP - they provide this]

### 4. Database Setup
- Create MySQL database in cPanel
- Use the MySQL schema provided
- Update db.php with MySQL credentials

### 5. Upload Files
- Use File Manager or FTP
- Upload all your files to public_html

## Database Conversion Required
You'll need to use the MySQL version of your database instead of Supabase.
