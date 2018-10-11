<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 005 05.10.18
 * Time: 3:49 AM
 */

/**
 * Hotline Subcategory data model class.
 */
class Subcategory
{
    private $categoryName;
    private $subcategoryName;
    private $subcategoryLinks;

    /**
     * Subcategory constructor.
     *
     * @param $categoryName string The category name.
     * @param $subcategoryName string The subcategory name.
     * @param $subcategoryLinks array The subcategory links.
     */
    public function __construct($categoryName, $subcategoryName, $subcategoryLinks)
    {
        $this->categoryName = $categoryName;
        $this->subcategoryName = $subcategoryName;
        $this->subcategoryLinks = $subcategoryLinks;
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * @return string
     */
    public function getSubcategoryName()
    {
        return $this->subcategoryName;
    }

    /**
     * @return array
     */
    public function getSubcategoryLinks()
    {
        return $this->subcategoryLinks;
    }
}