<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
    ->ignoreErrorsOnPackage('symfony/type-info', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackage('doctrine/doctrine-bundle', [ErrorType::UNUSED_DEPENDENCY])
    ->ignoreErrorsOnPackage('symfony/polyfill-php83', [ErrorType::UNUSED_DEPENDENCY])
;
