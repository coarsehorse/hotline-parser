<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 008 08.10.18
 * Time: 2:26 AM
 */

require __DIR__ . '\..\vendor\autoload.php';

use Automattic\WooCommerce\Client;

$woo = new Client(
    'http://localhost/wordpress',
    'ck_c3c3132a913beee062369ad9623a996fa335e228',
    'cs_eba4393c988af006885f01feb31724b9d5b3a37e',
    [
        'wp_api' => true,
        'version' => 'wc/v2',
    ]
);

$categories = (array)$woo->get("products/categories");
var_dump($categories);

$categoryIds = array_map(function ($c) {
    return get_object_vars($c)["id"];
}, $categories);

var_dump($categories[0]);

