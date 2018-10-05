<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 005 05.10.18
 * Time: 5:06 AM
 */

include "Subcategory.php";

/**
 * Parses the product links in the subcategory.
 *
 * @param $subcategory Subcategory The subcategory with its links.
 * @return array the array of the subcategory product links.
 */
function get_product_links($subcategory)
{
    $productLinks = array();

    // Parsing product links in each subcategory link
    foreach ($subcategory->getSubcategoryLinks() as $subLink) {
        // Getting link page
        $contents = file_get_contents($subLink);
        $dom = new DOMDocument();
        @$dom->loadHTML($contents);
        $xpath = new DOMXpath($dom);

        // Getting product links
        $productLinksQuery = $xpath->query("//div[@class='item-img']/a/@href");

        foreach ($productLinksQuery as $link) {
            $productLinks[] = "https://hotline.ua" . $link->textContent;
        }
    }

    return $productLinks;
}

// Some tests
//$subc = new Subcategory("Для рыб и рептилий",
//    array("https://hotline.ua/zootovary/akvariumy/",
//        "https://hotline.ua/zootovary/aksessuary-dlya-akvariumov/"));
//var_dump(get_product_links($subc));