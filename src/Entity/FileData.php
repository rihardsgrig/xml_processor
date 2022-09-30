<?php

declare(strict_types=1);

namespace Xml\Processor\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class FileData
{
    /**
     * @var ArrayCollection<int, Coffee>
     */
    private ArrayCollection $collection;

    public function __construct()
    {
        $this->collection = new ArrayCollection();
    }

    public function addItem(Coffee $coffee): void
    {
        if ($this->collection->contains($coffee)) {
            return;
        }

        $this->collection->add($coffee);
    }

    /**
     * @return ArrayCollection<int, Coffee>
     */
    public function items(): ArrayCollection
    {
        return $this->collection;
    }
}
