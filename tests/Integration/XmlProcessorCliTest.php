<?php

declare(strict_types=1);

namespace Xml\Processor\Tests\Integration;

use Monolog\Handler\StreamHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Xml\Processor\Application;
use Xml\Processor\ConfigBuilder;

class XmlProcessorCliTest extends TestCase
{
    use LockableTrait;

    private Logger $logger;

    protected function setUp(): void
    {
        $this->logger = new Logger('Test app');
        $this->logger->pushHandler(
            new TestHandler()
        );

        parent::setUp();
    }

    public function testVersion(): void
    {
        $command = ['--version'];
        $actualValue = $this->execute($command);

        self::assertEquals('Test app %s', $actualValue);
    }

    public function testLock()
    {
        $this->lock('xml-processor');
        $command = ['--version'];
        $actualValue = $this->execute($command);
        self::assertStringContainsString('The command is already running in another process.', $actualValue);

        // Use --no-lock to override the lock.
        $command[] = '--no-lock';
        $actualValue = $this->execute($command);
        self::assertStringContainsString('Test app', $actualValue);

        // Unlock to ensure tests are able to continue.
        $this->release();
    }

    private function execute(array $command): string
    {
        $root = dirname(__DIR__) . '/File/config.yml';
        putenv('XML_PROCESSOR_API_CREDENTIALS=' . dirname(__DIR__) . '/File/creds.json');
        $config = (new ConfigBuilder())->build($root);

        $input = new ArgvInput($command);
        $output = new BufferedOutput();
        $app = new Application('Test app', $config, $this->logger);

        $app->run($input, $output);
        putenv('XML_PROCESSOR_API_CREDENTIALS');
        return $output->fetch();
    }
}
