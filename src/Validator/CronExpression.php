<?php

namespace Setono\CronExpressionBundle\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;


/**
 * Metadata for the CronExpressionValidator.
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class CronExpression extends Constraint
{
    public $message = '{{ value }} is not a valid cron expression.';
}
