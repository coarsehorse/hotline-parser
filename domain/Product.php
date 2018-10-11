<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 003 03.10.18
 * Time: 11:06 PM
 */

/**
 * Hotline Product data model class.
 */
class Product
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $category;

    /**
     * @var array
     */
    private $images;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string
     */
    private $brand;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $characteristics;

    /**
     * Product constructor.
     *
     * @param $name string The name.
     * @param $url string The product hotline url.
     * @param $category string The category name.
     * @param $images array The images array.
     * @param $price string The lowest price.
     * @param $brand string The brand name.
     * @param $description string The description.
     * @param $characteristics array The characteristics array.
     */
    public function __construct($name, $url, $category, $images, $price, $brand, $description, $characteristics)
    {
        $this->name = $name;
        $this->url = $url;
        $this->category = $category;
        $this->images = $images;
        $this->price = $price;
        $this->brand = $brand;
        $this->description = $description;
        $this->characteristics = $characteristics;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getCharacteristics()
    {
        return $this->characteristics;
    }
}