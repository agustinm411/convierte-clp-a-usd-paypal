# PayBridge CLP

Plugin de WordPress/WooCommerce que convierte automáticamente la moneda del pedido de **CLP a USD** cuando el cliente elige **PayPal** como método de pago. Útil para tiendas chilenas, ya que PayPal no procesa pagos en pesos chilenos.

## Cómo funciona

1. El cliente llega al checkout con la tienda en CLP.
2. Si selecciona PayPal como método de pago, el plugin cambia la moneda de la sesión a USD y **convierte los precios de productos, variaciones, envío y cupones de monto fijo** usando el tipo de cambio configurado (los cupones porcentuales no necesitan conversión).
3. Si el cliente vuelve a elegir otro método de pago, la moneda se restaura a CLP.
4. Mientras la tienda está en CLP, se muestra una fila con el **total referencial en USD** en el carrito y el checkout.

Funciona tanto con el checkout clásico como con el checkout por bloques, y es compatible con HPOS (High-Performance Order Storage).

## Requisitos

- WordPress 6.0 o superior
- WooCommerce activo
- PHP 7.4 o superior
- Moneda de la tienda configurada en CLP
- WooCommerce PayPal Payments (`ppcp-gateway`) o PayPal Standard (`paypal`)

## Instalación

1. Descarga el repositorio y súbelo a `wp-content/plugins/` (o instala el ZIP desde el panel de WordPress).
2. Activa el plugin **PayBridge CLP**.
3. Ve a **WooCommerce → Ajustes → PayBridge CLP** y configura:
   - **Valor del dólar en pesos chilenos**: cuántos CLP equivalen a 1 USD (por ejemplo `900`).
   - **Obtener el tipo de cambio automáticamente**: si lo marcas, el valor del dólar se obtiene desde [mindicador.cl](https://mindicador.cl) y se actualiza cada 6 horas; el valor manual queda como respaldo si la API no responde.

## Limitaciones conocidas

- El plugin asume que la moneda base de la tienda es CLP.

## Aviso sobre uso de IA

Este repositorio puede incluir código generado o asistido por inteligencia artificial (IA) con el objetivo de optimizar el desarrollo, mejorar el rendimiento y agilizar la implementación de funcionalidades.

## Licencia

GPL-2.0-or-later. Ver [LICENSE](LICENSE).
