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
use Computer;
use Html;
use Log;
use MassiveAction;
use Session;

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
      return __('Example plugin');
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

   /**
    * @since version 0.85
    *
    * @see CommonDBTM::processMassiveActionsForOneItemtype()
   **/
   static function processMassiveActionsForOneItemtype(MassiveAction $ma, CommonDBTM $item,
                                                       array $ids) {
      global $DB;

      switch ($ma->getAction()) {
         case 'DoIt' :
            if ($item->getType() == 'Computer') {
               Session::addMessageAfterRedirect(__("Right it is the type I want...", 'example'));
               Session::addMessageAfterRedirect(__('Write in item history', 'example'));
               $changes = [0, 'old value', 'new value'];
               foreach ($ids as $id) {
                  if ($item->getFromDB($id)) {
                     Session::addMessageAfterRedirect("- ".$item->getField("name"));
                     Log::history($id, 'Computer', $changes, Example::class,
                                  Log::HISTORY_PLUGIN);
                     $ma->itemDone($item->getType(), $id, MassiveAction::ACTION_OK);
                  } else {
                     // Example of ko count
                     $ma->itemDone($item->getType(), $id, MassiveAction::ACTION_KO);
                  }
               }
            } else {
               // When nothing is possible ...
               $ma->itemDone($item->getType(), $ids, MassiveAction::ACTION_KO);
            }
            return;

         case 'do_nothing' :
            If ($item->getType() == Example::class) {
               Session::addMessageAfterRedirect(__("Right it is the type I want...", 'example'));
               Session::addMessageAfterRedirect(__("But... I say I will do nothing for:",
                                                   'example'));
               foreach ($ids as $id) {
                  if ($item->getFromDB($id)) {
                     Session::addMessageAfterRedirect("- ".$item->getField("name"));
                     $ma->itemDone($item->getType(), $id, MassiveAction::ACTION_OK);
                  } else {
                     // Example for noright / Maybe do it with can function is better
                     $ma->itemDone($item->getType(), $id, MassiveAction::ACTION_KO);
                  }
               }
            } else {
               $ma->itemDone($item->getType(), $ids, MassiveAction::ACTION_KO);
            }
            Return;
      }
      parent::processMassiveActionsForOneItemtype($ma, $item, $ids);
   }

   static function generateLinkContents($link, CommonDBTM $item) {

      if (strstr($link, "[EXAMPLE_ID]")) {
         $link = str_replace("[EXAMPLE_ID]", $item->getID(), $link);
         return [$link];
      }

      return parent::generateLinkContents($link, $item);
   }


   static function dashboardTypes() {
      return [
         'example' => [
            'label'    => __("Plugin Example", 'example'),
            'function' => Example::class . "::cardWidget",
            'image'    => "https://via.placeholder.com/100x86?text=example",
         ],
         'example_static' => [
            'label'    => __("Plugin Example (static)", 'example'),
            'function' => Example::class . "::cardWidgetWithoutProvider",
            'image'    => "https://via.placeholder.com/100x86?text=example+static",
         ],
      ];
   }


   static function dashboardCards($cards = []) {
      if (is_null($cards)) {
         $cards = [];
      }
      $new_cards =  [
         'plugin_example_card' => [
            'widgettype'   => ["example"],
            'label'        => __("Plugin Example card"),
            'provider'     => Example::class . "::cardDataProvider",
         ],
         'plugin_example_card_without_provider' => [
            'widgettype'   => ["example_static"],
            'label'        => __("Plugin Example card without provider"),
         ],
         'plugin_example_card_with_core_widget' => [
            'widgettype'   => ["bigNumber"],
            'label'        => __("Plugin Example card with core provider"),
            'provider'     => Example::class . "::cardBigNumberProvider",
         ],
      ];

      return array_merge($cards, $new_cards);
   }


   static function cardWidget(array $params = []) {
      $default = [
         'data'  => [],
         'title' => '',
         // this property is "pretty" mandatory,
         // as it contains the colors selected when adding widget on the grid send
         // without it, your card will be transparent
         'color' => '',
      ];
      $p = array_merge($default, $params);

      // you need to encapsulate your html in div.card to benefit core style
      $html = "<div class='card' style='background-color: {$p["color"]};'>";
      $html.= "<h2>{$p['title']}</h2>";
      $html.= "<ul>";
      foreach ($p['data'] as $line) {
         $html.= "<li>$line</li>";
      }
      $html.= "</ul>";
      $html.= "</div>";

      return $html;
   }

   static function cardDataProvider(array $params = []) {
      $default_params = [
         'label' => null,
         'icon'  => "fas fa-smile-wink",
      ];
      $params = array_merge($default_params, $params);

      return [
         'title' => $params['label'],
         'icon'  => $params['icon'],
         'data'  => [
            'test1',
            'test2',
            'test3',
         ]
      ];
   }

   static function cardWidgetWithoutProvider(array $params = []) {
      $default = [
         // this property is "pretty" mandatory,
         // as it contains the colors selected when adding widget on the grid send
         // without it, your card will be transparent
         'color' => '',
      ];
      $p = array_merge($default, $params);

      // you need to encapsulate your html in div.card to benefit core style
      $html = "<div class='card' style='background-color: {$p["color"]};'>
                  static html (+optional javascript) as card is not matched with a data provider

                  <img src='https://www.linux.org/images/logo.png'>
               </div>";

      return $html;
   }

   static function cardBigNumberProvider(array $params = []) {
      $default_params = [
         'label' => null,
         'icon'  => null,
      ];
      $params = array_merge($default_params, $params);

      return [
         'number' => rand(),
         'url'    => "https://www.linux.org/",
         'label'  => "plugin example - some text",
         'icon'   => "fab fa-linux", // font awesome icon
      ];
   }
}
