<?php
/**
 * Plugin Name: Squash Stats Dashboard
 * Plugin URI: https://stats.squashplayers.app
 * Description: Embeds the Squash Stats Dashboard from stats.squashplayers.app into WordPress using shortcode [squash_stats_dashboard]
 * Version: 1.3.0
 * Author: Itomic Apps
 * Author URI: https://www.itomic.com.au
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI: https://github.com/itomicspaceman/spa-stats-dashboard
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load the updater class
require_once plugin_dir_path(__FILE__) . 'includes/class-plugin-updater.php';

class Squash_Stats_Dashboard {
    
    private $dashboard_url = 'https://stats.squashplayers.app';
    
    public function __construct() {
        // Register shortcode
        add_shortcode('squash_stats_dashboard', array($this, 'render_dashboard_shortcode'));
    }
    
    /**
     * Render the dashboard shortcode using iframe
     * 
     * This approach provides complete isolation between the dashboard and WordPress:
     * - No JavaScript conflicts
     * - No CSS conflicts
     * - No global variable pollution
     * - Geolocation works properly
     * - Uses postMessage API for dynamic height adjustment (no scrollbars)
     */
    public function render_dashboard_shortcode($atts) {
        // Parse shortcode attributes
        $atts = shortcode_atts(array(
            'class' => '',      // Allow custom CSS classes
        ), $atts);
        
        // Generate unique ID for this iframe instance
        $iframe_id = 'squash-dashboard-' . uniqid();
        
        // Build iframe HTML
        $html = sprintf(
            '<iframe 
                id="%s"
                src="%s" 
                width="100%%" 
                style="border: none; display: block; overflow: hidden; min-height: 500px;"
                frameborder="0"
                scrolling="no"
                class="squash-dashboard-iframe %s"
                loading="lazy"
                sandbox="allow-scripts allow-same-origin allow-popups"
                title="Squash Stats Dashboard">
            </iframe>',
            esc_attr($iframe_id),
            esc_url($this->dashboard_url),
            esc_attr($atts['class'])
        );
        
        // Add postMessage listener for dynamic height adjustment
        $html .= sprintf(
            '<script>
            (function() {
                var iframe = document.getElementById("%s");
                
                // Listen for height messages from the iframe
                window.addEventListener("message", function(event) {
                    // Security: verify origin
                    if (event.origin !== "https://stats.squashplayers.app") {
                        return;
                    }
                    
                    // Check if this is a height update message
                    if (event.data && event.data.type === "squash-dashboard-height") {
                        iframe.style.height = event.data.height + "px";
                        console.log("Dashboard height updated:", event.data.height);
                    }
                });
                
                // Fallback: if no height message received after 5 seconds, set a default height
                setTimeout(function() {
                    if (iframe.style.height === "" || iframe.style.height === "500px") {
                        iframe.style.height = "3000px";
                        console.log("Dashboard height fallback applied");
                    }
                }, 5000);
            })();
            </script>',
            esc_js($iframe_id)
        );
        
        return $html;
    }
}

// Initialize the plugin
new Squash_Stats_Dashboard();

// Initialize the updater
if (is_admin()) {
    new Squash_Stats_Dashboard_Updater(
        plugin_basename(__FILE__),
        'itomicspaceman/spa-stats-dashboard'
    );
}
