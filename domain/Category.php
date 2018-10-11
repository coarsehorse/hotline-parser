<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 005 05.10.18
 * Time: 3:59 AM
 */

/**
 * Hotline Category data model class.
 */
class Category
{
    /**
     * @var string
     */
    private $categoryName;

    /**
     * @var array
     */
    private $subcategories;

    /**
     * Category constructor.
     *
     * @param $categoryName string The category name.
     * @param $subcategories array The subcategories.
     */
    public function __construct($categoryName, $subcategories)
    {
        $this->categoryName = $categoryName;
        $this->subcategories = $subcategories;
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * @return array
     */
    public function getSubcategories()
    {
        return $this->subcategories;
    }
}