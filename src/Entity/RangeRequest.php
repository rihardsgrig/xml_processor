<?php

declare(strict_types=1);

namespace Xml\Processor\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Xml\Processor\Exception\FailedToProcessFileException;

class RangeRequest
{
    /**
     * @var array<int, string>
     */
    private array $header;


    /**
     * @var ArrayCollection<int, array<int, string>>
     */
    private ArrayCollection $items;

    private function __construct()
    {
    }

    /**
     * @param ArrayCollection<int,Coffee> $items
     */
    public static function create(ArrayCollection $items): self
    {
        $obj = new self();

        if (false === $items->first()) {
            throw FailedToProcessFileException::emptyFile();
        }

        $obj->header = array_keys($items->first()->toArray());
        $obj->items = $items->map(
            function (Coffee $c) {
                return array_values($c->toArray());
            }
        );

        return $obj;
    }

    /**
     * @return ArrayCollection<int, array<int, string>>
     */
    public function items(): ArrayCollection
    {
        return $this->items;
    }

    /**
     * @return array<int, string>
     */
    public function header(): array
    {
        return $this->header;
    }

    public function range(): string
    {
        return sprintf(
            '%s:%s',
            'A1',
            $this->getColumnNameFromNumber(count($this->header)) . (count($this->items) + 1)
        );
    }

    private function getColumnNameFromNumber(int $num): string
    {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric); //65 === A
        $num2 = (int)(($num - 1) / 26);
        if ($num2 > 0) {
            return $this->getColumnNameFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }
}
