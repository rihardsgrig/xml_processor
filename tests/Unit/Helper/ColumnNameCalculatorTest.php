<?php

declare(strict_types=1);

namespace Xml\Processor\Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Webmozart\Assert\InvalidArgumentException;
use Xml\Processor\Entity\Coffee;
use Xml\Processor\Helper\ColumnNameCalculator;

class ColumnNameCalculatorTest extends TestCase
{
    /**
     * @dataProvider values
     */
    public function testCreateItem(int $number, string $letter): void
    {
        self::assertSame($letter, ColumnNameCalculator::getColumnNameFromNumber($number));
    }

    public static function values(): array
    {
        return [
            [1, 'A'],
            [2, 'B'],
            [26, 'Z'],
            [51, 'AY'],
            [52, 'AZ'],
            [80, 'CB'],
            [676, 'YZ'],
            [702, 'ZZ'],
            [705, 'AAC'],
        ];
    }
}
