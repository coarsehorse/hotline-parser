<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 008 08.10.18
 * Time: 2:26 AM
 */

require __DIR__ . '\..\vendor\autoload.php';
include_once "../domain/Product.php";

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

/***
 * Class for working with database via Woocomerce API.
 */
class WoocomerceDAO
{
    /**
     * @var WoocomerceDAO
     */
    private static $instance;

    /**
     * @var Client
     */
    private $woo;

    /**
     * Gets the instance via lazy initialization (created on first usage).
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Is not allowed to call from outside to prevent from creating multiple instances.
     */
    private function __construct()
    {
        $this->woo = new Client(
            'http://localhost/wordpress',
            'ck_c3c3132a913beee062369ad9623a996fa335e228',
            'cs_eba4393c988af006885f01feb31724b9d5b3a37e',
            [
                'wp_api' => true,
                'version' => 'wc/v2',
            ]
        );
    }

    /**
     * Prevent the instance from being cloned (which would create a second instance of it.
     */
    private function __clone()
    {
    }

    /**
     * Prevent from being unserialized (which would create a second instance of it.
     */
    private function __wakeup()
    {
    }

    /**
     * Looking for the existing categories in the shop and returning it names.
     *
     * @return array The array of categories array("name" => name,  "id" => id)
     */
    public function getCategoriesNameId()
    {
        try {
            $categories = $this->woo->get("products/categories");

            return array_map(function ($category) {
                $vars = get_object_vars($category);

                return array("name" => $vars["name"], "id" => $vars["id"]);
            }, $categories);
        } catch (HttpClientException $e) {
            echo $e->getMessage() . "\n"; // Error message
        }
    }

    /**
     * Uploading a one product to the WooCommerce db.
     *
     * @param $product Product The parsed product.
     * @param int $wooCategoryId The WooCommerce category id. Default is 0 (Uncategorized category).
     * @param int $wooBrandId The WooCommerce brand id. Default is 0 (no brand).
     * @return array The array with result of operation.
     */
    public function uploadProduct($product, $wooCategoryId = 0, $wooBrandId = 0)
    {
        try {
            $data = array();
            $data["name"] = $product->getName();
            $data["type"] = "simple";
            $data["regular_price"] = $product->getPrice();
            $data["categories"] = array(array("id" => $wooCategoryId));
            $data["images"] = array(array("src" => $product->getImageUrl(), "position" => 0));

            if ($wooBrandId) {
                $data["brands"] = array($wooBrandId);
            }

            // Construct description with characteristics
            $desc = $product->getDescription() . "\n<h4>Characteristics:</h4>\n\n";
            foreach ($product->getCharacteristics() as $charac) {
                $desc = $desc . "<b>" . $charac["name"] . "</b> " . $charac["value"] . "\n";
            }

            $data["description"] = $desc;

            return $this->woo->post("products", $data);
        } catch (HttpClientException $e) {
            echo $e->getMessage() . "\n"; // Error message
        }
    }

    /**
     * Add a new category.
     *
     * @param $name string The category name.
     * @param int $parent The category parent.
     * @param null $imageSrc Optional image url.
     * @return array The array with result of operation.
     */
    function addCategory($name, $parent = 0, $imageSrc = null)
    {
        try {
            $data = array();
            $data["name"] = $name;
            $data["parent"] = $parent;
            if (!is_null($imageSrc)) {
                $data["image"] = array(array("src" => $imageSrc));
            }

            return $this->woo->post("products/categories", $data);
        } catch (HttpClientException $e) {
            echo $e->getMessage() . "\n"; // Error message
        }
    }

    /**
     * Get all the brands.
     *
     * @return array The array with brand objects(in array representation).
     */
    function getBrands()
    {
        try {
            return array_map(function ($brand) {
                return get_object_vars($brand);
            }, $this->woo->get("brands"));
        } catch (HttpClientException $e) {
            echo $e->getMessage() . "\n"; // Error message
        }
    }

    /**
     * Add a new brand.
     *
     * @param $brandName string The brand name. Slug will be generated based on the name.
     * @return array An array with a single boolean value that indicating whether the operation was successful.
     */
    function addBrand($brandName) {
        try {
            $data = ["name" => $brandName];

            return $this->woo->post("brands", $data);
        } catch (HttpClientException $e) {
            echo $e->getMessage() . "\n"; // Error message
        }
    }
}