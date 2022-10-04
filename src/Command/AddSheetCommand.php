<?php

declare(strict_types=1);

namespace Xml\Processor\Command;

use Google\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xml\Processor\Client\Sheets;

class AddSheetCommand extends Command
{
    private LoggerInterface $logger;
    private Sheets $client;

    public function __construct(
        LoggerInterface $logger,
        Sheets $client
    ) {
        $this->logger = $logger;

        parent::__construct();
        $this->client = $client;
    }

    protected function configure(): void
    {
        $this
            ->setName('xml:add-sheet')
            ->addArgument(
                'spreadsheet-id',
                InputArgument::REQUIRED,
                'Id of the spreadsheet where to write.',
            )
            ->addArgument(
                'sheet-name',
                InputArgument::REQUIRED,
                'Name of the spreadsheet sheet.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var string $spreadsheetId
         */
        $spreadsheetId = $input->getArgument('spreadsheet-id');

        /**
         * @var string $sheetName
         */
        $sheetName = $input->getArgument('sheet-name');

        try {
            $sheet = $this->client->spreadsheet($spreadsheetId)->addSheet($sheetName);
            $output->writeln(sprintf('Sheet "%s" added to spreadsheet "%s"', $sheet, $spreadsheetId));
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
