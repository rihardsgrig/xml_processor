<?php

declare(strict_types=1);

namespace Xml\Processor\Exception;

use RuntimeException;
use Throwable;
use Webmozart\Assert\InvalidArgumentException;

class FailedToProcessFileException extends RuntimeException
{
    private const EXCEPTION_CODE = 0;

    private function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, self::EXCEPTION_CODE, $previous);
    }

    public static function unprocessableLocation(string $location, ?Throwable $previous = null): self
    {
        return new self(sprintf('File location "%s" is invalid.', $location), $previous);
    }

    public static function invalidFileContents(InvalidArgumentException $e): self
    {
        return new self($e->getMessage(), $e);
    }

    public static function emptyFile(?Throwable $previous = null): self
    {
        return new self('The file is empty.', $previous);
    }
}
