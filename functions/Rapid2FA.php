<?php
/**
 * PHP Class for handling Rapid 2FA integration.
 *
 * @author Areeb Majeed
 * @copyright 2017 Rapid 2FA
 * @license https://opensource.org/licenses/MIT MIT License
 *
 * @link https://rapid2fa.com/
 */
class Rapid2FA {
    private $api_key;
    private $api_secret;
    private $api_endpoint = "https://rapid2fa.com/api/";
    /**
     * Initialize the class with API Key and Secret.
     * Sets the API Key and API Secret.
     */
    function __construct($api_key, $api_secret) {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }
    /**
     * Sends POST data to a specified URL.
     * Returns the data.
     */
    function postData($url, $fields) {
        $post_data_string = "";
        foreach ($fields as $key => $value) {
            $post_data_string.= $key . '=' . urlencode($value) . '&';
        }
        rtrim($post_data_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36');
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data_string);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    /**
     * Generate a settings page by sending POST data to Rapid 2FA.
     * Throw exception on error.
     */
    function generateSettingsPage($user_id) {
        $data = array();
        $data['api_key'] = $this->api_key;
        $data['api_secret'] = $this->api_secret;
        $data['user'] = $user_id;
        $data['method'] = 'edit_profile_2fa';
        $response = $this->postData($this->api_endpoint, $data);
        $response = json_decode($response, true);
        if ($response['status_code'] == 'ER-05200') {
            return $response;
        } else {
            throw new Exception($response['status_code']);
        }
    }
    /**
     * Generate a hosted page or simply a user session.
     * Throw exception on error.
     */
    function generate2FASession($user_id) {
        $data = array();
        $data['api_key'] = $this->api_key;
        $data['api_secret'] = $this->api_secret;
        $data['user'] = $user_id;
        $data['method'] = 'create_2fa_session';
        $response = $this->postData($this->api_endpoint, $data);
        $response = json_decode($response, true);
        if ($response['status_code'] == 'ER-05200') {
            return $response;
        } else {
            throw new Exception($response['status_code']);
        }
    }
    /**
     * Validate the session hash returned by the user.
     * Throw exception on error.
     */
    function handleVerification($user_id,$hash) {
        $data = array();
        $data['api_key'] = $this->api_key;
        $data['api_secret'] = $this->api_secret;
        $data['user'] = $user_id;
        $data['hash'] = $hash;
        $data['method'] = 'verify_authentication';
        $response = $this->postData($this->api_endpoint, $data);
        $response = json_decode($response, true);
        if ($response['status_code'] == 'ER-05200') {
            return $response;
        } else {
            throw new Exception($response['status_code']);
        }
    }
}
?>