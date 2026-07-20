<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_settings_tabs_array', 'paybridge_clp_agregar_pestana_configuracion', 50 );
add_action( 'woocommerce_settings_tabs_paybridge_clp', 'paybridge_clp_mostrar_configuracion' );
add_action( 'woocommerce_update_options_paybridge_clp', 'paybridge_clp_guardar_configuracion' );
add_action( 'admin_init', 'paybridge_clp_migrar_opciones' );

function paybridge_clp_agregar_pestana_configuracion( array $settings_tabs ): array {
	$settings_tabs['paybridge_clp'] = __( 'PayBridge CLP', 'paybridge-clp' );
	return $settings_tabs;
}

function paybridge_clp_mostrar_configuracion(): void {
	woocommerce_admin_fields( paybridge_clp_obtener_campos_configuracion() );
}

function paybridge_clp_guardar_configuracion(): void {
	woocommerce_update_options( paybridge_clp_obtener_campos_configuracion() );
	// El tipo de cambio manual pudo cambiar; descarta el valor cacheado de la API.
	delete_transient( 'paybridge_clp_dolar_api' );
}

function paybridge_clp_obtener_campos_configuracion(): array {
	return array(
		'section_title'  => array(
			'name' => __( 'Ajustes de PayBridge CLP', 'paybridge-clp' ),
			'type' => 'title',
			'desc' => __( 'Configura las opciones de conversión de CLP a USD aquí.', 'paybridge-clp' ),
			'id'   => 'paybridge_clp_section_title',
		),
		'tipo_cambio'    => array(
			'name'              => __( 'Valor del dólar en pesos chilenos', 'paybridge-clp' ),
			'type'              => 'number',
			'desc'              => __( 'Ingrese cuántos pesos chilenos equivalen a 1 dólar (por ejemplo 900 si 1 USD = 900 CLP).', 'paybridge-clp' ),
			'id'                => 'paybridge_clp_tipo_cambio',
			'custom_attributes' => array(
				'min'  => '1',
				'step' => '0.01',
			),
		),
		'usar_api'       => array(
			'name' => __( 'Obtener el tipo de cambio automáticamente', 'paybridge-clp' ),
			'type' => 'checkbox',
			'desc' => __( 'Obtiene el valor del dólar desde mindicador.cl (se actualiza cada 6 horas) y sobreescribe el valor manual de arriba con cada actualización. Si la API no responde, se usa el valor manual.', 'paybridge-clp' ),
			'id'   => 'paybridge_clp_usar_api',
		),
		'section_end'    => array(
			'type' => 'sectionend',
			'id'   => 'paybridge_clp_section_end',
		),
	);
}

/**
 * Migra las opciones con nombres genéricos de versiones anteriores
 * (mi_plugin_campo_texto / mi_plugin_campo_checkbox) a las nuevas con prefijo.
 */
function paybridge_clp_migrar_opciones(): void {
	if ( get_option( 'paybridge_clp_version' ) === PAYBRIDGE_CLP_VERSION ) {
		return;
	}

	$tipo_cambio_antiguo = get_option( 'mi_plugin_campo_texto', null );
	if ( null !== $tipo_cambio_antiguo && null === get_option( 'paybridge_clp_tipo_cambio', null )
		&& is_numeric( $tipo_cambio_antiguo ) && (float) $tipo_cambio_antiguo > 0 ) {
		update_option( 'paybridge_clp_tipo_cambio', (string) (float) $tipo_cambio_antiguo );
	}

	$checkbox_antiguo = get_option( 'mi_plugin_campo_checkbox', null );
	if ( null !== $checkbox_antiguo && null === get_option( 'paybridge_clp_usar_api', null ) ) {
		update_option( 'paybridge_clp_usar_api', $checkbox_antiguo );
	}

	delete_option( 'mi_plugin_campo_texto' );
	delete_option( 'mi_plugin_campo_checkbox' );
	update_option( 'paybridge_clp_version', PAYBRIDGE_CLP_VERSION );
}
