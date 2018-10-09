<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 009 09.10.18
 * Time: 11:15 PM
 */

include_once "../parser/HotlineParser.php";
include_once "../dao/WoocomerceDAO.php";

$parser = new HotlineParser();
$dao = WoocomerceDAO::getInstance();

$dao->uploadProduct($parser->getProduct("https://hotline.ua/mobile-mobilnye-telefony-i-smartfony/samsung-j105h-galaxy-j1-mini-white/"));

