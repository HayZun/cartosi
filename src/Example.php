<?php

/**
 * -------------------------------------------------------------------------
 * Example plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Example.
 *
 * Example is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Example is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Example. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2006-2022 by Example plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/example
 * -------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file:
// Purpose of file:
// ----------------------------------------------------------------------

namespace GlpiPlugin\Example;
use CommonDBTM;
use CommonGLPI;

// Class of the defined type
class Example extends CommonDBTM {

   static $tags = '[EXAMPLE_ID]';

   // Should return the localized name of the type
   static function getTypeName($nb = 0) {
      return 'Assets';
   }


   static function canCreate() {

      if (isset($_SESSION["glpi_plugin_example_profile"])) {
         return ($_SESSION["glpi_plugin_example_profile"]['example'] == 'w');
      }
      return false;
   }


   static function canView() {

      if (isset($_SESSION["glpi_plugin_example_profile"])) {
         return ($_SESSION["glpi_plugin_example_profile"]['example'] == 'w'
                 || $_SESSION["glpi_plugin_example_profile"]['example'] == 'r');
      }
      return false;
   }


   /**
    * @see CommonGLPI::getMenuName()
   **/
   static function getMenuName() {
      return __('CartoSI');
   }


   /**
    * @see CommonGLPI::getAdditionalMenuLinks()
   **/
   static function getAdditionalMenuLinks() {
      global $CFG_GLPI;
      $links = [];

      $links['config'] = '/plugins/example/front/config.php';
      return $links;
   }

   function defineTabs($options = []) {

      $ong = [];
      $this->addDefaultFormTab($ong);
      $this->addStandardTab('Link', $ong, $options);

      return $ong;
   }

   function showForm($ID, array $options = []) {
      global $CFG_GLPI;

      $this->initForm($ID, $options);
      $this->showFormHeader($options);

      $req = $DB->query("SELECT * FROM glpi_plugin_example_examples where id=$ID");
      echo $req["domain"]

      echo "<tr class='tab_bg_1'>";

      echo "<td>" . __('ID') . "</td>";
      echo "<td>";
      echo $ID;
      echo "</td>";
      echo "toto";



      $this->showFormButtons($options);

      return true;
   }

   /**
    * Give localized information about 1 task
    *
    * @param $name of the task
    *
    * @return array of strings
    */
   static function cronInfo($name) {

      switch ($name) {
         case 'Sample' :
            return ['description' => __('Cron description for example', 'example'),
                    'parameter'   => __('Cron parameter for example', 'example')];
      }
      return [];
   }

   /**
    * Execute 1 task manage by the plugin
    *
    * @param $task Object of CronTask class for log / stat
    *
    * @return interger
    *    >0 : done
    *    <0 : to be run again (not finished)
    *     0 : nothing to do
    */
   static function cronSample($task) {

      $task->log("Example log message from class");
      $r = mt_rand(0, $task->fields['param']);
      usleep(1000000+$r*1000);
      $task->setVolume($r);

      return 1;
   }
}