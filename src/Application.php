<?php

declare(strict_types=1);

namespace Xml\Processor;

use Consolidation\Config\Config;
use Google_Client;
use Google_Service_Sheets;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xml\Processor\Command\ProcessFileCommand;
use Xml\Processor\Service\DataExtractor;
use Xml\Processor\Service\GoogleSpreadsheetWriter;

final class Application
{
    use LockableTrait;

    private const APP_NAME = 'XML Processor';
    private SymfonyApplication $app;

    public function __construct(Config $config)
    {
        $this->app = new SymfonyApplication(
            self::APP_NAME,
            '@package_version@ - @datetime@',
        );

        $logger = new Logger(self::APP_NAME);
        $logger->pushHandler(
            new StreamHandler($config->get('xml_processor.log_location'))
        );

        $client = new Google_Client();
        $client->setAccessType('offline');
        $client->setAuthConfig($config->get('xml_processor.google_api_creds'));
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $service = new Google_Service_Sheets($client);

        $spreadsheetWriter = new GoogleSpreadsheetWriter(
            $service,
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
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        // Obtain a lock and exit if the command is already running.
        if (!$input->hasParameterOption('--no-lock') && !$this->lock('xml-processor')) {
            $output->writeln('The command is already running in another process.');

            return Command::FAILURE;
        }

        $statusCode = $this->app->run($input, $output);

        // Release the lock after successful command invocation.
        $this->release();

        return $statusCode;
    }
}
