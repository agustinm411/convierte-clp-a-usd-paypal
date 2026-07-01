<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cambia el código de moneda de WooCommerce según la sesión.
 */
add_filter( 'woocommerce_currency', function ( string $currency ): string {
	if ( function_exists( 'WC' ) && WC()->session ) {
		$tmp = WC()->session->get( 'moneda_temporal' );
		if ( is_string( $tmp ) && in_array( $tmp, array( 'USD', 'CLP' ), true ) ) {
			return $tmp;
		}
	}
	return $currency;
} );

/**
 * Convierte los precios de productos y variaciones cuando la sesión está en USD.
 * Sin esto, WooCommerce cobraría el monto en CLP como si fueran dólares.
 */
$paybridge_clp_filtros_precio = array(
	'woocommerce_product_get_price',
	'woocommerce_product_get_regular_price',
	'woocommerce_product_get_sale_price',
	'woocommerce_product_variation_get_price',
	'woocommerce_product_variation_get_regular_price',
	'woocommerce_product_variation_get_sale_price',
	'woocommerce_variation_prices_price',
	'woocommerce_variation_prices_regular_price',
	'woocommerce_variation_prices_sale_price',
);

foreach ( $paybridge_clp_filtros_precio as $paybridge_clp_filtro ) {
	add_filter( $paybridge_clp_filtro, 'paybridge_clp_filtrar_precio', 99 );
}
unset( $paybridge_clp_filtros_precio, $paybridge_clp_filtro );

function paybridge_clp_filtrar_precio( $precio ) {
	if ( '' === $precio || ! is_numeric( $precio ) || ! paybridge_clp_usd_activo() ) {
		return $precio;
	}

	$convertido = paybridge_clp_convertir_a_usd( $precio );

	return ( false === $convertido ) ? $precio : $convertido;
}

/**
 * Incluye la moneda activa en el hash de precios de variaciones para que
 * WooCommerce no sirva precios cacheados de la otra moneda.
 */
add_filter( 'woocommerce_get_variation_prices_hash', function ( array $hash ): array {
	$hash['paybridge_clp_moneda'] = paybridge_clp_usd_activo() ? 'USD' : 'CLP';
	return $hash;
} );

/**
 * Convierte los cupones de monto fijo (definidos en CLP) cuando la sesión
 * está en USD. Los cupones porcentuales no se tocan: un 10% sigue siendo 10%.
 */
add_filter( 'woocommerce_coupon_get_amount', function ( $monto, $cupon ) {
	if ( ! $cupon->is_type( array( 'fixed_cart', 'fixed_product' ) ) ) {
		return $monto;
	}
	return paybridge_clp_filtrar_precio( $monto );
}, 99, 2 );

/**
 * Los montos de gasto mínimo/máximo del cupón también están definidos en CLP,
 * así que se convierten para que la validación funcione con el carrito en USD.
 */
add_filter( 'woocommerce_coupon_get_minimum_amount', 'paybridge_clp_filtrar_precio', 99 );
add_filter( 'woocommerce_coupon_get_maximum_amount', 'paybridge_clp_filtrar_precio', 99 );

/**
 * Convierte los costos de envío cuando la sesión está en USD.
 */
add_filter( 'woocommerce_package_rates', function ( array $rates ): array {
	if ( ! paybridge_clp_usd_activo() ) {
		return $rates;
	}

	$tipo_cambio = paybridge_clp_obtener_tipo_cambio();
	if ( $tipo_cambio <= 0 ) {
		return $rates;
	}

	foreach ( $rates as $rate ) {
		$rate->set_cost( round( (float) $rate->get_cost() / $tipo_cambio, 2 ) );
		$rate->set_taxes( array_map(
			function ( $impuesto ) use ( $tipo_cambio ) {
				return round( (float) $impuesto / $tipo_cambio, 2 );
			},
			$rate->get_taxes()
		) );
	}

	return $rates;
}, 99 );
