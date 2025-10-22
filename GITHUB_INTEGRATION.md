# GitHub Setup Instructions

## To connect this plugin to your GitHub repository:

1. **Create a new repository on GitHub**
   - Go to https://github.com and click "New repository"
   - Name it something like "media-url-proxy" (or your preferred name)
   - Don't initialize with README, .gitignore, or license (we already have these)
   - Click "Create repository"

2. **Copy the repository URL**
   - After creating the repository, you'll see a URL like: `https://github.com/yourusername/media-url-proxy.git`
   - Copy this URL

3. **Connect your local repository to GitHub**
   ```bash
   cd /home/chrisallen/Sites/surfturf/wp-content/plugins/media-url-proxy
   git remote add origin YOUR_REPOSITORY_URL
   git branch -M main
   git push -u origin main
   ```

Replace `YOUR_REPOSITORY_URL` with the URL you copied in step 2.

## Repository Structure
Your repository includes:
- `media-url-proxy.php` - Main plugin file
- `readme.txt` - Plugin documentation
- `.gitignore` - Files to exclude from Git
- `LICENSE` - MIT License file

## Making Future Changes
After making changes to your plugin:
```bash
cd /home/chrisallen/Sites/surfturf/wp-content/plugins/media-url-proxy
git add .
git commit -m "Your commit message"
git push
```

## For Private Repositories
If you're using a private repository, you may need to set up SSH keys or use GitHub tokens for authentication.