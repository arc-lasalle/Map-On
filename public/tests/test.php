<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
include("../../application/libraries/parsers/sql_parser.php");

$parser = new sql_parser();

$parser->setQuery("Select t1.c1, t2.c2 as t2c2 from b, gr.bb where c");

$result = $parser->parseSQL();

echo "Hey";
echo "<pre>";
print_r($result);
echo "</pre>";