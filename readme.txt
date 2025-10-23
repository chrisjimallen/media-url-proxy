# Media URL Proxy

A WordPress plugin that allows you to replace media URLs with a configured domain to proxy images from a live site.

## Features

- Replace media URLs in post content
- Replace attachment URLs 
- Replace URLs in image srcsets
- Settings page to configure the proxy domain

## Usage

1. Install and activate the plugin
2. Go to Settings > Media URL Proxy
3. Enter the domain you want to use for proxying media files (e.g., https://your-production-site.com)
4. Save the settings

## How It Works

The plugin will automatically replace any media URLs in your content with the configured domain. For example:
- From: https://your-local-site.local/wp-content/uploads/2024/03/cat.jpg
- To: https://your-production-site.com/wp-content/uploads/2024/03/cat.jpg

This allows you to serve images from a production domain while using a local development environment.