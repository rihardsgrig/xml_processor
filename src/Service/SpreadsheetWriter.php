<?php

namespace Xml\Processor\Service;

use Xml\Processor\Entity\RangeRequest;

interface SpreadsheetWriter
{
    public function write(string $spreadsheetId, RangeRequest $request): void;

    public function clear(string $spreadsheetId, RangeRequest $request): void;
}
