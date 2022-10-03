<?php

namespace Xml\Processor\Client;

use Google\Service\Sheets as GoogleSheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\BatchUpdateValuesRequest;
use Google\Service\Sheets\ClearValuesRequest;
use Google\Service\Sheets\Resource\SpreadsheetsValues;
use Google\Service\Sheets\ValueRange;

class Sheets
{
    private GoogleSheets $service;
    private string $spreadsheetId = '';
    private string $sheet = '';
    private string $range = '';

    public function __construct(GoogleClient $client)
    {
        $this->service = $client->make('sheets');
    }

    /**
     * @return GoogleSheets
     */
    public function getService(): GoogleSheets
    {
        return $this->service;
    }

    public function spreadsheet(string $spreadsheetId): self
    {
        $this->spreadsheetId = $spreadsheetId;

        return $this;
    }

    public function sheet(string $sheet): self
    {
        $this->sheet = $sheet;

        return $this;
    }

    public function range(string $range): self
    {
        $this->range = $range;

        return $this;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function sheetList(): array
    {
        $list = [];

        $sheets = $this->service->spreadsheets->get($this->spreadsheetId)->getSheets();

        foreach ($sheets as $sheet) {
            $list[$sheet->getProperties()->getSheetId()] = $sheet->getProperties()->getTitle();
        }

        return $list;
    }

    public function addSheet(string $sheetTitle): string
    {
        $body = new BatchUpdateSpreadsheetRequest(
            [
                'requests' => [
                    'addSheet' => [
                        'properties' => [
                            'title' => $sheetTitle,
                        ],
                    ],
                ],
            ]
        );

        $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $body);

        return $sheetTitle;
    }

    /**
     * @param array <int, string|mixed> $value
     */
    public function update(array $value, string $valueInputOption = 'RAW'): void
    {
        $range = $this->fullRange();

        $batch = new BatchUpdateValuesRequest();
        $batch->setValueInputOption($valueInputOption);

        $valueRange = new ValueRange();
        $valueRange->setValues($value);
        $valueRange->setRange($range);

        $batch->setData($valueRange);

        $this->spreadsheetsValues()->batchUpdate($this->spreadsheetId, $batch);
    }

    public function clear(): void
    {
        $range = $this->fullRange();

        $clear = new ClearValuesRequest();

        $this->spreadsheetsValues()->clear($this->spreadsheetId, $range, $clear);
    }

    private function fullRange(): string
    {
        if (0 === strlen($this->range)) {
            return $this->sheet;
        }

        if (false === mb_strpos($this->range, '!')) {
            return $this->sheet . '!' . $this->range;
        }

        return $this->range;
    }

    private function spreadsheetsValues(): SpreadsheetsValues
    {
        return $this->service->spreadsheets_values;
    }
}
