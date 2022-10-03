<?php

declare(strict_types=1);

namespace Xml\Processor\Tests\Unit\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Google\Exception;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Xml\Processor\Client\Sheets;
use Xml\Processor\Entity\Coffee;
use Xml\Processor\Entity\RangeRequest;
use Xml\Processor\Exception\FailedToWriteFileException;
use Xml\Processor\Service\GoogleSpreadsheetWriter;

class GoogleSpreadsheetWriterTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->addToAssertionCount(
            Mockery::getContainer()->mockery_getExpectationCount(),
        );

        Mockery::close();
    }

    public function testThrowsExceptionOnGoogleClientError(): void
    {
        $item = Coffee::create(new SimpleXMLElement(file_get_contents(__DIR__ . '/../../File/test_internal.xml')));
        $collection = new ArrayCollection();
        $collection->add($item);
        $request = RangeRequest::create($collection);
        $range = $request->range();

        $sheet = Mockery::mock(Sheets::class, function (MockInterface $mock) use ($range): void {
            $mock->shouldReceive('spreadsheet')
                ->once()
                ->with('spreadsheet_id')
                ->andReturnSelf();
            $mock->shouldReceive('sheet')
                ->once()
                ->with('sheet_name')
                ->andReturnSelf();
            $mock->shouldReceive('range')
                ->once()
                ->with($range)
                ->andReturnSelf();
            $mock->shouldReceive('clear')
                ->once()
                ->andThrow(new Exception());
        });

        $writer = new GoogleSpreadsheetWriter($sheet);

        self::expectException(FailedToWriteFileException::class);
        self::expectExceptionMessage('Failed to write data to spreadsheet with id: "spreadsheet_id');
        $writer->write('spreadsheet_id', 'sheet_name', $request);
    }

    public function testWritesToClient(): void
    {
        $item = Coffee::create(new SimpleXMLElement(file_get_contents(__DIR__ . '/../../File/test_internal.xml')));
        $collection = new ArrayCollection();
        $collection->add($item);
        $request = RangeRequest::create($collection);

        $sheet = Mockery::mock(Sheets::class, function (MockInterface $mock) use ($request): void {
            $mock->shouldReceive('spreadsheet')
                ->once()
                ->with('spreadsheet_id')
                ->andReturnSelf();
            $mock->shouldReceive('sheet')
                ->once()
                ->with('sheet_name')
                ->andReturnSelf();
            $mock->shouldReceive('range')
                ->once()
                ->with($request->range())
                ->andReturnSelf();
            $mock->shouldReceive('clear')
                ->once()
                ->andReturns();
            $mock->shouldReceive('update')
                ->once()
                ->with(array_merge([$request->header()], $request->items()->toArray()))
                ->andReturns();
        });

        $writer = new GoogleSpreadsheetWriter($sheet);
        $writer->write('spreadsheet_id', 'sheet_name', $request);
    }
}
