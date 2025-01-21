<?php

/*
 * Plugin Name:       Nearby Store
 * Plugin URI:        https://github.com/Tapabrata-Goswami/nearby-store
 * Description:       It's help to locate nearby store on google map.
 * Version:           1.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Tapabrata Goswami
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */


/* 
    @Plugin Activation 
*/

register_activation_hook(
    __FILE__,
    'ml_activate_plugin'
);

function ml_activate_plugin(){

}