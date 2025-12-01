<?php

declare(strict_types=1);

use Setono\CronExpressionBundle\Form\TypeGuesser\CronExpressionTypeGuesser;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('setono_cron_expression.form.type_guesser.cron_expression', CronExpressionTypeGuesser::class)
        ->args([
            Configurator\service('property_info'),
    ])
        ->tag('form.type_guesser');
};
