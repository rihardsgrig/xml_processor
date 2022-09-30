<?php

namespace Xml\Processor;

use Consolidation\Config\Config;
use Consolidation\Config\Loader\ConfigProcessor;
use Consolidation\Config\Loader\YamlConfigLoader;
use Webmozart\PathUtil\Path;

class ConfigBuilder
{
    public static function build(): Config
    {
        $config = new Config();
        $loader = new YamlConfigLoader();
        $processor = new ConfigProcessor();

        $globalConfig = join(\DIRECTORY_SEPARATOR, [Path::getHomeDirectory(), '.xml-processor', 'config.yml']);
        $projectConfig = join(\DIRECTORY_SEPARATOR, [dirname(__DIR__), 'config.yml']);

        $processor->extend($loader->load($globalConfig));
        $processor->extend($loader->load($projectConfig));

        $environment = [];
        if (is_string(getenv('XML_PROCESSOR_LOG_LOCATION'))) {
            $environment['xml_processor']['log_location'] = getenv('XML_PROCESSOR_LOG_LOCATION');
        }
        if (is_string(getenv('XML_PROCESSOR_LOG_LOCATION'))) {
            $environment['xml_processor']['google_api_creds'] = getenv('XML_PROCESSOR_LOG_LOCATION');
        }
        if (is_string(getenv('XML_PROCESSOR_SPREAD'))) {
            $environment['xml_processor']['spreadsheet_id'] = getenv('XML_PROCESSOR_LOG_LOCATION');
        }

        $processor->add($environment);
        $config->replace($processor->export());

        return $config;
    }
}
