<?php

declare(strict_types=1);

namespace Xml\Processor\Service;

use Google\Exception;
use Xml\Processor\Client\Sheets;
use Xml\Processor\Entity\RangeRequest;
use Xml\Processor\Exception\FailedToWriteFileException;

class GoogleSpreadsheetWriter implements SpreadsheetWriter
{
    private Sheets $service;

    public function __construct(Sheets $client)
    {
        $this->service = $client;
    }

    public function write(string $spreadsheetId, string $sheetName, RangeRequest $request): void
    {
        $this->service
            ->spreadsheet($spreadsheetId)
            ->sheet($sheetName)
            ->range($request->range());

        try {
            $this->service->clear();
            $this->service->update(
                array_merge([$request->header()], $request->items()->toArray())
            );
        } catch (Exception $e) {
            throw FailedToWriteFileException::unableToWriteToSheet($spreadsheetId, $e);
        }
    }
}
