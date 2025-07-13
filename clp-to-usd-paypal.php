<?php
/**
 * Plugin Name: paybridge-clp
 * Description: cambia automáticamente la moneda del pedido de CLP a USD cuando el usuario elige PayPal como método de pago en WooCommerce.
 * Version: 1.04
 * Author: Agustín Martínez
 * License: GPL2
 * Text Domain: paybridge-clp
 */
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';
require plugin_dir_path( __FILE__ ) . 'includes/config.php';
require_once plugin_dir_path(__FILE__) . 'includes/updater.php';
if (!defined('ABSPATH')) {
    exit;
}

function mostrar_total_en_dolares($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }


    $total_pesos = $cart->subtotal;


    $total_dolares = convertir_a_dolares($total_pesos);


    if (is_numeric($total_dolares)) {
        $cart->add_fee('Total en dólares (aprox)', $total_dolares, false);
    }
}
add_action('woocommerce_cart_calculate_fees', 'mostrar_total_en_dolares');

function convertir_a_dolares($total_pesos) {
    $precio_dolar = get_option('mi_plugin_campo_texto', '');
    if ($total_pesos < 0 || $precio_dolar <= 0) {
        return "Error: Los valores deben ser positivos y el precio del dólar debe ser mayor a 0.";
    }

    return round($total_pesos / $precio_dolar, 2);
}

add_action('woocommerce_before_checkout_process', 'cambiar_moneda_si_es_paypal');

function cambiar_moneda_si_es_paypal() {
    if (isset($_POST['payment_method']) && $_POST['payment_method'] === 'paypal') {
        cambiar_moneda_woocommerce('USD');
    }
}

