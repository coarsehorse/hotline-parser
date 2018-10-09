<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 008 08.10.18
 * Time: 5:38 AM
 */

include_once "../dao/WoocomerceDAO.php";

$dao = WoocomerceDAO::getInstance();

var_dump($dao->getCategoryNames());