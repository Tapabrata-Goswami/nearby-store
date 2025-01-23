<?php

function nl_nearby_installer_shortcode(){
    function add_font_awesome() {
        wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css', array(), null, 'all' );
    }
    add_action( 'wp_enqueue_scripts', 'add_font_awesome' );
    function my_plugin_enqueue_styles() {
        wp_enqueue_style(
            'my-plugin-style', 
            plugin_dir_url(__FILE__) . 'assets/css/style.css',
            array(), 
            '1.0.0',
            'all'
        );
    }
    add_action('wp_enqueue_scripts', 'my_plugin_enqueue_styles'); // Hook for frontend styles
    
    ?>

<div class="main">
    <!-- Search Container -->
    <div class="ln-find-showroom" id="search-container">
        <h2>FIND A SHOWROOM</h2>
        <div class="ln-search-bar">
            <div class="ln-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <input type="text" placeholder="Enter your location..." id="search-bar" />
            <button onclick="searchLocation()">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <!-- Search Result Container -->
    <div class="search-result-container" id="all-reslut-on-map">
        <!-- <div class="search-result" id="single-search-result">
            <div class="ln-header">
                <h2>${markerData.title}</h2>
            </div>
            <div class="ln-content">
                <p class="ln-link-add">${markerData.street}, ${markerData.post_code} ${markerData.city}</p>
                <a class="ln-link" href="tel:${markerData.phone}"><i class="fa-solid fa-phone"></i> ${markerData.phone}</a>
            </div>
        </div> -->
    </div>

    <!-- Loading Spinner -->
    <div id="loading-spinner" class="loading-spinner" style="display: none;">
        <div class="spinner"></div>
    </div>

    <!-- Map -->
    <div id="map"></div>

    <!-- Information Card -->
    <div class="ln-card" id="info-card" style="display: none;"></div>
</div>


<script src='https://maps.googleapis.com/maps/api/js?key=AIzaSyDbOwo3HPw7YB8g54d3xi7HLENgbOpEjzk&libraries=places&callback=initAutocomplete' async>

</script>
    <?php

    function nl_enqueue_scripts() {
        wp_enqueue_script(
            'nl-script-two', // Handle for the second script
            plugin_dir_url(__FILE__) . 'js/map-handle.js', // URL to the second script
            array(), // Dependencies (optional, if your script uses jQuery)
            null, // Version number (set null to use the default version)
            true // Load the script in the footer (recommended)
        );
    }
    
    // Hook the function into the 'wp_enqueue_scripts' action
    add_action('wp_enqueue_scripts', 'nl_enqueue_scripts');

}