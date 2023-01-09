<?php

/*
   ------------------------------------------------------------------------
   TimelineTicket
   Copyright (C) 2013-2022 by the TimelineTicket Development Team.

   https://github.com/pluginsGLPI/timelineticket
   ------------------------------------------------------------------------

   LICENSE

   This file is part of TimelineTicket project.

   TimelineTicket plugin is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   TimelineTicket plugin is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with TimelineTicket plugin. If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------

   @package   TimelineTicket plugin
   @copyright Copyright (c) 2013-2022 TimelineTicket team
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @link      https://github.com/pluginsGLPI/timelineticket
   @since     2013

   ------------------------------------------------------------------------
 */

class PluginCartosiTicket extends CommonDBTM {

   static function showForTicket(Ticket $ticket) {
      global $CFG_GLPI, $DB, $GLPI_CONFIG_DIR;

      $path = GLPI_CONFIG_DIR."/config_db.php";

      //echo $DB->dbhost;
      //retrieve tab names :
      $req = $DB->query("SELECT name FROM glpi_plugin_cartosi_cartosis");
      $array = array();
      foreach($req as $row) {
         $array[] = $row['name'];
      }
      $req = $DB->query("SELECT * FROM glpi_plugin_cartosi_cartosis");
      
      $description = array();
      $domain = array();
      $leader = array();
      $check = array();
      foreach($req as $row) {
         $description[$row["name"]] = $row['description'];
         $domain[$row["name"]] = $row["domain"];
         $leader[$row["name"]] = $row["leader"];
         $check[$row["name"]] = $row["check"];
      }
      $phpdescription = json_encode($description);
      $phparray = json_encode($array);
      echo "<center>";
      echo "<h1>Carto-SI :</h1>";
      echo "<br>";
      echo "<br>";
      echo "<br>";
      echo "<h2>Insérez le nom de l'application</h2>";
      echo '<div class="autocomplete" style="width:300px;">';
      echo '<input id="myInput" type="text" placeholder="Applications">';
      echo "</div>";
      echo "<button onclick='myFunction()'>Submit</button>";
      echo "</center>";
      echo "<br>";
      echo "<br>";
      echo "<br>";
      echo '<label for="Description">Description : </label>';
      echo '<input id="description" size="50">';
      echo '<br>';
      echo '<br>';
      echo '<label for="Description">Domain : </label>';
      echo '<input id="domain" size="50">';
      echo '<br>';
      echo '<br>';
      echo '<label for="Description">Leader : </label>';
      echo '<input id="leader" size="50">';
      echo '<br>';
      echo '<br>';
      echo '<label for="Description">Check : </label>';
      echo '<input id="check"size="50">';
      echo '<br>';
      echo '<br>';
      echo '<div id="div_donnees" style="width: 100px;height:100px;background:yellow;"></div>';
      echo '<button id="bouton" >POST</button>';
      echo "<script type=text/javascript>";
      echo "$('#bouton').click(function() {
         //mettre directement le nom du fichier php
         var url = '/plugins/cartosi/front/ajax_donnees.php';
         $.post(url, function(data){
              $('#data').html(data);
            });
      });";

      echo 'function myFunction() {
         document.getElementById("description").value = document.getElementById("myInput").value;
       }';
      echo "var jsvar = JSON.parse('".$phparray."');";
      echo 'autocomplete(document.getElementById("myInput"), jsvar);';
      echo "</script>";
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {

      if ($item->getType() == 'Ticket') {
         return __('CartoSI');
      }
      return '';
   }

   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {

      if ($item->getType() == 'Ticket') {
         self::showForTicket($item);
      }
      return true;
   }
}