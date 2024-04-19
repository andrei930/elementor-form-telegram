<?php
/**
 * Elementor Telegram Notification Plugin
 *
 * @package ElementorTelegramNotification
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


/**
 * Elementor_Telegram_Notification class
 */
class Elementor_Telegram_Notification
{
    private $bot_token;
    private $chat_id;
    private $settings;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Telegram bot token
        $this->settings = get_option('telegram_notification_settings');
        $this->bot_token = $this->settings['telegram_bot_token'];
        $this->chat_id = $this->settings['telegram_chat_id'];

        if (strlen($this->bot_token) == 0 || strlen($this->chat_id) == 0) {
            return;
        }

        // Handle form submission
        add_action('elementor_pro/forms/new_record', function ($record, $handler) {
            $this->send_form_content_via_telegram($record, $handler);
            $this->send_vcard_via_telegram($record, $handler);
        }, 10, 2);
    }


    /**
     * Generic telegram request
     *
     * @return void
     */
    private function telegram_request($endpoint, $method, $body = [])
    {
        // Telegram API base URL
        $url = "https://api.telegram.org/bot{$this->bot_token}/{$endpoint}";
        $body['chat_id'] = $this->chat_id;


        // Initialize cURL session
        $ch = curl_init();


        // Set cURL options
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
        ];

        // If method is POST and body is provided, include body in the request
        if ($method == 'POST' && !empty($body)) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_HTTPHEADER] = ['Content-Type: multipart/form-data'];
            $options[CURLOPT_POSTFIELDS] = $body;
        }

        // Set cURL options
        curl_setopt_array($ch, $options);

        // Execute the request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        // Return the response
        return $response;
    }


    /**
     * Send form content via Telegram
     *
     * @return void
     */
    private function send_form_content_via_telegram($record, $handler)
    {
        // Extract form data
        $form_data = $record->get_formatted_data();

        // Compose message
        $message = "New form submission:\n";
        foreach ($form_data as $field_name => $field_value) {
            $message .= "$field_name: $field_value\n";
        }

        $telegram_params['text'] = $message;

        $this->telegram_request('sendMessage', 'POST', $telegram_params);
    }



    /**
     * Send a vCard file via Telegram
     *
     * @return void
     */
    private function send_vcard_via_telegram($record, $handler)
    {
        $vcard_contents = $this->generate_vcard($record);
        $vcard_file = 'contact.vcf';
        file_put_contents($vcard_file, $vcard_contents);

        $telegram_params = array(
            'document' => new CURLFile(realpath($vcard_file), 'text/vcard', 'contact.vcf'),
            'caption' => 'Sample vCard file'
        );

        $this->telegram_request('sendDocument', 'POST', $telegram_params);
        unlink($vcard_file);
    }


    /**
     * Generate a sample vCard file
     *
     * @return string vCard file contents
     */
    private function generate_vcard($record)
    {
        $fields = $record->get('fields');

        $name = $fields['name']['value'] ?? '';
        $data['name'] = $name . ' TZ LEAD';
        $data['phone'] = $fields['phone']['value'] ?? '';
        $data['email'] = $fields['email']['value'] ?? '';


        $card = "BEGIN:VCARD\r\n";
        $card .= "VERSION:3.0\r\n";
        $card .= "CLASS:PUBLIC\r\n";
        $card .= "PRODID:-//class_vCard from WhatsAPI//NONSGML Version 1//EN\r\n";
        $card .= "REV:" . date('Y-m-d H:i:s') . "\r\n";
        $card .= "FN:" . $data['name'] ." " . "\r\n";
        $card .= "N:"
                . $data['name'] . ";"
                . ''. "\r\n";

        $card .= "TITLE:" . ' ' . "\r\n";
        $card .= "ORG:" . ' ' . "\r\n";
        $card .= "EMAIL;type=INTERNET,pref:" . $data['email'] . "\r\n";
        $card .= "TEL;type=WORK,voice:" . $data['phone'] . "\r\n";
        $card .= "END:VCARD\r\n";

        return $card;
    }

}
