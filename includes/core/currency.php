<?php
function obtener_moneda_woocommerce(): string {
    return get_option('woocommerce_currency', 'CLP');
}

function cambiar_moneda_woocommerce(string $nueva_moneda) {
check_ajax_referer('cambio_moneda_nonce');
$nueva = strtoupper(sanitize_text_field($nueva_moneda));
    $permitidas = ['USD', 'CLP'];

    if (!in_array($nueva, $permitidas, true)) {
        return "Error: Moneda no permitida.";
    }

    if (function_exists('WC') && WC()->session) {
        WC()->session->set('moneda_temporal', $nueva);
wp_send_json_success([
        'message' => 'Moneda cambiada',
         'currency' => $nueva_moneda,
    ]);
return true;
    }

    // Fallback extremo (no recomendado, pero evita roturas si no hay WC session)
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    $_SESSION['moneda_temporal'] = $nueva;
    return true;
}

function manejar_cambio_moneda() {
    echo cambiar_moneda_woocommerce('USD');
    wp_die();
}

function agregar_script_cambio_moneda(): void {
    $handle      = 'cambio-moneda';
    $script_rel  = '../js/cambio-moneda.js';
    $script_path = plugin_dir_path(__FILE__) . $script_rel;
    $script_url  = plugin_dir_url(__FILE__) . $script_rel;

    // Usa filemtime para cache-busting en desarrollo.
    $version = file_exists($script_path) ? (string) filemtime($script_path) : '1.0.0';

    wp_enqueue_script(
        $handle,
        $script_url,
        ['jquery'],   // jQuery es necesario porque tu JS dispara eventos jQuery.
        $version,
        true          // En el footer.
    );

    // Expone ajaxurl + nonce a tu JS como window.cambioMonedaData
    wp_localize_script($handle, 'cambioMonedaData', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('cambio_moneda_nonce'),
    ]);
}

function eliminar_sesion_moneda() {
    // Limpiar WC session
check_ajax_referer('cambio_moneda_nonce');
if (function_exists('WC') && WC()->session) {
        // Dos formas de limpiar, según la versión
        WC()->session->set('moneda_temporal', null);
        if (method_exists(WC()->session, '__unset')) {
            WC()->session->__unset('moneda_temporal');
        }
    }

    // Fallback: limpia $_SESSION si existiera
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    if (isset($_SESSION['moneda_temporal'])) {
        unset($_SESSION['moneda_temporal']);
    }

    wp_send_json_success(['message' => 'Sesión de moneda eliminada.']);
}