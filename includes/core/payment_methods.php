<?php
function obtener_metodo_pago_seleccionado(): string|false
{
    if (!WC()->session) {
        return false;
    }

    $metodo_pago = WC()->session->get('chosen_payment_method');

    if (!$metodo_pago || !is_string($metodo_pago)) {
        return false;
    }

    return $metodo_pago;
}
 
add_action('wp_footer', function () {
    if (is_checkout()) {
        $metodo = obtener_metodo_pago_seleccionado();
        var_dump($metodo);
    }
});
