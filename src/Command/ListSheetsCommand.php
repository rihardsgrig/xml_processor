<?php

declare(strict_types=1);

namespace Xml\Processor\Command;

use Google\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xml\Processor\Client\Sheets;

class ListSheetsCommand extends Command
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
            ->setName('xml:list-sheets')
            ->addArgument(
                'spreadsheet-id',
                InputArgument::REQUIRED,
                'Id of the spreadsheet where to write.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var string $spreadsheetId
         */
        $spreadsheetId = $input->getArgument('spreadsheet-id');

        try {
            $list = $this->client->spreadsheet($spreadsheetId)->sheetList();

            $list =  array_map(function ($v, $k) {
                return [$k, $v];
            }, $list, array_keys($list));

            $table = new Table($output);
            $table
                ->setHeaders(['Id', 'Name'])
                ->setRows($list);
            $table->render();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
