<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 008 08.10.18
 * Time: 5:45 AM
 */

require_once __DIR__ . "/../domain/Category.php";
require_once __DIR__ . "/../domain/Subcategory.php";
require_once __DIR__ . "/../domain/Product.php";
require_once __DIR__ . "/../dao/WoocomerceDAO.php";

/**
 * This class provides the ability to parse hotline.ua.
 * Also it wraps parsed data into handy domain objects.
 */
class HotlineParser
{
    /**
     * Parses the hotline.ua catalog and gets first $n categories.
     *
     * @param $n integer The number of categories that will be returned.
     * @param int $categoriesOffset The parser start will be shifted by the specified offset.
     * @return array the array of categories(Category objects).
     * @throws Exception The parsing error with specific message.
     */
    public function getCategories($n = 2, $categoriesOffset = 0) {
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
            $categoryCounter = 0; // number of parsed categories

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
                        $categoryCounter++;

                        // Check offset
                        if (($categoryCounter - 1) >= $categoriesOffset) {
                            // Complete an execution if the $n categories are found
                            if (count($categories) >= $n) {
                                return $categories;
                            }

                            $categoryName = trim($categoryNames->item($i)->textContent);
                            $subcategories = $this->getSubcategories($xpath, $categoryTrees->item($i), $categoryName);

                            $categories[] = new Category($categoryName, $subcategories);
                        }
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
     * @param $categoryName string The parent category name.
     * @return array The array of subcategories.
     */
    private function getSubcategories($xpath, $treeNavigationItem, $categoryName) {
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

            $subcategoriesArray[] = new Subcategory($categoryName, $subcategoryName, $subcatLinks);
        }

        return $subcategoriesArray;
    }

    /**
     * Parses the product links in the subcategory.
     *
     * @param $subcategory Subcategory The subcategory with its links.
     * @param int $n the desirable number of product links.
     * @param int $productsOffset
     * @return array the array of the subcategory product links. Array length will be <= $n.
     */
    public function getSubcategoryProductLinks($subcategory, $n = 20, $productsOffset = 0)
    {
        $productLinks = array();
        $productsCounter = 0;

        // Parsing product links in each subcategory link
        // subLink is another pseudo category like "Apple" or "Google" in subcategory "Smartphones"
        foreach ($subcategory->getSubcategoryLinks() as $subLink) {
            // Delay to avoid hotline ban
            sleep(1);

            // Getting link page
            $contents = file_get_contents($subLink);
            $dom = new DOMDocument();
            @$dom->loadHTML($contents);
            $xpath = new DOMXpath($dom);

            // Getting product links
            $productLinksQuery = $xpath->query("//div[@class='item-img']/a/@href");

            foreach ($productLinksQuery as $href) {
                $productsCounter++;

                // Check offset
                if (($productsCounter - 1) >= $productsOffset) {
                    // Complete the execution if $n product links are found
                    if (count($productLinks) >= $n) {
                        return $productLinks;
                    }

                    $linkPart = $href->textContent;

                    // Exclude unstandardized products
                    $exceptionsArray = array("/price/");
                    $linkIsBad = false;

                    foreach ($exceptionsArray as $exception) {
                        if (strpos($linkPart, $exception) !== false) {
                            $linkIsBad = true;
                            break;
                        }
                    }

                    if (!$linkIsBad) {
                        $productLinks[] = "https://hotline.ua" . $linkPart;
                    }
                }
            }
        }

        return $productLinks;
    }

    /**
     * Parses the specific product page and constructs Product object.
     *
     * @param $link string The product url.
     * @return Product The product object.
     * @throws Exception
     */
    public function getProduct($link)
    {
        // Getting product page
        $ctx = stream_context_create(array('https' => array('timeout' => 3)));
        $contents = file_get_contents($link, false, $ctx);
        $dom = new DOMDocument();
        @$dom->loadHTML($contents);
        $xpath = new DOMXpath($dom);

        // Parsing name
        $name = $xpath
            ->query("//h1[@datatype='card-title']");
        if ($name->length != 0) {
            $name = trim($name->item(0)->textContent);
        } else {
            $name = null;
        }

        // Parsing category
        $category = $xpath
            ->query("//ul[contains(@class, 'breadcrumbs')]//li/a");
        if ($category->length != 0) {
            $category = trim($category->item(1)->textContent);
        } else {
            $category = null;
        }

        // Parsing images
        $images = $this->getProductImages($link);

        // Parsing price
        $price = null;
        $priceRange = $xpath
            ->query("//span[contains(@class, 'price-lg')]");
        if ($priceRange->length == 0) {
            $singlePrice = $xpath
                ->query("//div[contains(@class, 'resume-price')]//span[contains(@class, 'price-format')]");
            if ($singlePrice->length != 0) {
                // Replace &nbsp
                $price = str_replace(" ", "",
                    explode(",", trim($singlePrice->item(0)->textContent))[0]);
            } else {
                throw new Exception("Price string length = 0 at " . $link);
            }
        } else {
            // Replace &nbsp
            $price = explode("–",
                str_replace(" ", "", trim($priceRange->item(0)->textContent)))[0];
        }

        // Parsing brand
        $brand = $xpath
            ->query("//div[contains(text(), 'Производитель')]/..//p");
        if ($brand->length != 0) {
            $brand = trim($brand->item(0)->textContent);
        } else {
            $brand = null;
        }

        // Parsing description
        $description = $xpath
            ->query("//div[contains(@class, 'text')]/p[@data-specification-box]/text()");
        if ($description->length != 0) {
            $description = trim($description->item(0)->textContent);
            $moreText = $xpath
                ->query("//div[contains(@class, 'text')]/p[@data-specification-box]/span[contains(@class, 'hidden')]/text()");
            if ($moreText->length != 0) {
                $description .= $moreText->item(0)->textContent;
            }
        } else {
            $description = null;
        }

        // Parsing characteristics
        $characteristics = array();
        $charQuery = $xpath->query("//div[@class='clearfix']/div"); // extended characteristics
        if ($charQuery->length != 0) {
            foreach ($charQuery as $table) {
                /* @var $table DOMElement */
                foreach ($xpath->evaluate(".//div[@class='table-row']", $table) as $tableRow) {
                    $cell_4 = $xpath->evaluate("./div[contains(@class, 'cell-4')]", $tableRow);
                    $cell_8 = $xpath->evaluate("./div[contains(@class, 'cell-8')]", $tableRow);

                    $characName = null;
                    if ($cell_4->length != 0) {
                        $cellText = $xpath->evaluate("./text()", $cell_4->item(0));
                        if ($cellText->length != 0) {
                            $characName = trim($cellText->item(0)->textContent);
                        }
                    }

                    $characVal = null;
                    if ($cell_8->length != 0) {
                        $characVal = trim($cell_8->item(0)->textContent);
                    }

                    $characteristics[] = array("name" => $characName, "value" => $characVal);
                }
            }
        }


        return new Product($name, $link, $category, $images, $price, $brand, $description, $characteristics);
    }

    /**
     * @param $productURL
     * @return array
     * @throws Exception
     */
    private function getProductImages($productURL) {
        // Get csrf-token token from usual session
        // Prepare curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $productURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch))
            throw new Exception(curl_error($ch));

        $dom = new DomDocument();
        @$dom->loadHTML($response);
        $xpath = new DOMXpath($dom);

        $tokenQuery = $xpath->query("//meta[@name='csrf-token']/@content");
        if ($tokenQuery->length != 0) {
            $token = $tokenQuery->item(0)->textContent;
        } else {
            throw new Exception("Failed to parse x-csrf-token on " . $productURL);
        }

        // Make POST request to the shop API to get full images
        curl_setopt($ch, CURLOPT_URL, $productURL . "get-product-gallery-content/");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('x-csrf-token: ' . $token));

        $response = curl_exec($ch);

        if (curl_errno($ch))
            throw new Exception(curl_error($ch));

        // Parse response
        @$dom->loadHTML(json_decode($response, true)["data"]);
        $xpath = new DOMXpath($dom);

        // Get image URLs
        $imagesQuery = $xpath->query("//img/@data-gallery-image");
        $images = [];

        if ($imagesQuery->length != 0) {
            foreach ($imagesQuery as $image) {
                $images[] = $image->textContent;
            }
        }

        return $images;
    }

    public function parse($categoryNum = 5, $subcPerCategoryNum = 4, $productsPerSubcatNum = 10,
                   $categoryOffset = 0, $subcategoryOffset = 0, $productsOffset = 0) {

        // Prepare parser and dao
        $dao = WoocomerceDAO::getInstance();

        // Parsing categories
        $categories = $this->getCategories($categoryNum, $categoryOffset);

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
                $productLinks = $this->getSubcategoryProductLinks($subcategory, $productsPerSubcatNum, $productsOffset);

                $parsedProducts = array();

                foreach ($productLinks as $productLink) {
                    $parsedProducts[] = $this->getProduct($productLink);
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
                    $dao->uploadProduct($product, $subcategoryId, $brandId);
                }
                echo "Subcategory(" . $subcategory->getSubcategoryName()
                    . ") products(" . count($parsedProducts). ") has been uploaded\n";
            }
        }
    }
}