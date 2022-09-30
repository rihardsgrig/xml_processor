<?php

declare(strict_types=1);

namespace Xml\Processor\Service;

use Webmozart\Assert\InvalidArgumentException;
use Xml\Processor\Entity\Coffee;
use Xml\Processor\Entity\FileData;
use Xml\Processor\Exception\FailedToProcessFileException;
use XMLReader;

class DataExtractor
{
    public function extract(string $location): FileData
    {
        $xml = XMLReader::open($location);
        if (false === $xml) {
            throw FailedToProcessFileException::unprocessableLocation($location);
        }

        while ($xml->name !== 'item' && $xml->nodeType !== XMLReader::END_ELEMENT) {
            $xml->read();
        }

        $data = new FileData();

        while ($xml->name === 'item') {
            $object = simplexml_load_string($xml->readOuterXml());

            if (false === $object) {
                throw FailedToProcessFileException::unprocessableLocation($location);
            }

            try {
                $data->addItem(Coffee::create($object));
            } catch (InvalidArgumentException $e) {
                throw FailedToProcessFileException::invalidFileContents($e);
            }

            $xml->next('item');
        };

        if ($data->items()->isEmpty()) {
            throw FailedToProcessFileException::emptyFile();
        }

        return $data;
    }
}
