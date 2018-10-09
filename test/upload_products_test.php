<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 008 08.10.18
 * Time: 5:44 AM
 */

include_once "../parser/HotlineParser.php";
include_once "../dao/WoocomerceDAO.php";

// Input data
$categoryNum = 1; // number of all categories
$subcPerCategoryNum = 2; // number of subcategories per category
$productsPerSubcatNum = 3; // number of products per subcategory

// Prepare parser and dao
$parser = new HotlineParser();
$dao = WoocomerceDAO::getInstance();

// Parsing categories
$categories = $parser->getCategories($categoryNum);

echo "Shop categories(" . count($categories) . ") has been loaded\n";

$wooCategories = $dao->getCategoriesNameId();

echo "WooCommerce categories(" . count($wooCategories) . ") has been loaded\n";

$wooCategoryNames = array_map(function ($nameId) {
    return $nameId["name"];
}, $wooCategories);

foreach ($categories as $category) {
    /*$parsedCategories[] = $category;*/
    $categoryId = 0;

    // Check if category exists in Woo
    if (!in_array($category->getCategoryName(), $wooCategoryNames)) {
        // Create new category and get id
        $q = $dao->addCategory($category->getCategoryName());
        if ($q) {
            $categoryId = get_object_vars($q)["id"];

            echo "Category \"" . $category->getCategoryName() . "\" was added to the WooCommerce\n";
        } else {
            throw new Exception("Something goes wrong with category addition");
        }
    } else {
        // Find category id
        $filtered = array_values(array_filter($wooCategories, function ($wc) use ($category) {
            return $wc["name"] == $category->getCategoryName();
        }));
        $categoryId = $filtered[0]["id"];
    }

    // Filter out empty subcategories
    $subcategories = array_values(array_filter($category->getSubcategories(), function ($s) {
        return count($s->getSubcategoryLinks()) > 0;
    }));

    // Parse subcategories
    for ($i = 0; $i < $subcPerCategoryNum; $i++) {
        $subcategory = $subcategories[$i];
        /*$parsedSubcategories[] = $subcategory;*/
        $subcategoryId = 0;

        // Check if the subcategory exists in Woo
        if (!in_array($subcategory->getSubcategoryName(), $wooCategoryNames)) {
            // Create new subcategory and get id
            $q = $dao->addCategory($subcategory->getSubcategoryName(), $categoryId);
            if ($q) {
                $subcategoryId = get_object_vars($q)["id"];

                echo "Subcategory \"" . $subcategory->getSubcategoryName() . "\" was added to the WooCommerce\n";
            } else {
                throw new Exception("\nSomething goes wrong with subcategory addition\n");
            }
        } else {
            // Find subcategory id
            $filtered = array_values(array_filter($wooCategories, function ($wc) use ($subcategory) {
                return $wc["name"] == $subcategory->getSubcategoryName();
            }));
            $subcategoryId = $filtered[0]["id"];
        }


        // Parse n products from subcategory and save it
        $productLinks = $parser->getSubcategoryProductLinks($subcategory, $productsPerSubcatNum);

        echo "Subcategory(" . $subcategory->getSubcategoryName()
            . ") product links(" . count($productLinks) . ") has been parsed\n";

        $parsedProducts = array();

        foreach ($productLinks as $productLink) {
            $parsedProducts[] = $parser->getProduct($productLink);
        }
        echo "Subcategory(" . $subcategory->getSubcategoryName()
            . ") products(" . count($parsedProducts) . ") has been parsed\n";

        // Upload products from this subcategory
        foreach ($parsedProducts as $product) {
            $dao->uploadProduct($product, $subcategoryId);
        }
        echo "Subcategory(" . $subcategory->getSubcategoryName()
            . ") products(" . count($parsedProducts). ") has been uploaded\n";
    }
}