<?php

/**
 * Validate the address in Woocommerce with the Google maps api
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

class AddressChecker_validate
{


    /**
     * AddressChecker_validate constructor.
     */
    public function __construct()
    {
        if(get_option('address_checker_settings_api_valid') == 'false'){
            return;
        }

        add_action('woocommerce_before_checkout_process', array($this, 'custom_validation_process'));

    }


    /**
     *  Validate the address fields of the billing address and the shipping address
     */
    public function custom_validation_process()
    {


        global $woocommerce;

        if (isset($_POST['billing_address_1']) and $_POST['billing_address_1'] != '') {

          $billing_address_1= sanitize_text_field($_POST['billing_address_1']);
          if(! $billing_address_1){
            $billing_address_1 ='';
          }

          $billing_postcode= sanitize_text_field( $_POST['billing_postcode']);
          if(! $billing_postcode){
            $billing_postcode ='';
          }

          $billing_city= sanitize_text_field($_POST['billing_city']);
          if(! $billing_city){
            $billing_city ='';
          }

          $billing_country= sanitize_text_field($_POST['billing_country']);

          if(! $billing_country){
            $billing_country ='';
          }
            $this->validateAdres(
                $billing_address_1,
                $billing_postcode,
                $billing_city,
                $billing_country
            );
        }
        if (isset($_POST['ship_to_different_address'])) {
          $shipping_address_1= sanitize_text_field($_POST['shipping_address_1']);
          if(! $billing_address_1){
            $shipping_address_1 ='';
          }

          $shipping_postcode= sanitize_text_field( $_POST['shipping_postcode']);
          if(! $shipping_postcode){
            $shipping_postcode ='';
          }

          $shipping_city= sanitize_text_field($_POST['shipping_city']);
          if(! $shipping_city){
            $shipping_city ='';
          }

          $billing_country= sanitize_text_field($_POST['billing_country']);
          if(! $billing_country){
            $billing_country ='';
          }

            $this->validateAdres(
              $shipping_address_1,
              $shipping_postcode,
              $shipping_city,
              $shipping_country
            );
        }
    }


    /**
     * Scream the mediation in woocommerce
     * @param $scream
     */
    public function scream_wrong($scream)
    {
        global $woocommerce;
        if (function_exists('wc_add_notice')) {
            wc_add_notice($scream, 'error');
        }
        else {
            $woocommerce->add_error(print_r($_POST));
        }
    }


    /**
     * Get the zipcode from of the Address field
     *
     * @param $straat
     * @param $number
     * @param $plaats
     * @param $api
     * @return array|null
     */
    public function getPostcode($straat, $number, $plaats, $api)
    {

        $url = $straat." ".$number." ".$plaats;
        $url = rawurlencode($url);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$url."&key=".$api;


        $request = wp_remote_get($url);
        $body = wp_remote_retrieve_body($request);

        $dump = json_decode($body, true);

        $stuff = array();


        if ($dump['status'] == 'ZERO_RESULTS') {

            return null;
        }
        else {
            array_push(
                $stuff,
                str_replace(' ', '', strtoupper($dump['results'][0]['address_components'][6]['long_name']))
            );

            array_push($stuff, $dump['results'][0]['address_components'][4]['long_name']);
        }

        return $stuff;
    }


    /**
     *
     * Validates the address.
     * This can only be used in the Netherlands
     *
     * @param $adresField
     * @param $postalcodeField
     * @param $cityField
     */
    public function validateAdres($adresField, $postalcodeField, $cityField, $country)
    {


        if ($country != "NL") {
            $this->scream_wrong("This can only be used for The Netherlands");
        }

        $api_code = get_option('wcp_settings_api_code');
        $postcode = $postalcodeField = str_replace(' ', '', strtoupper($postalcodeField));
        $city = $cityField;
        $adress = $adresField;
        $address = "";
        $number = "";
        $matches = array();

        /*
         * Validate the address field
         */
        if (preg_match('/(?P<address>[^\d]+) (?P<number>\d+.?)/', $adress, $matches)) {
            $address = $matches['address'];
            $number = $matches['number'];
        } else { // no number found, it is only address
            $address = $adress;
        }


        if ($number == "") {
            $this->scream_wrong("Enter the Addressfield!");

        }
        /*
         * Get the zipcode of the adressfield or scream something
         */
        elseif (preg_match('~\A[1-9]\d{3} ?[a-zA-Z]{2}\z~', $postalcodeField)) {
            $api_postcode = $this->getPostcode($address, $number, $city, $api_code);

            if ($api_postcode[0] != $postcode) {
                $this->scream_wrong("Your address information has been entered incorrectly");
            }
        } else {
            $this->scream_wrong("Your address information has been entered incorrectly");

        }
    }
}
