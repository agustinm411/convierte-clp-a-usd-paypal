<?php
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

function convertir_a_dolares($total_pesos) {
    $precio_dolar = get_option('mi_plugin_campo_texto', '');
    if ($total_pesos < 0 || $precio_dolar <= 0) {
        return "Error: Los valores deben ser positivos y el precio del dólar debe ser mayor a 0.";
    }
    return round($total_pesos / $precio_dolar, 2);
}
