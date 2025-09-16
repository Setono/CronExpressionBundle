<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;
use Symfony\Component\TypeInfo\Type;

$config = new Configuration();

$config
    ->ignoreErrorsOnPackage('doctrine/doctrine-bundle', [ErrorType::UNUSED_DEPENDENCY])
;

if (class_exists(Type::class)) {
    $config->ignoreErrorsOnPackage('symfony/type-info', [ErrorType::SHADOW_DEPENDENCY]);
} else {
    $config->ignoreUnknownClasses([Type::class]);
}

// ignore polyfill
if (version_compare(PHP_VERSION, '8.3.0', '>=')) {
    $config->ignoreErrorsOnPackage('symfony/polyfill-php83', [ErrorType::UNUSED_DEPENDENCY]);
}

return $config;
