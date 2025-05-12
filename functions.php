<?php

function obtener_metodo_pago_seleccionado() {
    if (!WC()->session) {
        return "Error: La sesión de WooCommerce no está disponible.";
    }

    $metodo_pago = WC()->session->get('chosen_payment_method');
    if (!$metodo_pago) {
        return "No se ha seleccionado ningún método de pago.";
    }

    return $metodo_pago;
}

function obtener_moneda_woocommerce() {
    return get_option('woocommerce_currency', 'CLP');
}

function cambiar_moneda_woocommerce($nueva_moneda) {
    if (!session_id()) {
        session_start();
    }

    $nueva_moneda = strtoupper(sanitize_text_field($nueva_moneda));
    $monedas_permitidas = ['USD', 'CLP'];

    if (!in_array($nueva_moneda, $monedas_permitidas, true)) {
        return "Error: Moneda no permitida.";
    }

    $_SESSION['moneda_temporal'] = $nueva_moneda;
    return "La moneda de la tienda ha sido cambiada a: " . esc_html($nueva_moneda) . " para esta sesión.";
}

add_filter('woocommerce_currency', function($currency) {
    if (!session_id()) {
        session_start();
    }
    return isset($_SESSION['moneda_temporal']) ? $_SESSION['moneda_temporal'] : $currency;
});

function agregar_script_cambio_moneda() {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.body.addEventListener("change", function (event) {
                let radioPayPal = document.querySelector("#radio-control-wc-payment-method-options-ppcp-gateway");
                let metodosPago = document.querySelectorAll("input[name='payment_method']");

                if (event.target === radioPayPal && radioPayPal.checked) {
                    cambiarMoneda();
                } else {
                    let otroMetodoSeleccionado = false;
                    metodosPago.forEach(metodo => {
                        if (metodo !== radioPayPal && metodo.checked) {
                            otroMetodoSeleccionado = true;
                        }
                    });

                    if (otroMetodoSeleccionado) {
                        eliminarSesionMoneda();
                    }
                }
            });
        });

        function cambiarMoneda() {
            let datos = new FormData();
            datos.append('action', 'cambiar_moneda');

            fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                method: "POST",
                body: datos
            })
            .then(response => response.text())
            .then(data => {
                console.log("Respuesta del servidor:", data);
                actualizarPrecios();
            })
            .catch(error => console.error("Error al cambiar la moneda:", error));
        }

        function eliminarSesionMoneda() {
            let datos = new FormData();
            datos.append('action', 'eliminar_sesion_moneda');

            fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                method: "POST",
                body: datos
            })
            .then(response => response.text())
            .then(data => {
                console.log("Sesión eliminada:", data);
                actualizarPrecios();
            })
            .catch(error => console.error("Error al eliminar la sesión de moneda:", error));
        }

        function actualizarPrecios() {
            jQuery(document.body).trigger('wc_fragment_refresh'); 
            jQuery(document.body).trigger('update_checkout');
        }
    </script>
    <?php
}
add_action('wp_footer', 'agregar_script_cambio_moneda');

function eliminar_sesion_moneda() {
    if (!session_id()) {
        session_start();
    }

    if (isset($_SESSION['moneda_temporal'])) {
        unset($_SESSION['moneda_temporal']);
        echo "Sesión de moneda eliminada.";
    } else {
        echo "No había sesión de moneda.";
    }

    wp_die();
}

add_action('wp_ajax_eliminar_sesion_moneda', 'eliminar_sesion_moneda');
add_action('wp_ajax_nopriv_eliminar_sesion_moneda', 'eliminar_sesion_moneda');

function manejar_cambio_moneda() {
    echo cambiar_moneda_woocommerce('USD');
    wp_die();
}
add_action('wp_ajax_cambiar_moneda', 'manejar_cambio_moneda');
add_action('wp_ajax_nopriv_cambiar_moneda', 'manejar_cambio_moneda');

add_action('woocommerce_thankyou', function() {
    if (!session_id()) {
        session_start();
    }

    if (isset($_SESSION['moneda_temporal'])) {
        unset($_SESSION['moneda_temporal']);
    }
});
