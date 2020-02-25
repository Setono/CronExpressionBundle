<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Doctrine\DBAL\Types;

use Cron\CronExpression;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Setono\CronExpressionBundle\Doctrine\DBAL\Types\CronExpressionType;

final class CronExpressionTypeTest extends TestCase
{
    /** @var Type */
    private $type;

    /** @var AbstractPlatform */
    private $platform;

    /**
     * @throws DBALException
     */
    protected function setUp(): void
    {
        if (!Type::hasType('cron_expression')) {
            Type::addType('cron_expression', CronExpressionType::class);
        }

        $this->type = Type::getType('cron_expression');
        $this->platform = $this->getPlatform();
    }

    /**
     * @test
     */
    public function convertToPhpReturnsCronExpression(): void
    {
        $val = $this->type->convertToPHPValue('@daily', $this->platform);

        $this->assertInstanceOf(CronExpression::class, $val);
    }

    /**
     * @test
     */
    public function convertToDatabaseReturnsString(): void
    {
        $val = $this->type->convertToDatabaseValue(CronExpression::factory('@daily'), $this->platform);

        $this->assertIsString('string', $val);
    }

    /**
     * @return MockObject|AbstractPlatform
     */
    private function getPlatform(): MockObject
    {
        return $this->getMockBuilder(AbstractPlatform::class)->disableOriginalConstructor()->getMock();
    }
}
