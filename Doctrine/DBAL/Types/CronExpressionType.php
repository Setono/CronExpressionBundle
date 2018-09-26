<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Doctrine\DBAL\Types;

use Cron\CronExpression;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class CronExpressionType extends Type
{
    const CRON_EXPRESSION_TYPE = 'cron_expression';

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLength(AbstractPlatform $platform)
    {
        return $platform->getVarcharDefaultLength();
    }

    /**
     * @param string           $value
     * @param AbstractPlatform $platform
     *
     * @return CronExpression
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return CronExpression::factory($value);
    }

    /**
     * @param CronExpression   $value
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (string) $value;
    }

    public function getName()
    {
        return self::CRON_EXPRESSION_TYPE; // modify to match your constant name
    }
}
