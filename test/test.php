<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 004 04.10.18
 * Time: 4:59 PM
 */

include_once "parser/get_categories.php";
include_once "parser/get_product_links.php";
include_once "parser/get_product.php";

$time_start = microtime(true);
$categ = null;

try {
    $categ = getCategories(1);
} catch (Exception $e) {
    echo $e->getTraceAsString();
}

$catalogTime = microtime(true);

echo "Catalog page downloaded by " . number_format($catalogTime - $time_start, 2) . " sec\n";

$subcat = $categ[0]->getSubcategories()[1];

echo "Working with " . count($subcat->getSubcategoryLinks()) . " subcategory links\n";

$productLinks = getSubcategoryProductLinks($subcat);
$productLinksTime = microtime(true);

echo "Got " . count($productLinks) . " product links by "
    . number_format($productLinksTime - $catalogTime, 2) . " sec\n";

$products = array();
/////// PRODUCTS COUNTER
$num = 100;
//////
$lastProductTime = microtime(true);

for ($i = 0; $i < $num and $i < count($productLinks); $i++) {
    $products[] = getProduct($productLinks[$i]);
    echo "Product[$i] " . $productLinks[$i] . " parsed by "
        . number_format(microtime(true) - $lastProductTime, 2)
        . " sec\n";
    $lastProductTime = microtime(true);
    sleep(1);
}

echo count($products) . " products parsed by "
    . number_format(microtime(true) - $productLinksTime, 2)
    . " sec\n";

$time_end = microtime(true);
$execution_time = $time_end - $time_start;

echo 'Time: ' . number_format($execution_time, 2) . " sec\n";

file_put_contents("products.json", productsToStr($products));

function productsToStr($products)
{
    $productsStr = "[\n";
    $delimiter1 = "";

    foreach ($products as $p) {
        /* @var $p Product */
        $characStr = null;
        $delimiter2 = "";

        foreach ($p->getCharacteristics() as $c) {
            $characStr .= $delimiter2 . "\"" . fix($c["name"]) . "\": \"" . fix($c["value"]) . "\"";
            $delimiter2 = ", ";
        }

        $productsStr .= $delimiter1 . "{\n"
            . "\t\"name\": \"" . fix($p->getName()) . "\",\n"
            . "\t\"url\": \"" . fix($p->getUrl()) . "\",\n"
            . "\t\"category\": \"" . fix($p->getCategory()) . "\",\n"
            . "\t\"imageUrl\": \"" . fix($p->getImageUrl()) . "\",\n"
            . "\t\"price\": \"" . fix($p->getPrice()) . "\",\n"
            . "\t\"brand\": \"" . fix($p->getBrand()) . "\",\n"
            . "\t\"description\": \"" . fix($p->getDescription()) . "\",\n"
            . "\t\"characteristics\": {" . $characStr . "}\n"
            . "}";
        $delimiter1 = ",\n";
    }

    return $productsStr . "]\n";
}

function fix($str) {
    $str = preg_replace("/\n/", "\\\\n", $str);
    $str = preg_replace("/\"/", "\\\"", $str);

    return $str;
}