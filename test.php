<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 004 04.10.18
 * Time: 4:59 PM
 */

$arr = array(1, 2, 3, 4);
foreach ($arr as $value) {
    $value = $value * 2;
}
// $arr = array(2, 4, 6, 8)

// Без unset($value), $value все еще ссылается на последний элемент: $arr[3]

foreach ($arr as $key => $value) {
    // $arr[3] будет перезаписываться значениями $arr при каждой итерации цикла
    echo "{$key} => {$value} ";
    print_r($arr);

    // "//li[contains(concat(' ', normalize-space(@class), ' '), ' level-1 ' )]/a/@href");
}