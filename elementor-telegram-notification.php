<?php
/*
Plugin Name: Elementor Telegram Notification
Description: Sends a Telegram notification when an Elementor form is submitted.
Version: 1.0
Author: andrei930
*/


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the class file
require_once plugin_dir_path(__FILE__) . 'class-elementor-telegram-notification-settings.php';
require_once plugin_dir_path(__FILE__) . 'class-elementor-telegram-notification.php';


class ElementorTelegramNotificationInit {
    /**
     * Constructor
     */
    public function __construct()
    {
        // Instantiate the settings class
        new Elementor_Telegram_Notification_Settings();
        // Instantiate the main class
        new Elementor_Telegram_Notification();
    }
}

add_action('elementor/loaded', function() {
    new ElementorTelegramNotificationInit();
});
