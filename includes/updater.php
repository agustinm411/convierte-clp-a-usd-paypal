<?php
declare(strict_types=1);

use Puc_v4_Factory;

if (!defined('ABSPATH')) {
    exit;
}

// Incluir la librería del actualizador
require_once plugin_dir_path(__FILE__) . 'lib/plugin-update-checker/plugin-update-checker.php';

// Inicializar el actualizador
$updateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/TU-USUARIO/clp-to-usd-paypal/',
    dirname(__DIR__) . '/clp-to-usd-paypal.php',
    'clp-to-usd-paypal'
);

$updateChecker->setBranch('main');
