<?php

function nl_nearby_installer_shortcode(){
    function add_font_awesome() {
        wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css', array(), null, 'all' );
    }
    add_action( 'wp_enqueue_scripts', 'add_font_awesome' );
    ?>
<style>
    #map {
        width: 100%;
        height: 95vh;
        position: relative;
    }

    #search-container {
        position: absolute;
        top: 25px;
        left: 18%;
        transform: translateX(-50%);
        z-index: 9;
        background-color: white;
    }

    #search-container input {
        padding: 8px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        flex: 1;
    }

    #search-container button {
        padding: 8px 12px;
        font-size: 14px;
        background-color: #4285F4;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    #search-container button:hover {
        background-color: #357ae8;
    }

    /* Custom marker styles */
    .custom-marker {
        position: absolute;
        background-color: #4285F4;
        color: white;
        padding: 10px;
        height: 10px;
        width: 10px;
        border-radius: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        transform: translate(-50%, -100%);
    }

    .custom-marker:after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 8px 6px 0 6px;
        border-color: #4285F4 transparent transparent transparent;
        transform: translateX(-50%);
    }

    .main {
        position: relative;
    }

    .ln-find-showroom {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 16px 24px;
        max-width: 400px;
    }

    .ln-find-showroom h2 {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 16px;
    }

    .ln-search-bar {
        display: flex;
        align-items: center;
        background: #f1f1f1;
        border-radius: 8px;
        padding: 8px;
        margin-bottom: 16px;
    }

    .ln-search-bar .ln-icon {
        margin-right: 8px;
        color: #555;
    }

    .ln-search-bar input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        font-size: 14px;
    }

    .ln-search-bar button {
        background: #007bff;
        border: none;
        padding: 8px;
        border-radius: 8px;
        color: white;
        cursor: pointer;
    }

    .ln-search-bar button i {
        font-size: 14px;
    }

    .ln-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .ln-filters label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        cursor: pointer;
    }

    .ln-filters input[type="checkbox"] {
        accent-color: #007bff;
    }

    .ln-filters .ln-info-icon {
        background: #ddd;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        text-align: center;
        font-size: 12px;
        line-height: 16px;
        cursor: help;
    }

    .ln-card {
        position: absolute;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        /* max-width: 400px; */
        display: flex;
        flex-direction: column;
        gap: 5px;
        right: 30px;
        bottom: 30px;
    }

    .ln-header {

    }
    .ln-header > h2{
        margin: 0 ;
    }

    .ln-content {
        font-size: 14px;
        color: #555;
        display: flex;
        flex-direction: column;
        gap: 0px;
    }

    .ln-link {
        color: #007bff;
        text-decoration: none;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .ln-link:hover {
        text-decoration: underline;
    }
    .loading-spinner {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1;
        background: rgba(255, 255, 255, 0.8);
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }   

    .spinner {
        border: 8px solid #f3f3f3; /* Light gray */
        border-top: 8px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

</style>
<div class="main">
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
        <div class="ln-filters">
            <label>
                <input type="checkbox" />
                PREMIUM PARTNER
                <span class="ln-info-icon" title="Information about Premium Partner">?</span>
            </label>
            <label>
                <input type="checkbox" />
                PARTNER STANDARD
                <span class="ln-info-icon" title="Information about Partner Standard">?</span>
            </label>
            <label>
                <input type="checkbox" />
                SMART HOME PARTNER
                <span class="ln-info-icon" title="Information about Smart Home Partner">?</span>
            </label>
        </div>
    </div>
    <div id="loading-spinner" class="loading-spinner" style="display: none;">
        <div class="spinner"></div>
    </div>
    <div id="map"></div>
    <div class="ln-card" id="info-card" style="display:none">
        <!-- <div class="ln-header">
            <h2>ELMONT</h2>
        </div>
        <div class="ln-content">
            <p><strong>Address and Contact:</strong></p>
            <p>AUGUST 15, 43, 96500<br>Sochaczew</p>
            <a href="#" class="ln-link">
                <span>📍</span> Plan Your Route
            </a>
            <p>📞 504088796</p>
            <a href="mailto:elmont@oknoplast.com.pl" class="ln-link">
                ✉️ elmont@oknoplast.com.pl
            </a>
        </div> -->
    </div>
</div>
    <?php

    function nl_enqueue_scripts() {
        // Enqueue the first JavaScript file
        wp_enqueue_script(
            'nl-script-one', // Handle for the first script
            'https://maps.googleapis.com/maps/api/js?key=', // URL to the first script
            array('jquery'), // Dependencies (optional, if your script uses jQuery)
            null, // Version number (set null to use the default version)
            true // Load the script in the footer (recommended)
        );
    
        // Enqueue the second JavaScript file
        wp_enqueue_script(
            'nl-script-two', // Handle for the second script
            plugin_dir_url(__FILE__) . 'js/map-handle.js', // URL to the second script
            array('jquery'), // Dependencies (optional, if your script uses jQuery)
            null, // Version number (set null to use the default version)
            true // Load the script in the footer (recommended)
        );
    }
    
    // Hook the function into the 'wp_enqueue_scripts' action
    add_action('wp_enqueue_scripts', 'nl_enqueue_scripts');

}