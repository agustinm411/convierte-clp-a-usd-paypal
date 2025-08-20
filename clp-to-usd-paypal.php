<?php
/**
 * Plugin Name: paybridge-clp
 * Description: cambia automáticamente la moneda del pedido de CLP a USD cuando el usuario elige PayPal como método de pago en WooCommerce.
 * Version: 1.05
 * Author: Agustín Martínez
 * License: GPL2
 * Text Domain: paybridge-clp
 */

require plugin_dir_path( __FILE__ ) . 'includes/config/config.php';
require plugin_dir_path( __FILE__ ) . 'includes/core/payment_methods.php';
require plugin_dir_path( __FILE__ ) . 'includes/core/currency.php';
require plugin_dir_path( __FILE__ ) . 'includes/core/filters.php';
require plugin_dir_path( __FILE__ ) . 'includes/core/actions.php';
require plugin_dir_path( __FILE__ ) . 'includes/core/convert-currency.php';
require_once plugin_dir_path(__FILE__) . 'includes/updater.php';
if (!defined('ABSPATH')) {
    exit;
}

