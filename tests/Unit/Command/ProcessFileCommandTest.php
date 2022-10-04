<?php

declare(strict_types=1);

namespace Xml\Processor\Tests\Unit\Command;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Xml\Processor\Command\ProcessFileCommand;
use Xml\Processor\Entity\Coffee;
use Xml\Processor\Entity\FileData;
use Xml\Processor\Entity\RangeRequest;
use Xml\Processor\Exception\FailedToProcessFileException;
use Xml\Processor\Exception\FailedToWriteFileException;
use Xml\Processor\Service\DataExtractor;
use Xml\Processor\Service\SpreadsheetWriter;

class ProcessFileCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->addToAssertionCount(
            Mockery::getContainer()->mockery_getExpectationCount(),
        );

        Mockery::close();
    }

    public function testExecutesSuccessfully(): void
    {
        $item = Coffee::create(new SimpleXMLElement(file_get_contents(__DIR__ . '/../../File/test_internal.xml')));
        $data = new FileData();
        $data->addItem($item);

        $logger = Mockery::mock(LoggerInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('info')
                ->twice();
        });

        $dataExtractor = Mockery::mock(DataExtractor::class, function (MockInterface $mock) use ($data): void {
            $mock->shouldReceive('extract')
                ->once()
                ->with('some/file/location.xml')
                ->andReturn($data);
        });

        $spreadsheetWriter = Mockery::mock(SpreadsheetWriter::class, function (MockInterface $mock) use ($data): void {
            $mock->shouldReceive('write')
                ->once()
                ->withArgs(function (string $spreadsheetId, string $sheetName, RangeRequest $request) use ($data) {
                    self::assertSame('some_id', $spreadsheetId);
                    self::assertSame('some_name', $sheetName);
                    self::assertSame(
                        $data->items()->first()->id(),
                        $request->items()->first()[0]
                    );

                    return true;
                })
                ->andReturns();
        });

        $cmd = new ProcessFileCommand($logger, $dataExtractor, $spreadsheetWriter);

        $cmdTester = new CommandTester($cmd);
        $commandStatus = $cmdTester->execute([
            'file-location' => 'some/file/location.xml',
            'spreadsheet-id' => 'some_id',
            'sheet-name' => 'some_name',
        ]);
        self::assertEquals(Command::SUCCESS, $commandStatus);
    }

    public function testMissingFileLocationArgument(): void
    {
        $logger = Mockery::mock(LoggerInterface::class);
        $dataExtractor = Mockery::mock(DataExtractor::class);
        $spreadsheetWriter = Mockery::mock(SpreadsheetWriter::class);

        $cmd = new ProcessFileCommand($logger, $dataExtractor, $spreadsheetWriter);

        $cmdTester = new CommandTester($cmd);

        self::expectExceptionMessage('Not enough arguments (missing: "file-location").');
        $cmdTester->execute([
            'spreadsheet-id' => 'some_id',
            'sheet-name' => 'some_name',
        ]);
    }

    public function testMissingSpreadsheetIdArgument(): void
    {
        $logger = Mockery::mock(LoggerInterface::class);
        $dataExtractor = Mockery::mock(DataExtractor::class);
        $spreadsheetWriter = Mockery::mock(SpreadsheetWriter::class);

        $cmd = new ProcessFileCommand($logger, $dataExtractor, $spreadsheetWriter);

        $cmdTester = new CommandTester($cmd);

        self::expectExceptionMessage('Not enough arguments (missing: "spreadsheet-id").');
        $cmdTester->execute([
            'file-location' => 'some/file/location.xml',
            'sheet-name' => 'some_name',
        ]);
    }

    public function testMissingSheetNameArgument(): void
    {
        $logger = Mockery::mock(LoggerInterface::class);
        $dataExtractor = Mockery::mock(DataExtractor::class);
        $spreadsheetWriter = Mockery::mock(SpreadsheetWriter::class);

        $cmd = new ProcessFileCommand($logger, $dataExtractor, $spreadsheetWriter);

        $cmdTester = new CommandTester($cmd);

        self::expectExceptionMessage('Not enough arguments (missing: "sheet-name").');
        $cmdTester->execute([
            'file-location' => 'some/file/location.xml',
            'spreadsheet-id' => 'some_id',
        ]);
    }

    public function testHandlesFileProcessException(): void
    {
        $logger = Mockery::mock(LoggerInterface::class, function (MockInterface $mock): void {
            $mock->shouldReceive('error')
                ->once()
                ->with('The file is empty.');
        });

        $dataExtractor = Mockery::mock(DataExtractor::class, function (MockInterface $mock): void {
            $mock->shouldReceive('extract')
                ->once()
                ->with('some/file/location.xml')
                ->andThrow(FailedToProcessFileException::emptyFile());
        });

        $spreadsheetWriter = Mockery::mock(SpreadsheetWriter::class);

        $cmd = new ProcessFileCommand($logger, $dataExtractor, $spreadsheetWriter);

        $cmdTester = new CommandTester($cmd);
        $commandStatus = $cmdTester->execute([
            'file-location' => 'some/file/location.xml',
            'spreadsheet-id' => 'some_id',
            'sheet-name' => 'some_name',
        ]);
        self::assertEquals(Command::FAILURE, $commandStatus);
    }

    public function testHandlesFileWriteException(): void
    {
        $logger = Mockery::mock(LoggerInterface::class, function (MockInterface $mock): void {
            $mock->shouldReceive('info')
                ->once()
                ->with('Data extracted from XML file.');
            $mock->shouldReceive('error')
                ->once()
                ->with('Failed to write data to spreadsheet with id: "some_id".');
        });

        $item = Coffee::create(new SimpleXMLElement(file_get_contents(__DIR__ . '/../../File/test_internal.xml')));
        $data = new FileData();
        $data->addItem($item);
        $dataExtractor = Mockery::mock(DataExtractor::class, function (MockInterface $mock) use ($data): void {
            $mock->shouldReceive('extract')
                ->once()
                ->with('some/file/location.xml')
                ->andReturn($data);
        });

        $spreadsheetWriter = Mockery::mock(SpreadsheetWriter::class, function (MockInterface $mock): void {
            $mock->shouldReceive('write')
                ->once()
                ->andThrow(FailedToWriteFileException::unableToWriteToSheet('some_id'));
        });

        $cmd = new ProcessFileCommand($logger, $dataExtractor, $spreadsheetWriter);

        $cmdTester = new CommandTester($cmd);
        $commandStatus = $cmdTester->execute([
            'file-location' => 'some/file/location.xml',
            'spreadsheet-id' => 'some_id',
            'sheet-name' => 'some_name',
        ]);
        self::assertEquals(Command::FAILURE, $commandStatus);
    }
}
