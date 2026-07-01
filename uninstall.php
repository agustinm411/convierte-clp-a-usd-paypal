<?php
/**
 * Limpieza al desinstalar PayBridge CLP.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'paybridge_clp_tipo_cambio' );
delete_option( 'paybridge_clp_usar_api' );
delete_option( 'paybridge_clp_version' );

// Opciones de versiones anteriores a 1.1.0.
delete_option( 'mi_plugin_campo_texto' );
delete_option( 'mi_plugin_campo_checkbox' );

delete_transient( 'paybridge_clp_dolar_api' );
