<?php

/**
 * Gives the notifications in the plugin
 *
 * @link       http://cherement.nl/demo
 * @since      1.0.0
 *
 * @package    Address-Checker
 * @subpackage Address-Checker/includes
 */
if (!defined('ABSPATH')) {
    exit;
}

class AddressCheckerNotification
{

    /**
     * AddressCheckerNotification constructor.
     */
    public function __construct()
    {
        $key= get_option('address_checker_settings_api_code');

        self::api_notice($key);
        $this->woocoomerce_available();



    }

    /**
     * Screams in Wordpress the notification to get the google maps api code
     * @return
     */
    public static function api_code_maps()
    {

        $class = 'notice notice-error';
        $message = __('The Google API Key is invalid in Address-Checker', 'address-checker');

        return printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));

    }



    /**
     * @param $straat
     * @param $number
     * @param $plaats
     * @param $api
     * @return array|null
     */
    public static function getPostcode($straat, $number, $plaats, $api)
    {

        $url = $straat." ".$number." ".$plaats;
        $url = rawurlencode($url);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$url."&key=".$api;

        $ch = curl_init();
// Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
// Execute
        $result = curl_exec($ch);
// Closing
        curl_close($ch);

        $dump = (json_decode($result, true));
        $stuff = array();

        if ($dump['status'] == 'ZERO_RESULTS') {
            return null;
        }
        if ($dump['status'] == 'REQUEST_DENIED') {
            return 'REQUEST_DENIED';
        } else {
            array_push(
                $stuff,
                str_replace(' ', '', strtoupper($dump['results'][0]['address_components'][6]['long_name']))
            );

            array_push($stuff, $dump['results'][0]['address_components'][4]['long_name']);
        }

        return $stuff;
    }



public static function api_notice($key){
    if(get_option('address_checker_settings_api_valid') ==''){
        add_option('address_checker_settings_api_valid', 'false');
    }

    $apikey = get_option('address_checker_settings_api_code');

    //$valid= get_option('address_checker_settings_api_valid');

    $valid = self::getPostcode("stationsplein", 1, "Amsterdam", $key);


    if ($valid == "REQUEST_DENIED") {

        add_action('admin_notices', array('AddressCheckerNotification', 'api_code_maps'), 90, 90);
        update_option('address_checker_settings_api_valid', 'false');

    } else {
        update_option('address_checker_settings_api_valid', 'true');
    }
}



private function woocoomerce_available(){

    if ( class_exists( 'WooCommerce' ) ) {
        $class = 'notice notice-error';
        $message = __('Woocommerce is required in Address-Checker', 'address-checker');

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));

    }





}
}
