<?php

declare(strict_types=1);

namespace Xml\Processor\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Webmozart\Assert\InvalidArgumentException;
use Xml\Processor\Entity\Coffee;

class CoffeeTest extends TestCase
{
    public function testCreateItem(): void
    {
        $item = Coffee::create(new SimpleXMLElement(file_get_contents(__DIR__ . '/../../File/test_internal.xml')));

        self::assertSame([
            'Id' => '1',
            'Category Name' => 'Coffee',
            'SKU' => '20',
            'Name' => 'French Coffee',
            'Description' => 'description',
            'Shortdesc' => 'short_description',
            'Price' => '41.6000',
            'Link' => 'http://www.test.com',
            'Image' => 'http://cdn.test.com/image.jpg',
            'Brand' => 'some_brand',
            'Rating' => '0',
            'Caffeine Type' => 'Caffeinated',
            'Count' => '1',
            'Flavored' => 'No',
            'Seasonal' => 'No',
            'In stock' => 'Yes',
            'Facebook' => '1',
            'IsKCup' => '0',
        ], $item->toArray());
    }

    public function testValidationFailsOnInvalidXml(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('XML item is missing a node: "sku".');
        Coffee::create(new SimpleXMLElement(file_get_contents(__DIR__ . '/../../File/test_invalid_item.xml')));
    }
}
