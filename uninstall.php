<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {

    exit;

    delete_option('address_checker_settings_api_valid');
    delete_option('address_checker_settings_api_code');
}