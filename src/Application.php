<?php

namespace Xml\Processor;

use Monolog\Logger;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class Application
{
    private const APP_NAME = 'XML Processor';
    private SymfonyApplication $app;
    private OutputInterface $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput();

        // @todo console logger and file logger
//        $logger = new Logger(self::APP_NAME);
//        $logger->pushHandler(
//
//        );

        $this->app = new SymfonyApplication(
            self::APP_NAME,
            '@package_version@',
        );
    }

    public function run(): void
    {
        $this->app->run(null, $this->output);
    }
}
