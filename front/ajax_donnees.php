<?php
if (\Session::haveRight(self::$rightname, CREATE)) {
    function testajax(){
        echo "coucou ajax";
    }
     
    testajax();
?>