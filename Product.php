<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 003 03.10.18
 * Time: 11:06 PM
 */

class Product
{
    private $name;
    private $category;
    private $imageUrl;
    private $price;
    private $brand;
    private $description;
    private $characteristics;

    /**
     * Product constructor.
     * @param $name
     * @param $category
     * @param $imageUrl
     * @param $price
     * @param $brand
     * @param $description
     * @param $characteristics
     */
    public function __construct($name, $category, $imageUrl, $price, $brand, $description, $characteristics)
    {
        $this->name = $name;
        $this->category = $category;
        $this->imageUrl = $imageUrl;
        $this->price = $price;
        $this->brand = $brand;
        $this->description = $description;
        $this->characteristics = $characteristics;
    }


}