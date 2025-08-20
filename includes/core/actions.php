<?php
add_action('wp_ajax_eliminar_sesion_moneda', 'eliminar_sesion_moneda');
add_action('wp_ajax_nopriv_eliminar_sesion_moneda', 'eliminar_sesion_moneda');
add_action('wp_ajax_cambiar_moneda', 'manejar_cambio_moneda');
add_action('wp_ajax_nopriv_cambiar_moneda', 'manejar_cambio_moneda');
add_action('wp_enqueue_scripts', 'agregar_script_cambio_moneda');
add_action('woocommerce_cart_calculate_fees', 'mostrar_total_en_dolares');
add_action('woocommerce_thankyou', function() {
    if (!session_id()) {
        session_start();
    }

    if (isset($_SESSION['moneda_temporal'])) {
        unset($_SESSION['moneda_temporal']);
    }
});
