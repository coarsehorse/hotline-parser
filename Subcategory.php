<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 005 05.10.18
 * Time: 3:49 AM
 */

class Subcategory
{
    private $subcategoryName;
    private $subcategoryLinks;

    /**
     * Subcategory constructor.
     * @param $subcategoryName
     * @param $subcategoryLinks
     */
    public function __construct($subcategoryName, $subcategoryLinks)
    {
        $this->subcategoryName = $subcategoryName;
        $this->subcategoryLinks = $subcategoryLinks;
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