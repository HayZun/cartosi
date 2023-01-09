<?php
// Entry menu case

Session::checkRight("config", "w");

function testajax(){
    echo "coucou ajax";
}
 
testajax();
?>