<?php
/*
  Plugin Name: SMS.to SMS add-on for Salon Booking
  Description: Send sms bookings notifications using the provider sms.to
  Version: 1.1.1
  Plugin URI: https://www.salonbookingsystem.com/
  Author: Intergo Telecom Ltd
  Author URI: https://sms.to/
  Text Domain: slnsmstosms
  Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


define('SLNSMSTOSMS_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));
define('SLNSMSTOSMS_PLUGIN_BASENAME', plugin_basename(__FILE__));

define('SLNSMSTOSMS_VERSION', '1.1.1');
define('SLNSMSTOSMS_STORE_URL', 'https://www.salonbookingsystem.com/');
define('SLNSMSTOSMS_AUTHOR', 'Intergo Telecom Ltd');
define('SLNSMSTOSMS_ITEM_SLUG', 'smsto-addon-salon-booking');
define('SLNSMSTOSMS_ITEM_NAME', 'SMS.to SMS Addon for Salon Booking');

load_plugin_textdomain('slnsmstosms', false, dirname(plugin_basename(__FILE__)).'/languages');

spl_autoload_register('slnsmstosms_autoload');
add_action('sln.sms_provider.init', 'slnsmstosms_init_sms_provider');

/** @var SLNSMSTOSMS_Update_Manager $slnshsms_license */
$slnsmstosms_license = new SLNSMSTOSMS_Update_Manager(
    array(
        'slug'     => SLNSMSTOSMS_ITEM_SLUG,
        'basename' => SLNSMSTOSMS_PLUGIN_BASENAME,
        'name'     => SLNSMSTOSMS_ITEM_NAME,
        'version'  => SLNSMSTOSMS_VERSION,
        'author'   => SLNSMSTOSMS_AUTHOR,
        'store'    => SLNSMSTOSMS_STORE_URL,
    )
);

function slnsmstosms_init_sms_provider()
{
    global $slnsmstosms_license;
    if ($slnsmstosms_license->isValid()) {
        SLN_Enum_SmsProvider::addService('smsto', 'SMSto', 'SLNSMSTOSMS_Provider');
    }
}

function slnsmstosms_autoload($className)
{
    if (strpos($className, 'SLNSMSTOSMS_') === 0) {
        $filename = SLNSMSTOSMS_PLUGIN_DIR."/src/".str_replace("_", "/", $className).'.php';
        if (file_exists($filename)) {
            include_once($filename);
        }
    }
}
