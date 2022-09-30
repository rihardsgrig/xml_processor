<?php

declare(strict_types=1);

namespace Xml\Processor;

use Consolidation\Config\Config;
use Google_Client;
use Google_Service_Sheets;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Xml\Processor\Command\ProcessFileCommand;
use Xml\Processor\Service\DataExtractor;
use Xml\Processor\Service\GoogleSpreadsheetWriter;

final class Application
{
    private const APP_NAME = 'XML Processor';
    private SymfonyApplication $app;
    private OutputInterface $output;

    public function __construct(Config $config)
    {
        $this->app = new SymfonyApplication(
            self::APP_NAME,
            '@package_version@ - @datetime@',
        );
        $this->output = new ConsoleOutput();

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

        $this->app->add(
            new ProcessFileCommand(
                $logger,
                new DataExtractor(),
                $spreadsheetWriter
            ),
        );
    }

    public function run(): void
    {
        $this->app->run(null, $this->output);
    }
}
