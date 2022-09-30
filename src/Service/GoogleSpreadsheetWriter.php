<?php

declare(strict_types=1);

namespace Xml\Processor\Service;

use Google\Exception;
use Google_Service_Sheets;
use Google_Service_Sheets_ClearValuesRequest;
use Google_Service_Sheets_ValueRange;
use Xml\Processor\Entity\RangeRequest;
use Xml\Processor\Exception\FailedToWriteFileException;

class GoogleSpreadsheetWriter implements SpreadsheetWriter
{
    private Google_Service_Sheets $service;

    public function __construct(Google_Service_Sheets $client)
    {
        $this->service = $client;
    }

    public function write(string $spreadsheetId, RangeRequest $request): void
    {
        try {
            $updateBody = new Google_Service_Sheets_ValueRange([
                'range' => $request->range(),
                'majorDimension' => 'ROWS',
                'values' => array_merge([$request->header()], $request->items()->toArray()),
            ]);

            $this->service->spreadsheets_values->update(
                $spreadsheetId,
                $request->range(),
                $updateBody,
                ['valueInputOption' => 'RAW']
            );
        } catch (Exception $e) {
            throw FailedToWriteFileException::unableToWriteToFile($spreadsheetId, $e);
        }
    }

    public function clear(string $spreadsheetId, RangeRequest $request): void
    {
        try {
            $requestBody = new Google_Service_Sheets_ClearValuesRequest();

            $this->service->spreadsheets_values->clear(
                $spreadsheetId,
                'A1:ZZZ10000',
                $requestBody
            );
        } catch (Exception $e) {
            throw FailedToWriteFileException::unableToClearFile($spreadsheetId, $e);
        }
    }
}
