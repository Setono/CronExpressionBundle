<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Doctrine\DBAL\Types;

use Cron\CronExpression;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class CronExpressionType extends Type
{
    public const CRON_EXPRESSION_TYPE = 'cron_expression';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    /**
     * @param mixed $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): CronExpression
    {
        return CronExpression::factory($value);
    }

    /**
     * @param mixed $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return (string) $value;
    }

    public function getName(): string
    {
        return self::CRON_EXPRESSION_TYPE;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
