<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_cambiar_moneda', 'paybridge_clp_manejar_cambio_moneda' );
add_action( 'wp_ajax_nopriv_cambiar_moneda', 'paybridge_clp_manejar_cambio_moneda' );
add_action( 'wp_ajax_eliminar_sesion_moneda', 'paybridge_clp_eliminar_sesion_moneda' );
add_action( 'wp_ajax_nopriv_eliminar_sesion_moneda', 'paybridge_clp_eliminar_sesion_moneda' );

add_action( 'wp_enqueue_scripts', 'paybridge_clp_agregar_script_cambio_moneda' );

add_action( 'woocommerce_cart_totals_after_order_total', 'paybridge_clp_mostrar_total_en_dolares' );
add_action( 'woocommerce_review_order_after_order_total', 'paybridge_clp_mostrar_total_en_dolares' );

add_action( 'woocommerce_thankyou', 'paybridge_clp_limpiar_moneda_tras_pedido' );
