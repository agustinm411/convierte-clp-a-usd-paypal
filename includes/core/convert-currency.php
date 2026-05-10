<?php
function mostrar_total_en_dolares() {
    // 1. Verificación de seguridad para evitar errores en el admin
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;

    // 2. Obtener el total numérico sin formato (limpio)
    // Usamos get_total() del objeto global de WooCommerce
    $total_clp = WC()->cart->get_total( 'edit' ); 

    // 3. Obtener el tipo de cambio
    $precio_dolar = get_option('mi_plugin_campo_texto', '');

    // 4. Validar y Calcular
    if ( is_numeric($precio_dolar) && $precio_dolar > 0 ) {
        $total_usd = $total_clp / $precio_dolar;
        
        // 5. Imprimir la fila en la tabla de totales
        ?>
        <tr class="total-usd-reference">
            <th>Total Referencial (USD)</th>
            <td data-title="Total USD">
                <strong>$<?php echo number_format($total_usd, 2, '.', ','); ?> USD</strong>
            </td>
        </tr>
        <?php
    }
}

function calcular_conversion_dolares($total_pesos) {
    $precio_dolar = get_option('mi_plugin_campo_texto', '');
    
    // Validamos que el precio del dólar sea un número válido y mayor a 0
    if (empty($precio_dolar) || !is_numeric($precio_dolar) || $precio_dolar <= 0) {
        return false; 
    }
    
    return round($total_pesos / $precio_dolar, 2);
}
