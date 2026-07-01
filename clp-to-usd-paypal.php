<?php
/**
 * Plugin Name: PayBridge CLP
 * Description: Convierte automáticamente la moneda del pedido de CLP a USD cuando el cliente elige PayPal como método de pago en WooCommerce.
 * Version: 2.0
 * Author: Agustín Martínez
 * License: GPL-2.0-or-later
 * Text Domain: paybridge-clp
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PAYBRIDGE_CLP_VERSION', '2.0' );
define( 'PAYBRIDGE_CLP_PLUGIN_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'includes/updater.php';

// Compatibilidad con HPOS y con los bloques de carrito/checkout.
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
	}
} );

add_action( 'plugins_loaded', 'paybridge_clp_iniciar' );

function paybridge_clp_iniciar(): void {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-error"><p>'
				. esc_html__( 'PayBridge CLP requiere que WooCommerce esté instalado y activo.', 'paybridge-clp' )
				. '</p></div>';
		} );
		return;
	}

	require_once plugin_dir_path( PAYBRIDGE_CLP_PLUGIN_FILE ) . 'includes/config/config.php';
	require_once plugin_dir_path( PAYBRIDGE_CLP_PLUGIN_FILE ) . 'includes/core/convert-currency.php';
	require_once plugin_dir_path( PAYBRIDGE_CLP_PLUGIN_FILE ) . 'includes/core/currency.php';
	require_once plugin_dir_path( PAYBRIDGE_CLP_PLUGIN_FILE ) . 'includes/core/filters.php';
	require_once plugin_dir_path( PAYBRIDGE_CLP_PLUGIN_FILE ) . 'includes/core/actions.php';
}
