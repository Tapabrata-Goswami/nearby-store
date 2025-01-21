<?php

/*
 * Plugin Name:       Nearby Store
 * Plugin URI:        https://github.com/Tapabrata-Goswami/nearby-store
 * Description:       It's help to locate nearby store on google map.
 * Version:           1.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Tapabrata Goswami
 * Author URI:        https://tapabrata.me/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */


/* 
    @Plugin Activation 
*/


register_activation_hook(
    __FILE__,
    'nl_activate_plugin'
);

function nl_activate_plugin(){
    
}

include plugin_dir_path(__FILE__) . 'class-maps-handle.php';
include plugin_dir_path(__FILE__) . 'class-shortcode.php';


/* 
    @Adding to menu
*/
function nl_plugin_show_menu(){
    add_menu_page(
        "Nearby Store",
        "Nearby Store",
        "read",
        "nearby-store",
        "nl_nearby_store_admin_page_content",
        plugin_dir_url(__FILE__) . 'assets/map_icon.png'
    );
}
add_action('admin_menu','nl_plugin_show_menu');
function nl_custom_admin_styles() {
    echo '
    <style>
        .toplevel_page_nearby-store div.wp-menu-image img {
            height: 20px; /* Adjust height */
            width: 20px;  /* Adjust width */
        }
    </style>';
}
add_action('admin_head', 'nl_custom_admin_styles');

/* 
    @Admin content function
*/
function nl_nearby_store_register_settings() {
    // Register options
    register_setting('nl_nearby_store_options_group', 'store_name');
    register_setting('nl_nearby_store_options_group', 'store_location');
}
add_action('admin_init', 'nl_nearby_store_register_settings');

function nl_nearby_store_admin_page_content() {
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Nearby Store</h1>
        <p>Welcome to the Nearby Store plugin settings page. Customize your options below.</p>
        
        <?php
        // Show success or error messages after settings are saved
        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
            echo '<div class="updated"><p>Settings saved successfully!</p></div>';
        }
        ?>
        
        <!-- Example Form -->
        <form method="post" action="options.php">
            <?php
            // Output security fields for the settings group
            settings_fields('nl_nearby_store_options_group');
            do_settings_sections('nl_nearby_store');
            ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="store_name">Center Country Name</label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            id="store_name" 
                            name="store_name" 
                            class="regular-text" 
                            placeholder="Center city name"
                            value="<?php echo esc_attr(get_option('store_name')); ?>"
                            required
                        >
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="store_location">Google Maps API Key</label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            id="store_location" 
                            name="store_location" 
                            class="regular-text" 
                            placeholder="Google maps api key"
                            value="<?php echo esc_attr(get_option('store_location')); ?>"
                            required
                        >
                       
                        
                    </td>
                </tr>
            </table>
            
            <button type="submit" class="button button-primary">Save Changes</button>
        </form>
    </div>
    <?php
}

function nl_nearby_store_add_success_message() {
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
        add_settings_error(
            'nl_nearby_store_options_group', 
            'settings_updated', 
            'Settings saved successfully!', 
            'updated'
        );
    }
}
add_action('admin_notices', 'nl_nearby_store_add_success_message');



// Register the REST API endpoint
function nl_register_api_endpoint() {
    register_rest_route('nl/v1', '/nearby-installers/', [
        'methods' => ['GET', 'POST'],  // Support both GET and POST methods
        'callback' => 'nl_get_nearby_installers',
    ]);
}
add_action('rest_api_init', 'nl_register_api_endpoint');

add_shortcode('nl_nearby_installer', 'nl_nearby_installer_shortcode');