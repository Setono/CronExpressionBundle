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

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidType|ValueNotConvertible
     */
    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?CronExpression
    {
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            /**
             * @psalm-suppress UndefinedMethod
             */
            throw class_exists(InvalidType::class) ? InvalidType::new($value, CronExpression::class, ['string']) : ConversionException::conversionFailedInvalidType($value, CronExpression::class, ['string']);
        }

        if ('' === $value) {
            return null;
        }

        try {
            return CronExpression::factory($value);
        } catch (\Throwable $e) {
            /**
             * @psalm-suppress UndefinedMethod
             */
            throw class_exists(ValueNotConvertible::class) ? ValueNotConvertible::new($value, CronExpression::class, null, $e) : ConversionException::conversionFailed($value, CronExpression::class, $e);
        }
    }

    /**
     * @param mixed $value
     */
    #[\Override]
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
