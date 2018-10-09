<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 005 05.10.18
 * Time: 3:49 AM
 */

class Subcategory
{
    private $categoryName;
    private $subcategoryName;
    private $subcategoryLinks;

    /**
     * Subcategory constructor.
     * @param $categoryName
     * @param $subcategoryName
     * @param $subcategoryLinks
     */
    public function __construct($categoryName, $subcategoryName, $subcategoryLinks)
    {
        $this->categoryName = $categoryName;
        $this->subcategoryName = $subcategoryName;
        $this->subcategoryLinks = $subcategoryLinks;
    }

    /**
     * @return mixed
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * @return mixed
     */
    public function getSubcategoryName()
    {
        return $this->subcategoryName;
    }

    /**
     * @return mixed
     */
    public function getSubcategoryLinks()
    {
        return $this->subcategoryLinks;
    }
}