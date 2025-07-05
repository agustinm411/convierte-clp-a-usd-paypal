<?php
declare(strict_types=1);

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

if (!defined('ABSPATH')) {
    exit;
}

// Incluir la librería del actualizador
require_once plugin_dir_path(__FILE__) . 'lib/plugin-update-checker/plugin-update-checker.php';

// Inicializar el actualizador
$updateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/agustinm411/convierte-clp-a-usd-paypal/',
    dirname(__DIR__) . '/clp-to-usd-paypal.php',
    'clp-to-usd-paypal'
);

$updateChecker->setBranch('main');
