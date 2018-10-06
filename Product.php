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
    private $url;
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
    public function __construct($name, $url, $category, $imageUrl, $price, $brand, $description, $characteristics)
    {
        $this->name = $name;
        $this->url = $url;
        $this->category = $category;
        $this->imageUrl = $imageUrl;
        $this->price = $price;
        $this->brand = $brand;
        $this->description = $description;
        $this->characteristics = $characteristics;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getCharacteristics()
    {
        return $this->characteristics;
    }
}