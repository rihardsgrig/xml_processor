<?php

declare(strict_types=1);

namespace Xml\Processor\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Xml\Processor\Entity\Coffee;
use Xml\Processor\Entity\RangeRequest;
use Xml\Processor\Exception\FailedToProcessFileException;

class RangeRequestTest extends TestCase
{
    public function testCannotBeCreatedFromEmptyCollection(): void
    {
        self::expectException(FailedToProcessFileException::class);
        self::expectExceptionMessage('The file is empty.');
        RangeRequest::create(new ArrayCollection());
    }

    public function testItemIsAddedToCollection(): void
    {
        $item = Coffee::create(new SimpleXMLElement(file_get_contents(__DIR__ . '/../../File/test_internal.xml')));
        $collection = new ArrayCollection();
        $collection->add($item);

        $request = RangeRequest::create($collection);

        self::assertSame([array_values($item->toArray())], $request->items()->toArray());
        self::assertSame(array_keys($item->toArray()), $request->header());
        self::assertSame('A1:R2', $request->range());
    }
}
