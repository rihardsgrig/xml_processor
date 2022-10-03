<?php

declare(strict_types=1);

namespace Xml\Processor\Exception;

use RuntimeException;
use Throwable;

class FailedToWriteFileException extends RuntimeException
{
    private const EXCEPTION_CODE = 0;

    private function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, self::EXCEPTION_CODE, $previous);
    }

    public static function unableToWriteToSheet(string $fileId, ?Throwable $previous = null): self
    {
        return new self(sprintf('Failed to write data to spreadsheet with id: "%s".', $fileId), $previous);
    }
}
