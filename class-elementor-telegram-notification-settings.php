<?php
/**
 * Elementor Telegram Notification Settings
 *
 * @package ElementorTelegramNotification
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor_Telegram_Notification_Settings class
 */
class Elementor_Telegram_Notification_Settings
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Register settings
        add_action('admin_init', array($this, 'register_settings'));

        // Add settings menu
        add_action('admin_menu', array($this, 'add_settings_menu'));
    }

    /**
    * Add submenu item under Elementor menu
    */
    public function add_settings_menu()
    {
        add_options_page(
            'Elementor Form Telegram Notification Settings',
            'Elementor Form Telegram',
            'manage_options',
            'elementor-form-telegram-notification-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Render the settings page
     */
    public function render_settings_page()
    {
        ?>
        <div class="wrap">
            <h1>Telegram Notification Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('telegram_notification_settings_group'); ?>
                <?php do_settings_sections('elementor-form-telegram-notification-settings'); ?>
                <?php submit_button('Save Settings'); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register settings and fields
     */
    public function register_settings()
    {
        // Register settings group
        register_setting('telegram_notification_settings_group', 'telegram_notification_settings');


        add_settings_section(
            'telegram_notification_settings_section',
            'Telegram Bot Settings',
            array($this, 'render_settings_section_callback'),
            'elementor-form-telegram-notification-settings'
        );

        // Add bot token field
        add_settings_field(
            'telegram_bot_token',
            'Bot Token',
            array($this, 'render_bot_token_field'),
            'elementor-form-telegram-notification-settings',
            'telegram_notification_settings_section'
        );

        // Add chat ID field
        add_settings_field(
            'telegram_chat_id',
            'Chat ID',
            array($this, 'render_chat_id_field'),
            'elementor-form-telegram-notification-settings',
            'telegram_notification_settings_section'
        );
    }

    /**
     * Callback function for the settings section
     */
    public function render_settings_section_callback()
    {
        echo 'Enter your Telegram bot settings below:';
    }

    /**
     * Callback function for the bot token field
     */
    public function render_bot_token_field()
    {
        $settings = get_option('telegram_notification_settings');
        $token = isset($settings['telegram_bot_token']) ? $settings['telegram_bot_token'] : '';
        echo "<input type='text' name='telegram_notification_settings[telegram_bot_token]' value='$token' />";
    }

    /**
     * Callback function for the chat ID field
     */
    public function render_chat_id_field()
    {
        $settings = get_option('telegram_notification_settings');
        $chat_id = isset($settings['telegram_chat_id']) ? $settings['telegram_chat_id'] : '';
        echo "<input type='text' name='telegram_notification_settings[telegram_chat_id]' value='$chat_id' />";
    }
}
