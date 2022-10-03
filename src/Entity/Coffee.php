<?php

declare(strict_types=1);

namespace Xml\Processor\Entity;

use SimpleXMLElement;
use Webmozart\Assert\Assert;

class Coffee
{
    private string $id;
    private string $categoryName;
    private string $sku;
    private string $name;
    private string $description;
    private string $shortdesc;
    private string $price;
    private string $link;
    private string $image;
    private string $brand;
    private string $rating;
    private string $caffeineType;
    private string $count;
    private string $flavored;
    private string $seasonal;
    private string $instock;
    private string $facebook;
    private string $isKCup;

    private function __construct()
    {
    }

    public static function create(SimpleXMLElement $xml): self
    {
        self::validate($xml);
        $obj = new self();

        $obj->id = (string) $xml->entity_id;
        $obj->categoryName = (string) $xml->CategoryName;
        $obj->sku = (string) $xml->sku;
        $obj->name = (string) $xml->name;
        $obj->description = (string) $xml->description;
        $obj->shortdesc = (string) $xml->shortdesc;
        $obj->price = (string) $xml->price;
        $obj->link = (string) $xml->link;
        $obj->image = (string) $xml->image;
        $obj->brand = (string) $xml->Brand;
        $obj->rating = (string) $xml->Rating;
        $obj->caffeineType = (string) $xml->CaffeineType;
        $obj->count = (string) $xml->Count;
        $obj->flavored = (string) $xml->Flavored;
        $obj->seasonal = (string) $xml->Seasonal;
        $obj->instock = (string) $xml->Instock;
        $obj->facebook = (string) $xml->Facebook;
        $obj->isKCup = (string) $xml->IsKCup;

        return $obj;
    }

    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'Id' => $this->id,
            'Category Name' => $this->categoryName,
            'SKU' => $this->sku,
            'Name' => $this->name,
            'Description' => $this->description,
            'Shortdesc' => $this->shortdesc,
            'Price' => $this->price,
            'Link' => $this->link,
            'Image' => $this->image,
            'Brand' => $this->brand,
            'Rating' => $this->rating,
            'Caffeine Type' => $this->caffeineType,
            'Count' => $this->count,
            'Flavored' => $this->flavored,
            'Seasonal' => $this->seasonal,
            'In stock' => $this->instock,
            'Facebook' => $this->facebook,
            'IsKCup' => $this->isKCup,
        ];
    }

    private static function validate(SimpleXMLElement $xml): void
    {
        Assert::propertyExists($xml, 'entity_id', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'CategoryName', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'sku', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'name', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'description', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'shortdesc', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'price', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'link', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'image', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'Brand', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'Rating', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'CaffeineType', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'Count', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'Flavored', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'Seasonal', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'Instock', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'Facebook', 'XML item is missing a node: %s.');
        Assert::propertyExists($xml, 'IsKCup', 'XML item is missing a node: %s.');
    }
}
