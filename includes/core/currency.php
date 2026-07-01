<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handler AJAX: activa la moneda temporal de la sesión (USD o CLP).
 */
function paybridge_clp_manejar_cambio_moneda(): void {
	check_ajax_referer( 'cambio_moneda_nonce' );

	$moneda = isset( $_POST['moneda'] )
		? strtoupper( sanitize_text_field( wp_unslash( $_POST['moneda'] ) ) )
		: 'USD';

	if ( ! in_array( $moneda, array( 'USD', 'CLP' ), true ) ) {
		wp_send_json_error( array( 'message' => __( 'Moneda no permitida.', 'paybridge-clp' ) ), 400 );
	}

	if ( ! function_exists( 'WC' ) || ! WC()->session ) {
		wp_send_json_error( array( 'message' => __( 'La sesión de WooCommerce no está disponible.', 'paybridge-clp' ) ), 500 );
	}

	WC()->session->set( 'moneda_temporal', $moneda );

	wp_send_json_success(
		array(
			'message'  => __( 'Moneda cambiada.', 'paybridge-clp' ),
			'currency' => $moneda,
		)
	);
}

/**
 * Handler AJAX: elimina la moneda temporal de la sesión.
 */
function paybridge_clp_eliminar_sesion_moneda(): void {
	check_ajax_referer( 'cambio_moneda_nonce' );

	if ( function_exists( 'WC' ) && WC()->session ) {
		WC()->session->set( 'moneda_temporal', null );
		WC()->session->__unset( 'moneda_temporal' );
	}

	wp_send_json_success( array( 'message' => __( 'Sesión de moneda eliminada.', 'paybridge-clp' ) ) );
}

/**
 * Encola el script de cambio de moneda solo en carrito y checkout.
 */
function paybridge_clp_agregar_script_cambio_moneda(): void {
	if ( ! is_cart() && ! is_checkout() ) {
		return;
	}

	$handle      = 'paybridge-clp-cambio-moneda';
	$script_rel  = '../js/cambio-moneda.js';
	$script_path = plugin_dir_path( __FILE__ ) . $script_rel;
	$script_url  = plugin_dir_url( __FILE__ ) . $script_rel;

	// filemtime como cache-busting; si no existe, usa la versión del plugin.
	$version = file_exists( $script_path ) ? (string) filemtime( $script_path ) : PAYBRIDGE_CLP_VERSION;

	wp_enqueue_script( $handle, $script_url, array(), $version, true );

	wp_localize_script(
		$handle,
		'cambioMonedaData',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'cambio_moneda_nonce' ),
		)
	);
}

/**
 * Limpia la moneda temporal una vez completado el pedido.
 */
function paybridge_clp_limpiar_moneda_tras_pedido(): void {
	if ( function_exists( 'WC' ) && WC()->session ) {
		WC()->session->set( 'moneda_temporal', null );
		WC()->session->__unset( 'moneda_temporal' );
	}
}
