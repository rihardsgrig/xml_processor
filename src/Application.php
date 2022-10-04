<?php

declare(strict_types=1);

namespace Xml\Processor;

use Consolidation\Config\Config;
use Google\Service\Sheets as GoogleSheets;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xml\Processor\Client\GoogleClient;
use Xml\Processor\Client\Sheets;
use Xml\Processor\Command\AddSheetCommand;
use Xml\Processor\Command\ListSheetsCommand;
use Xml\Processor\Command\ProcessFileCommand;
use Xml\Processor\Service\DataExtractor;
use Xml\Processor\Service\GoogleSpreadsheetWriter;

final class Application
{
    use LockableTrait;

    private SymfonyApplication $app;

    public function __construct(
        string $appName,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->app = new SymfonyApplication(
            $appName,
            '@package_version@ - @datetime@',
        );

        /**
         * @var string $creds
         */
        $creds = $config->get('xml_processor.google_api_creds');

        $sheets = new Sheets(
            new GoogleClient(
                [
                    'application_name' => $appName,
                    'google_api_creds' => $creds,
                    'scopes' => [GoogleSheets::SPREADSHEETS]
                ]
            )
        );

        $spreadsheetWriter = new GoogleSpreadsheetWriter(
            $sheets
        );

        $this->app->getDefinition()->addOptions([
            new InputOption(
                '--no-lock',
                null,
                InputOption::VALUE_NONE,
                'Run commands without locking. Allows multiple instances of commands to run concurrently.'
            )
        ]);

        $this->app->add(
            new ProcessFileCommand(
                $logger,
                new DataExtractor(),
                $spreadsheetWriter
            ),
        );

        $this->app->add(
            new ListSheetsCommand(
                $logger,
                $sheets
            ),
        );

        $this->app->add(
            new AddSheetCommand(
                $logger,
                $sheets
            ),
        );
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        // Obtain a lock and exit if the command is already running.
        if (!$input->hasParameterOption('--no-lock') && !$this->lock('xml-processor')) {
            $output->writeln('The command is already running in another process.');

            return Command::SUCCESS;
        }

        $statusCode = $this->app->run($input, $output);

        // Release the lock after successful command invocation.
        $this->release();

        return $statusCode;
    }
}
