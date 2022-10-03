<?php

declare(strict_types=1);

namespace Xml\Processor\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Webmozart\Assert\InvalidArgumentException;
use Xml\Processor\Entity\Coffee;
use Xml\Processor\Entity\FileData;
use Xml\Processor\Exception\FailedToProcessFileException;
use Xml\Processor\Service\DataExtractor;

class DataExtractorTest extends TestCase
{
    public function testThrowsExceptionOnUnprocessableLocation(): void
    {
        self::expectException(FailedToProcessFileException::class);
        self::expectExceptionMessage('File location "some_location" is invalid.');
        (new DataExtractor())->extract('some_location');
    }

    public function testThrowsExceptionOnNonExistingItems(): void
    {
        self::expectException(FailedToProcessFileException::class);
        self::expectExceptionMessage('The file is empty.');
        (new DataExtractor())->extract(__DIR__ . '/../../File/test_empty.xml');
    }

    public function testThrowsExceptionOnInvalidItemNode(): void
    {
        self::expectException(FailedToProcessFileException::class);
        self::expectExceptionMessage('XML item is missing a node: "sku".');
        (new DataExtractor())->extract(__DIR__ . '/../../File/test_invalid_item.xml');
    }

    public function testExtractsData(): void
    {
        $extractor = (new DataExtractor())->extract(__DIR__ . '/../../File/test_collection.xml');

        self::assertInstanceOf(FileData::class, $extractor);
        self::assertSame(3, $extractor->items()->count());
        self::assertSame('1', $extractor->items()->first()->id());
        self::assertSame('3', $extractor->items()->last()->id());
    }
}
