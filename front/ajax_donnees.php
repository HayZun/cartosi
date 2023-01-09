<?php
// Entry menu case
define('GLPI_ROOT', '../..');
include (GLPI_ROOT . "/inc/includes.php");

Session::checkRight("config", "w");

function testajax(){
    echo "coucou ajax";
}
 
testajax();
?>