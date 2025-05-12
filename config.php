<?php

function agregar_configuracion_plugin_a_woocommerce($settings_tabs) {
    $settings_tabs['configuracion_mi_plugin'] = __('convertidor de peso chileno a dólar', 'text-domain');
    return $settings_tabs;
}
add_filter('woocommerce_settings_tabs_array', 'agregar_configuracion_plugin_a_woocommerce', 50);


function mostrar_configuracion_plugin_woocommerce() {
    woocommerce_admin_fields(obtener_configuracion_plugin());
}
add_action('woocommerce_settings_tabs_configuracion_mi_plugin', 'mostrar_configuracion_plugin_woocommerce');


function guardar_configuracion_plugin_woocommerce() {
    woocommerce_update_options(obtener_configuracion_plugin());
}
add_action('woocommerce_update_options_configuracion_mi_plugin', 'guardar_configuracion_plugin_woocommerce');


function obtener_configuracion_plugin() {
    $configuraciones = array(
        'section_title' => array(
            'name'     => __('Ajustes del convertidor', 'text-domain'),
            'type'     => 'title',
            'desc'     => ' Configura las opciones de conversión aquí.',
            'id'       => 'mi_plugin_section_title'
        ),
        'campo_texto' => array(
            'name' => __('Valor en pesos chilenos', 'text-domain'),
            'type' => 'text',
            'desc' => __('Ingrese el valor en pesos chilenos (por ejemplo 900 si 1 dólar = a 900 clp).', 'text-domain'),
            'id'   => 'mi_plugin_campo_texto'
        ),
        'campo_checkbox' => array(
            'name'    => __('Habilitar clave de api externa', 'text-domain'),
            'type'    => 'checkbox',
            'desc'    => __('Marcar para activar la función.', 'text-domain'),
            'id'      => 'mi_plugin_campo_checkbox'
        ),
        'section_end' => array(
            'type' => 'sectionend',
            'id'   => 'mi_plugin_section_end'
        )
    );
    return $configuraciones;
}
