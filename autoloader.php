<?php
/**
 * Autoloader customizado para o projeto.
 */
spl_autoload_register(function ($className) {
    // Padroniza o namespace para corresponder à estrutura de pastas
    $prefixMap = [
        'App\\' => __DIR__ . '/app/',
        'Core\\' => __DIR__ . '/app/core/' // Mapeia o namespace Core para a pasta correta
    ];

    foreach ($prefixMap as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $className, $len) === 0) {
            $relativeClass = substr($className, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});