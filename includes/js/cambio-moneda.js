(function () {
	'use strict';

	// IDs de gateway de PayPal: WooCommerce PayPal Payments y PayPal Standard (legado).
	var METODOS_PAYPAL = ['ppcp-gateway', 'paypal'];

	function esRadioDePago(el) {
		return el instanceof HTMLInputElement
			&& el.type === 'radio'
			&& (el.name === 'payment_method' || el.name.indexOf('radio-control-wc-payment-method') === 0);
	}

	// Delegación sobre document: funciona con el checkout clásico y el de bloques,
	// aunque el formulario de pago se vuelva a renderizar.
	document.addEventListener('change', function (event) {
		var radio = event.target;

		if (!esRadioDePago(radio) || !radio.checked) {
			return;
		}

		if (METODOS_PAYPAL.indexOf(radio.value) !== -1) {
			cambiarMoneda();
		} else {
			eliminarSesionMoneda();
		}
	});

	function peticionAjax(accion) {
		var datos = new FormData();
		datos.append('action', accion);
		datos.append('_ajax_nonce', cambioMonedaData.nonce);

		return fetch(cambioMonedaData.ajaxurl, {
			method: 'POST',
			body: datos
		}).then(function (respuesta) {
			return respuesta.json();
		}).then(function (json) {
			if (!json.success) {
				throw new Error(json.data && json.data.message ? json.data.message : 'Error desconocido');
			}
			return json.data;
		});
	}

	function cambiarMoneda() {
		peticionAjax('cambiar_moneda')
			.then(actualizarPrecios)
			.catch(function (error) {
				console.error('PayBridge CLP: error al cambiar la moneda:', error);
			});
	}

	function eliminarSesionMoneda() {
		peticionAjax('eliminar_sesion_moneda')
			.then(actualizarPrecios)
			.catch(function (error) {
				console.error('PayBridge CLP: error al restaurar la moneda:', error);
			});
	}

	function actualizarPrecios() {
		// Checkout clásico y mini-carrito.
		if (window.jQuery) {
			window.jQuery(document.body).trigger('wc_fragment_refresh');
			window.jQuery(document.body).trigger('update_checkout');
		}

		// Checkout por bloques: fuerza a la tienda de datos a recargar el carrito.
		if (window.wp && window.wp.data && typeof window.wp.data.dispatch === 'function') {
			var cartStore = window.wp.data.dispatch('wc/store/cart');
			if (cartStore && typeof cartStore.invalidateResolutionForStore === 'function') {
				cartStore.invalidateResolutionForStore();
			}
		}
	}
})();
