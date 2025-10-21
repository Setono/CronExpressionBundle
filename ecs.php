<?php

declare(strict_types=1);

use SlevomatCodingStandard\Sniffs\Commenting\ForbiddenAnnotationsSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {
    $config->import('vendor/sylius-labs/coding-standard/ecs.php');
    $config->ruleWithConfiguration(ForbiddenAnnotationsSniff::class, ['forbiddenAnnotations' => ['@author', '@category', '@copyright', '@created', '@license', '@package', '@since', '@subpackage', '@version']]);
    $config->paths([
        'src', 'tests'
    ]);
};
