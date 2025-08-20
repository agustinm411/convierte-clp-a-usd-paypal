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
datos.append('_ajax_nonce', cambioMonedaData.nonce);
fetch(cambioMonedaData.ajaxurl, {
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
datos.append('_ajax_nonce', cambioMonedaData.nonce);
    fetch(cambioMonedaData.ajaxurl, {
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
