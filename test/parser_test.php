<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 008 08.10.18
 * Time: 5:55 AM
 */

include_once "../parser/HotlineParser.php";

$parser = new HotlineParser();

$cat = $parser->getCategories(1);

var_dump($cat);