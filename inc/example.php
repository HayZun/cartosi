<?php

class Example extends CommonDBTM {
    function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
        switch ($item::getType()) {
           case Ticket::getType():
           case Computer::getType():
           case Phone::getType():
              return __('Tab from my plugin', 'example');
              break;
        }
        return '';
     }
     
     static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
        switch ($item::getType()) {
           case Ticket::getType():
              //display form for computers
              self::displayTabContentForTicket($item);
              break;
           case Phone::getType():
              self::displayTabContentForPhone($item);
              break;
        }
        if ($item->getType() == 'ObjetDuCoeur') {
           $monplugin = new self();
           $ID = $item->getField('id');
          // j'affiche le formulaire
           $monplugin->nomDeLaFonctionQuiAfficheraLeContenuDeMonOnglet();
        }
        return true;
     }

     private static function displayTabContentForTicket(Ticket $item) {
        //...
     }

     private static function displayTabContentForComputer(Computer $item) {
        //...
     }
     
     private static function displayTabContentForPhone(Phone $item) {
        //...
     }

     private static function nomDeLaFonctionQuiAfficheraLeContenuDeMonOnglet() {
        echo "toto";
     }
}