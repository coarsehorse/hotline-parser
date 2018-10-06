<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 006 06.10.18
 * Time: 2:41 AM
 */

include_once "get_product.php";

/*$str = "\"qwe\":\n\"qwe\"";

echo "\"" . $str . "\"\n";

$str = preg_replace("/\n/", "\\\\n", $str);
$str = preg_replace("/\"/", "\\\"", $str);

echo "\"" . $str . "\"\n";*/

echo json_encode(
    getProduct("https://hotline.ua/auto-nasosy-i-kompressory/vitol-ka-u12051-uragan/")
    ->getName(),
    JSON_UNESCAPED_UNICODE);