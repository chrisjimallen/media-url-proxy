<?php
/**
 * Plugin Name: Media URL Proxy
 * Description: Replaces media URLs with a configured domain to proxy images from live site
 * Version: 1.0
 * Author: Chris Allen
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class MediaURLProxy {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'settings_init'));
    }
    
    public function init() {
        // Hook into content filters to replace media URLs
        add_filter('the_content', array($this, 'replace_media_urls'), 20);
        add_filter('wp_get_attachment_url', array($this, 'replace_attachment_url'), 20);
        add_filter('wp_calculate_image_srcset', array($this, 'replace_image_srcset'), 20);
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Media URL Proxy Settings',
            'Media URL Proxy',
            'manage_options',
            'media-url-proxy',
            array($this, 'options_page')
        );
    }
    
    public function settings_init() {
        register_setting('mediaURLProxy', 'media_url_proxy_settings');
        
        add_settings_section(
            'mediaURLProxy_section',
            'Configuration',
            array($this, 'settings_section_callback'),
            'mediaURLProxy'
        );
        
        add_settings_field(
            'proxy_domain',
            'Proxy Domain',
            array($this, 'proxy_domain_render'),
            'mediaURLProxy',
            'mediaURLProxy_section'
        );
    }
    
    public function proxy_domain_render() {
        $options = get_option('media_url_proxy_settings');
        ?>
        <input type='text' name='media_url_proxy_settings[proxy_domain]' value='<?php echo isset($options['proxy_domain']) ? esc_attr($options['proxy_domain']) : ''; ?>' placeholder="https://example.com" style="width: 400px;">
        <p class="description">Enter the domain you want to use for proxying media files (e.g., https://yoursite.com)</p>
        <?php
    }
    
    public function settings_section_callback() {
        echo '<p>Configure the domain to use for proxying media URLs.</p>';
    }
    
    public function options_page() {
        ?>
        <div class="wrap">
            <h2>Media URL Proxy Settings</h2>
            <form action='options.php' method='post'>
                <?php
                settings_fields('mediaURLProxy');
                do_settings_sections('mediaURLProxy');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Replace media URLs in post content
     */
    public function replace_media_urls($content) {
        $options = get_option('media_url_proxy_settings');
        $proxy_domain = isset($options['proxy_domain']) ? trim($options['proxy_domain']) : '';
        
        if (empty($proxy_domain)) {
            return $content;
        }
        
        // Get the upload directory URL to identify media URLs to replace
        $upload_dir = wp_upload_dir();
        $upload_base_url = $upload_dir['baseurl'];
        
        // Get the site URL to identify any site media URLs
        $site_url = home_url();
        
        // Escape special regex characters in URLs
        $upload_url_pattern = preg_quote($upload_base_url, '#');
        $site_url_pattern = preg_quote($site_url, '#');
        
        // Pattern to match URLs that contain the upload directory or site URL with media file extensions
        $pattern = '#(src|srcset|data[^=]*=|url\()(["\']?)' . $site_url_pattern . '[^"\'>\s]*\.(jpe?g|png|gif|webp|bmp|svg|ico|pdf|mp4|mov|avi|m4v|mp3|ogg|m4a|wav|pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar|7z|tar|gz)([^"\'>\s]*\s*["\']?)#i';
        
        $content = preg_replace_callback($pattern, function($matches) use ($proxy_domain, $site_url) {
            $attribute = $matches[1]; // src, srcset, etc.
            $quote = $matches[2]; // Opening quote
            $original_url = $matches[0];
            $file_path_full = str_replace([$site_url, $attribute . $quote], '', $original_url);
            $file_extension = $matches[3]; // File extension
            $additional_params = $matches[4]; // Any additional parameters after the file
            
            // Construct the new URL with the proxy domain
            $new_url = $attribute . $quote . rtrim($proxy_domain, '/') . '/' . ltrim($file_path_full, '/');
            
            return $new_url;
        }, $content);
        
        // Also handle inline styles with background-image
        $style_pattern = '#(background-image|background|content):\s*url\(\s*["\']?(' . $site_url_pattern . '[^"\'>\s]*\.(jpe?g|png|gif|webp|bmp|svg|ico))["\']?\s*\)#i';
        $content = preg_replace_callback($style_pattern, function($matches) use ($proxy_domain, $site_url) {
            $css_property = $matches[1];
            $full_url = $matches[2];
            $file_extension = $matches[3];
            
            // Get just the path part
            $path = str_replace($site_url, '', $full_url);
            
            // Construct the new URL with the proxy domain
            $new_url = $css_property . ': url("' . rtrim($proxy_domain, '/') . '/' . ltrim($path, '/') . '")';
            
            return $new_url;
        }, $content);
        
        return $content;
    }
    
    /**
     * Replace attachment URLs
     */
    public function replace_attachment_url($url) {
        $options = get_option('media_url_proxy_settings');
        $proxy_domain = isset($options['proxy_domain']) ? trim($options['proxy_domain']) : '';
        
        if (empty($proxy_domain)) {
            return $url;
        }
        
        // Check if this is a local media URL
        $site_url = home_url();
        if (strpos($url, $site_url) !== false) {
            // Get the path part of the URL
            $parsed_url = parse_url($url);
            $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
            
            // Construct the new URL with the proxy domain
            $new_url = trailingslashit(rtrim($proxy_domain, '/')) . ltrim($path, '/');
            return $new_url;
        }
        
        return $url;
    }
    
    /**
     * Replace URLs in image srcset
     */
    public function replace_image_srcset($sources) {
        $options = get_option('media_url_proxy_settings');
        $proxy_domain = isset($options['proxy_domain']) ? trim($options['proxy_domain']) : '';
        
        if (empty($proxy_domain)) {
            return $sources;
        }
        
        $site_url = home_url();
        
        foreach ($sources as $key => $source) {
            if (isset($source['url']) && strpos($source['url'], $site_url) !== false) {
                $parsed_url = parse_url($source['url']);
                $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
                
                $sources[$key]['url'] = trailingslashit(rtrim($proxy_domain, '/')) . ltrim($path, '/');
            }
        }
        
        return $sources;
    }
}

// Initialize the plugin
new MediaURLProxy();