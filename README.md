# Media URL Proxy

A WordPress plugin that allows you to replace media URLs with a configured domain to proxy images from a live site.

## Features

- Replace media URLs in post content
- Replace attachment URLs 
- Replace URLs in image srcsets
- Settings page to configure the proxy domain

## Installation

1. Install and activate the plugin in WordPress
2. Go to Settings > Media URL Proxy
3. Enter the domain you want to use for proxying media files (e.g., https://yoursite.com)
4. Save the settings

## How It Works

The plugin will automatically replace any media URLs in your content with the configured domain. For example:
- From: https://surfturf.ddev.site/wp-content/uploads/2024/03/cat.jpg
- To: https://yoursite.com/wp-content/uploads/2024/03/cat.jpg

This allows you to serve images from a production domain while using a local development environment.

## Usage Example

Once configured, the plugin will transform image tags like:
```html
<img src="https://local-site.com/wp-content/uploads/2024/03/cat.jpg" alt="Cat">
```

To:
```html
<img src="https://yoursite.com/wp-content/uploads/2024/03/cat.jpg" alt="Cat">
```

## Development

This plugin was created to help with WordPress development when working with local environments that need to access media from a live site.