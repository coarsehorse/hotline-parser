<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 004 04.10.18
 * Time: 11:21 PM
 */


include "Category.php";
include "Subcategory.php";

/**
 * Parses the hotline.ua catalog and gets first $n categories.
 *
 * @param $n integer The number of categories that will be returned.
 * @return array the array of categories(Category objects).
 * @throws Exception The parsing error with specific message.
 */
function findCategories($n = 5) {
    $categories = array();

    // Getting catalog page
    $contents = file_get_contents("https://hotline.ua/catalog/");
    $dom = new DOMDocument();
    @$dom->loadHTML($contents);
    $xpath = new DOMXpath($dom);

    // Getting categories columns(single row cells)
    $catCells = $xpath
        ->query("//div[@class='viewbox']/div[@class='row']/div[contains(@class, 'cell-')]");

    if ($catCells->length != 0) {
        // One category cell contains on the one level multiple category names with their category trees.
        // Like: [cat_name cat_tree cat_name cat_tree]
        foreach ($catCells as $catCell) {
            // Getting all category names + all category trees from single cell
            $categoryNames = $xpath->evaluate(".//p[@class='h4']", $catCell);
            $categoryTrees = $xpath
                ->evaluate(".//ul[@class='tree-navigation']", $catCell);

            // Check whether the all category trees have their category names.
            if ($categoryNames->length != 0 and $categoryTrees->length == $categoryNames->length) {
                // Process each category name and corresponding category tree
                for ($i = 0; $i < $categoryNames->length; $i++) {
                    // Complete the execution if $n categories found
                    if (count($categories) >= $n) {
                        break;
                    }

                    $categoryName = trim($categoryNames->item($i)->textContent);
                    $subcategories = getSubcategories($xpath, $categoryTrees->item($i));

                    $categories[] = new Category($categoryName, $subcategories);
                }
            } else {
                throw new Exception('Some error occurred on getting category names and its trees');
            }
        }
    } else {
        throw new Exception('Some error occurred during getting category cells');
    }

    return $categories;
}

/**
 * Parses the subcategories in the category tree.
 *
 * @param $xpath DOMXpath The document xpath object.
 * @param $treeNavigationItem DOMElement The category tree.
 * @return array The array of subcategories.
 */
function getSubcategories($xpath, $treeNavigationItem) {
    $subcategoriesArray = array();

    // Find subcategories
    foreach ($xpath->evaluate("./li", $treeNavigationItem) as $arrowRight) {
        $subcategoryName = trim($xpath->evaluate("./span", $arrowRight)->item(0)->textContent);
        $subcatLinks = array();

        // Find subcategories links
        foreach ($xpath->evaluate("./ul/li/a/@href", $arrowRight) as $subcatLinkPart) {
            $linkPart = trim($subcatLinkPart->textContent);

            // Exclude unstandardized subcategories
            $exceptionsArray = array("/zapchasti/");
            $linkIsBad = false;

            // Check if url contains any exception
            foreach ($exceptionsArray as $exception) {
                if (strpos($linkPart, $exception) !== false) {
                    $linkIsBad = true;
                    break;
                }
            }

            if (!$linkIsBad) {
                $subcatLinks[] = 'https://hotline.ua' . $linkPart;
            }
        }

        $subcategoriesArray[] = new Subcategory($subcategoryName, $subcatLinks);
    }

    return $subcategoriesArray;
}

// Some tests
//var_dump(findCategories(6));