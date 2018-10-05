<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 004 04.10.18
 * Time: 4:59 PM
 */

include_once "get_categories.php";
include_once "get_product_links.php";
include_once "get_product.php";

$categ = null;
try {
    $categ = getCategories(1);
} catch (Exception $e) {
    echo $e->getTraceAsString();
}

/* @var $categ Category */
$subcat = $categ[0]->getSubcategories()[1];
var_dump($subcat);
$productLinks = getProductLinks($subcat);
var_dump($productLinks);
$products = array();

foreach ($productLinks as $link) {
    $products[] = getProduct($link);
}

var_dump($products);

// ! Exclude price(go to another site) product links