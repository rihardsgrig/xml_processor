<?php

namespace Xml\Processor;

use Consolidation\Config\Config;
use Consolidation\Config\Loader\ConfigProcessor;
use Consolidation\Config\Loader\YamlConfigLoader;
use Webmozart\PathUtil\Path;

class ConfigBuilder
{
    public static function build(string $projectRoot): Config
    {
        $config = new Config();
        $loader = new YamlConfigLoader();
        $processor = new ConfigProcessor();

        $globalConfig = join(DIRECTORY_SEPARATOR, [Path::getHomeDirectory(), '.xml-processor', 'config.yml']);
        $projectConfig = join(DIRECTORY_SEPARATOR, [$projectRoot, 'config.yml']);

        $processor->extend($loader->load($globalConfig));
        $processor->extend($loader->load($projectConfig));

        $environment = [];
        if (is_string(getenv('XML_PROCESSOR_LOG_LOCATION'))) {
            $environment['xml_processor']['log_location'] = getenv('XML_PROCESSOR_LOG_LOCATION');
        }
        if (is_string(getenv('XML_PROCESSOR_API_CREDENTIALS'))) {
            $environment['xml_processor']['google_api_creds'] = getenv('XML_PROCESSOR_API_CREDENTIALS');
        }

        $processor->add($environment);
        $config->replace($processor->export());

        return $config;
    }
}
