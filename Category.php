<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 005 05.10.18
 * Time: 3:59 AM
 */

class Category
{
    private $categoryName;
    private $subcategories;

    /**
     * Category constructor.
     * @param $categoryName
     * @param $subcategories
     */
    public function __construct($categoryName, $subcategories)
    {
        $this->categoryName = $categoryName;
        $this->subcategories = $subcategories;
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
    public function getSubcategories()
    {
        return $this->subcategories;
    }


}