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
$subcPerCategoryNum = 1; // number of subcategories per category
$productsPerSubcatNum = 3; // number of products per subcategory

// Offsets
$categoryOffset = 14;
$subcategoryOffset = 4;
$productsOffset = 24;

// Prepare parser and dao
$parser = new HotlineParser();
$dao = WoocomerceDAO::getInstance();

// Parsing categories
$categories = $parser->getCategories($categoryNum, $categoryOffset);

echo "Shop categories(" . count($categories) . ") has been loaded\n";

$wooCategories = $dao->getCategoriesNameId();
$wooCategoryNames = array_map(function ($nameId) {
    return $nameId["name"];
}, $wooCategories);

foreach ($categories as $category) {
    $categoryId = 0;

    // Check if category exists in Woo
    if (!in_array($category->getCategoryName(), $wooCategoryNames)) {
        // Create new category and get id
        $q = $dao->addCategory($category->getCategoryName());
        if ($q) {
            $categoryId = get_object_vars($q)["id"];

            echo "Category \"" . $category->getCategoryName() . "\" has been added to the WooCommerce\n";
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
    $subcCounter = 0;
    for ($i = $subcategoryOffset;
         $i < count($subcategories), $subcCounter < $subcPerCategoryNum; $i++, $subcCounter++) {

        $subcategory = $subcategories[$i];
        $subcategoryId = 0;

        // Check if the subcategory exists in Woo
        if (!in_array($subcategory->getSubcategoryName(), $wooCategoryNames)) {
            // Create new subcategory and get id
            $q = $dao->addCategory($subcategory->getSubcategoryName(), $categoryId);
            if ($q)
                $subcategoryId = get_object_vars($q)["id"];
            else
                throw new Exception("\nSomething goes wrong with subcategory addition\n");
        } else {
            // Find subcategory id
            $filtered = array_values(array_filter($wooCategories, function ($wc) use ($subcategory) {
                return $wc["name"] == $subcategory->getSubcategoryName();
            }));
            $subcategoryId = $filtered[0]["id"];
        }

        // Parse n products from subcategory and save it
        $productLinks = $parser->getSubcategoryProductLinks($subcategory, $productsPerSubcatNum, $productsOffset);

        $parsedProducts = array();

        foreach ($productLinks as $productLink) {
            $parsedProducts[] = $parser->getProduct($productLink);
        }
        echo "Subcategory(" . $subcategory->getSubcategoryName()
            . ") products(" . count($parsedProducts) . ") has been parsed\n";

        // Parse products brands
        $brands = array_map(function ($p) { return $p->getBrand(); }, $parsedProducts);
        $wooBrands = $dao->getBrands();
        $wooBrandNames = array_map(function ($brand) {
            return $brand["name"];
        }, $wooBrands);

        foreach ($brands as $brand) {
            // Add an absent brands
            if (!in_array($brand, $wooBrandNames)) {
                $dao->addBrand($brand);
                $wooBrandNames[] = $brand;
                echo "Brand(" . $brand . ") has been added to the WooCommerce\n";
            }
        }

        // Refresh Woo brands("Perfect WooCommerce Brands" API does not returns a newly added brand id)
        $wooBrands = $dao->getBrands();

        if (count($wooBrands) == 0) {
            throw new Exception("At that moment Woo brands must exist");
        }

        // Upload products from this subcategory
        foreach ($parsedProducts as $product) {
            $brandId = array_values(array_filter($wooBrands, function ($wooBrand) use ($product) {
                return $wooBrand["name"] == $product->getBrand();
            }));
            $brandId = $brandId[0]["term_id"];
            //$dao->uploadProduct($product, $subcategoryId, $brandId);
            var_dump($product);
        }
        echo "Subcategory(" . $subcategory->getSubcategoryName()
            . ") products(" . count($parsedProducts). ") has been uploaded\n";
    }
}