<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Doctrine\DBAL\Types;

use Cron\CronExpression;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;

final class CronExpressionType extends Type
{
    public const CRON_EXPRESSION_TYPE = 'cron_expression';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    /**
     * @param mixed $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?CronExpression
    {
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            if (class_exists(InvalidType::class)) {
                throw InvalidType::new($value, CronExpression::class, ['string']);
            } else if (method_exists(ConversionException::class, "conversionFailedInvalidType")) {
                throw ConversionException::conversionFailedInvalidType($value, CronExpression::class, ['string']);
            }
            return null;
        }

        if ('' === $value) {
            return null;
        }

        try {
            return CronExpression::factory($value);
        } catch (\Throwable $e) {
            if (class_exists(ValueNotConvertible::class)) {
                throw ValueNotConvertible::new($value, CronExpression::class, null, $e);
            } else if (method_exists(ConversionException::class, "conversionFailed")) {
                throw ConversionException::conversionFailed($value, CronExpression::class, $e);
            }
            return null;
        }
    }

    /**
     * @param mixed $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

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
