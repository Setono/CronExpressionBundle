<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\CronExpressionBundle\Form\TypeGuesser\CronExpressionTypeGuesser;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('setono_cron_expression.form.type_guesser.cron_expression', CronExpressionTypeGuesser::class)
        ->args([
            service('property_info'),
    ])
        ->tag('form.type_guesser');
};
