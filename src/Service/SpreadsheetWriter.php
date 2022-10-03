<?php

namespace Xml\Processor\Service;

use Xml\Processor\Entity\RangeRequest;

interface SpreadsheetWriter
{
    public function write(string $spreadsheetId, string $sheetName, RangeRequest $request): void;
}
