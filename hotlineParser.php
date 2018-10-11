#!/usr/bin/php
<?php
/**
 * Starter Script
 *
 * Created by PhpStorm.
 * User: User
 * Date: 010 10.10.18
 * Time: 7:55 PM
 */

require_once __DIR__ . "/parser/HotlineParser.php";

// Available options
$params = [
    'help:' => '',
    'cn' => 'category-number',
    'sn' => 'subcategory-number',
    'pn' => 'product-number',
    'co' => 'category-offset',
    'so' => 'subcategory-offset',
    'po' => 'product-offset',
    'dbn' => 'database-name',
    'tp' => 'table-prefix'
];

// Transform options into longopts
$longopts = array_map(function ($el) {
    return $el . ":";
}, array_merge($params, array_keys($params)));

// Receive input options
$options = getopt('', $longopts);

// Default values
$cn = 5;
$sn = 4;
$pn = 10;
$co = 0;
$so = 0;
$po = 0;
// These values are ignored due to WooCommerce API using instead of manually working with db.
$dbn = "";
$tp = "";

// Process received options
if (count($options) == 0) {
    echo "No options was recognized. Using defaults. For more info run again with flag --help" . "\n";
} else {
    if (isset($options['help'])) {
        $help = "usage: php script.php "
            . "[--help]"
            . "[--cn|--category-number][--sn|--subcategory-number][--pn|--product-number]"
            . "[--co|--category-offset][--so|--subcategory-offset][--po|--product-offset]"
            . "[--dbn|--database-name][--tp|--table-prefix]\n"
            . "Options:\n"
            . "\t[--help]\t\t\t\tShow this message\n"
            . "\t[--cn|--category-number]\t\tNumber of categories to be parsed\n"
            . "\t[--sn|--subcategory-number]\t\tNumber of subcategories per category to be parsed\n"
            . "\t[--pn|--product-number]\t\t\tNumber of products per subcategory to be parsed\n"
            . "\t[--co|--category-offset]\t\tOffset from the beginning when parsing categories\n"
            . "\t[--so|--subcategory-offset]\t\tOffset from the beginning when parsing subcategories\n"
            . "\t[--po|--product-offset]\t\t\tOffset from the beginning when parsing products\n"
            . "\t[--dbn|--database-name]\t\t\tWP database name\n"
            . "\t[--tp|--table-prefix]\t\t\tWP table prefix\n"

            . "You can specify any options selectively, remaining options will get the default values.";

        die($help);
    }
    readOption("cn", $cn, $params, $options);
    readOption("sn", $sn, $params, $options);
    readOption("pn", $pn, $params, $options);
    readOption("co", $co, $params, $options);
    readOption("so", $so, $params, $options);
    readOption("po", $po, $params, $options);
    readOption("dbn", $dbn, $params, $options);
    readOption("tp", $tp, $params, $options);
}

// Starting
$parser = new HotlineParser();

try {
    echo ":::::::::::::::::::::::::::::\n";
    echo ":::: Hotline parser v1.0 ::::\n";
    echo ":::::::::::::::::::::::::::::\n\n";
    echo "The parsing is running...\n";
    $parser->parse($cn, $sn, $pn, $co, $so, $po);
    echo "Done.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

/**
 * Reading option by name into given link if it exist in any representation(both short and full).
 *
 * @param $name string The option SHORT name.
 * @param $link integer The option value.
 * @param $params array The array with short => full representation of the option.
 * @param $options array The user input options.
 */
function readOption($name, &$link, $params, $options) {
    if (isset($options[$name]) || isset($options[$params[$name]])) {
        $link = isset($options[$name]) ? (int) $options[$name] : (int) $options[$params[$name]];
    }
}
