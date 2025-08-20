<?php
add_filter('woocommerce_currency', function (string $currency): string {
    // Usar la sesión de WooCommerce
    if (function_exists('WC') && WC()->session) {
        $tmp = WC()->session->get('moneda_temporal');
        if (is_string($tmp) && in_array($tmp, ['USD', 'CLP'], true)) {
            return $tmp;
        }
    }
    return $currency;
});
