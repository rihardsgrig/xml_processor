<?php

declare(strict_types=1);

namespace Xml\Processor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Xml\Processor\ConfigBuilder;

class ConfigBuilderTest extends TestCase
{
    public function testReturnsNullOnMissingConfig(): void
    {
        $config = (new ConfigBuilder())->build('some');
        self::assertNull($config->get('xml_processor.google_api_creds'));
        self::assertNull($config->get('xml_processor.log_location'));
    }

    public function testSetsFromProjectConfig(): void
    {
        $config = (new ConfigBuilder())->build(__DIR__ . '/../File');
        self::assertSame('/creads.json', $config->get('xml_processor.google_api_creds'));
        self::assertSame('app.log', $config->get('xml_processor.log_location'));
    }

    public function testSetsFromEnvVars(): void
    {
        putenv('XML_PROCESSOR_API_CREDENTIALS=/creds_env.json');
        putenv('XML_PROCESSOR_LOG_LOCATION=/app_env.log');

        $config = (new ConfigBuilder())->build(__DIR__);
        self::assertSame('/creds_env.json', $config->get('xml_processor.google_api_creds'));
        self::assertSame('/app_env.log', $config->get('xml_processor.log_location'));

        putenv('XML_PROCESSOR_API_CREDENTIALS');
        putenv('XML_PROCESSOR_LOG_LOCATION');
    }

    public function testEnvVarsOverride(): void
    {
        putenv('XML_PROCESSOR_API_CREDENTIALS=/creds_env.json');
        putenv('XML_PROCESSOR_LOG_LOCATION=/app_env.log');

        $config = (new ConfigBuilder())->build(__DIR__ . '/../File'); // existing file

        self::assertSame('/creds_env.json', $config->get('xml_processor.google_api_creds'));
        self::assertSame('/app_env.log', $config->get('xml_processor.log_location'));

        putenv('XML_PROCESSOR_API_CREDENTIALS');
        putenv('XML_PROCESSOR_LOG_LOCATION');
    }
}
