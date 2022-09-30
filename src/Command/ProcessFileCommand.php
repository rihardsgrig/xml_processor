<?php

declare(strict_types=1);

namespace Xml\Processor\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xml\Processor\Entity\RangeRequest;
use Xml\Processor\Exception\FailedToProcessFileException;
use Xml\Processor\Exception\FailedToWriteFileException;
use Xml\Processor\Service\DataExtractor;
use Xml\Processor\Service\SpreadsheetWriter;

class ProcessFileCommand extends Command
{
    private LoggerInterface $logger;
    private DataExtractor $dataExtractor;
    private SpreadsheetWriter $spreadsheetWriter;

    public function __construct(
        LoggerInterface $logger,
        DataExtractor $dataExtractor,
        SpreadsheetWriter $spreadsheetWriter
    ) {
        $this->logger = $logger;
        $this->dataExtractor = $dataExtractor;
        $this->spreadsheetWriter = $spreadsheetWriter;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('xml:process-file')
            ->addArgument(
                'file-location',
                InputArgument::REQUIRED,
                'Local or remote xml file location.',
            )
            ->addArgument(
                'spreadsheet-id',
                InputArgument::REQUIRED,
                'Id of the spreadsheet where to write.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file-location');
        $spreadsheetId = $input->getArgument('spreadsheet-id');
        if (!is_string($file) || !is_string($spreadsheetId)) {
            $this->logger->error('Input parameter value is not a string.');
            return Command::FAILURE;
        }

        try {
            $fileData = $this->dataExtractor->extract($file);
            $output->write('Data extracted from XML file.', true);

            $this->spreadsheetWriter->clear(
                $spreadsheetId,
                RangeRequest::create($fileData->items())
            );
            $output->write(sprintf('Content removed from spreadsheet "%s".', $spreadsheetId), true);

            $this->spreadsheetWriter->write(
                $spreadsheetId,
                RangeRequest::create($fileData->items())
            );
            $output->write(sprintf('Data written to spreadsheet "%s".', $spreadsheetId), true);
        } catch (FailedToProcessFileException | FailedToWriteFileException $e) {
            $this->logger->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
