<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Indica si la sesión actual tiene activa la conversión a USD.
 */
function paybridge_clp_usd_activo(): bool {
	return function_exists( 'WC' )
		&& WC()->session
		&& 'USD' === WC()->session->get( 'moneda_temporal' );
}

/**
 * Devuelve cuántos CLP equivalen a 1 USD.
 *
 * Si la opción de API está activa, consulta mindicador.cl y cachea el valor
 * 6 horas en un transient; cada valor obtenido correctamente de la API
 * sobreescribe además el valor manual configurado. Si la API falla, se usa
 * el valor manual.
 *
 * @return float Tipo de cambio, o 0.0 si no hay un valor válido configurado.
 */
function paybridge_clp_obtener_tipo_cambio(): float {
	if ( 'yes' === get_option( 'paybridge_clp_usar_api' ) ) {
		$valor_api = get_transient( 'paybridge_clp_dolar_api' );

		if ( false === $valor_api ) {
			$respuesta = wp_remote_get( 'https://mindicador.cl/api/dolar', array( 'timeout' => 10 ) );

			if ( ! is_wp_error( $respuesta ) && 200 === wp_remote_retrieve_response_code( $respuesta ) ) {
				$datos     = json_decode( wp_remote_retrieve_body( $respuesta ), true );
				$valor_api = isset( $datos['serie'][0]['valor'] ) ? (float) $datos['serie'][0]['valor'] : 0.0;

				if ( paybridge_clp_tipo_cambio_es_plausible( $valor_api ) ) {
					set_transient( 'paybridge_clp_dolar_api', $valor_api, 6 * HOUR_IN_SECONDS );
					// Mantiene el valor manual sincronizado con el último valor de la API,
					// para que sirva de respaldo actualizado si la API deja de responder.
					update_option( 'paybridge_clp_tipo_cambio', (string) $valor_api );
				}
			}
		}

		if ( is_numeric( $valor_api ) && paybridge_clp_tipo_cambio_es_plausible( (float) $valor_api ) ) {
			return (float) $valor_api;
		}
	}

	$valor_manual = get_option( 'paybridge_clp_tipo_cambio', '' );

	return ( is_numeric( $valor_manual ) && $valor_manual > 0 ) ? (float) $valor_manual : 0.0;
}

/**
 * Comprueba que un tipo de cambio CLP/USD esté en un rango plausible.
 *
 * Protege contra respuestas corruptas o manipuladas de la API: un valor
 * fuera de rango (p. ej. 1 o 900000) distorsionaría todos los precios de
 * la tienda y además sobreescribiría el valor manual de respaldo.
 */
function paybridge_clp_tipo_cambio_es_plausible( float $valor ): bool {
	return $valor >= 100 && $valor <= 5000;
}

/**
 * Convierte un monto en CLP a USD.
 *
 * @param int|float|string $monto_clp Monto en pesos chilenos.
 * @return float|false Monto en USD, o false si no hay tipo de cambio válido.
 */
function paybridge_clp_convertir_a_usd( $monto_clp ) {
	$tipo_cambio = paybridge_clp_obtener_tipo_cambio();

	if ( $tipo_cambio <= 0 ) {
		return false;
	}

	return round( (float) $monto_clp / $tipo_cambio, 2 );
}

/**
 * Imprime una fila con el total referencial en USD en las tablas de totales
 * del carrito y del checkout, mientras la moneda activa siga siendo CLP.
 */
function paybridge_clp_mostrar_total_en_dolares(): void {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	// Si la moneda ya se cambió a USD, el total de la tabla ya está en dólares.
	if ( 'CLP' !== get_woocommerce_currency() ) {
		return;
	}

	if ( ! WC()->cart ) {
		return;
	}

	$total_usd = paybridge_clp_convertir_a_usd( WC()->cart->get_total( 'edit' ) );

	if ( false === $total_usd ) {
		return;
	}
	?>
	<tr class="total-usd-reference">
		<th><?php esc_html_e( 'Total referencial (USD)', 'paybridge-clp' ); ?></th>
		<td data-title="<?php esc_attr_e( 'Total USD', 'paybridge-clp' ); ?>">
			<strong>$<?php echo esc_html( number_format( $total_usd, 2, '.', ',' ) ); ?> USD</strong>
		</td>
	</tr>
	<?php
}
