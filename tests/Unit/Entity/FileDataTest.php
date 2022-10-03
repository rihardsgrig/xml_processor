<?php

declare(strict_types=1);

namespace Xml\Processor\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Webmozart\Assert\InvalidArgumentException;
use Xml\Processor\Entity\Coffee;
use Xml\Processor\Entity\FileData;

class FileDataTest extends TestCase
{
    public function testEmptyCollectionIsCreated(): void
    {
        $data = new FileData();

        self::assertTrue($data->items()->isEmpty());
    }

    public function testItemIsAddedToCollection(): void
    {
        $item = Coffee::create(new SimpleXMLElement(file_get_contents(__DIR__ . '/../../File/test_internal.xml')));
        $data = new FileData();
        $data->addItem($item);

        self::assertSame(1, $data->items()->count());
        self::assertTrue($data->items()->contains($item));
    }

    public function testItemIsAddedOnlyOnce(): void
    {
        $item = Coffee::create(new SimpleXMLElement(file_get_contents(__DIR__ . '/../../File/test_internal.xml')));
        $item2 = Coffee::create(new SimpleXMLElement(file_get_contents(__DIR__ . '/../../File/test_internal.xml')));
        $data = new FileData();
        $data->addItem($item);
        $data->addItem($item2);

        self::assertSame(1, $data->items()->count());
        self::assertTrue($data->items()->contains($item));
    }
}
