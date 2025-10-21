<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @api
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class CronExpression extends Constraint
{
    public string $message = '{{ value }} is not a valid cron expression.';

    /**
     * @param string[]|null $groups
     */
    public function __construct(
        string $message = null,
        ?array $groups = null,
        mixed $payload = null,
        array $options = [],
    ) {
        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
