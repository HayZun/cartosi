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

use GlpiPlugin\Example\Example;

// See also GlpiPlugin\Example\Example::getSpecificValueToDisplay()
function plugin_example_giveItem($type, $ID, $data, $num) {
   $searchopt = &Search::getOptions($type);
   $table = $searchopt[$ID]["table"];
   $field = $searchopt[$ID]["field"];

   switch ($table.'.'.$field) {
      case "glpi_plugin_example_examples.name" :
         $out = "<a href='".Toolbox::getItemTypeFormURL(Example::class)."?id=".$data['id']."'>";
         $out .= $data[$num][0]['name'];
         if ($_SESSION["glpiis_ids_visible"] || empty($data[$num][0]['name'])) {
            $out .= " (".$data["id"].")";
         }
         $out .= "</a>";
         return $out;
   }
   return "";
}

function plugin_example_displayConfigItem($type, $ID, $data, $num) {
   $searchopt = &Search::getOptions($type);
   $table     = $searchopt[$ID]["table"];
   $field     = $searchopt[$ID]["field"];

   // Example of specific style options
   // No need of the function if you do not have specific cases
   switch ($table.'.'.$field) {
      case "glpi_plugin_example_examples.name" :
         return " style=\"background-color:#DDDDDD;\" ";
   }
   return "";
}

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_example_install() {
   global $DB;

   ProfileRight::addProfileRights(['example:read']);

   $default_charset = DBConnection::getDefaultCharset();
   $default_collation = DBConnection::getDefaultCollation();
   $default_key_sign = DBConnection::getDefaultPrimaryKeySignOption();

   if (!$DB->tableExists("glpi_plugin_example_examples")) {
      $query = "CREATE TABLE `glpi_plugin_example_examples` (
                  `id` int {$default_key_sign} NOT NULL auto_increment,
                  `name` varchar(255) default NULL,
                  `serial` varchar(255) NOT NULL,
                  `plugin_example_dropdowns_id` int NOT NULL default '0',
                  `is_deleted` tinyint NOT NULL default '0',
                  `is_template` tinyint NOT NULL default '0',
                  `template_name` varchar(255) default NULL,
                PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

      $DB->query($query) or die("error creating glpi_plugin_example_examples ". $DB->error());

      $query = "INSERT INTO `glpi_plugin_example_examples`
                       (`id`, `name`, `serial`, `plugin_example_dropdowns_id`, `is_deleted`,
                        `is_template`, `template_name`)
                VALUES (1, 'example 1', 'serial 1', 1, 0, 0, NULL),
                       (2, 'example 2', 'serial 2', 2, 0, 0, NULL),
                       (3, 'example 3', 'serial 3', 1, 0, 0, NULL)";
      $DB->query($query) or die("error populate glpi_plugin_example ". $DB->error());
   }

   if (!$DB->tableExists("glpi_plugin_cartosi_credentials")) {
      // create tab glpi_plugin_cartosi_credentials
      $query = "CREATE TABLE `glpi_plugin_cartosi_credentials` (
                  `id` int {$default_key_sign} NOT NULL auto_increment,
                  `token` TEXT NOT NULL,
                  `tenant` INT NOT NULL,
                PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

      $DB->query($query) or die("error creating glpi_plugin_example_examples ". $DB->error());
   }

   if (!$DB->tableExists("glpi_plugin_cartosi_app")) {
      // create tab glpi_plugin_cartosi_credentials
      $query = "CREATE TABLE `glpi_plugin_cartosi_app` (
                  `id` int {$default_key_sign} NOT NULL auto_increment,
                  `name` TEXT NOT NULL,
                  `domain` TEXT NOT NULL,
                  `leader` TEXT NOT NULL,
                  `check` date,
                PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

      $DB->query($query) or die("error glpi_plugin_cartosi_app ". $DB->error());
   }
   
   // To be called for each task the plugin manage
   // task in class
   CronTask::Register(Example::class, 'Sample', DAY_TIMESTAMP, ['param' => 50]);
   return true;
}


/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_example_uninstall() {
   global $DB;

   $config = new Config();
   $config->deleteConfigurationValues('plugin:Example', ['configuration' => false]);

   ProfileRight::deleteProfileRights(['example:read']);

   $notif = new Notification();
   $options = ['itemtype' => 'Ticket',
               'event'    => 'plugin_example',
               'FIELDS'   => 'id'];
   foreach ($DB->request('glpi_notifications', $options) as $data) {
      $notif->delete($data);
   }
   // Old version tables
   if ($DB->tableExists("glpi_dropdown_plugin_example")) {
      $query = "DROP TABLE `glpi_dropdown_plugin_example`";
      $DB->query($query) or die("error deleting glpi_dropdown_plugin_example");
   }
   if ($DB->tableExists("glpi_plugin_example")) {
      $query = "DROP TABLE `glpi_plugin_example`";
      $DB->query($query) or die("error deleting glpi_plugin_example");
   }
   // Current version tables
   if ($DB->tableExists("glpi_plugin_example_example")) {
      $query = "DROP TABLE `glpi_plugin_example_example`";
      $DB->query($query) or die("error deleting glpi_plugin_example_example");
   }
   if ($DB->tableExists("glpi_plugin_example_dropdowns")) {
      $query = "DROP TABLE `glpi_plugin_example_dropdowns`;";
      $DB->query($query) or die("error deleting glpi_plugin_example_dropdowns");
   }
   if ($DB->tableExists("glpi_plugin_example_devicecameras")) {
      $query = "DROP TABLE `glpi_plugin_example_devicecameras`;";
      $DB->query($query) or die("error deleting glpi_plugin_example_devicecameras");
   }
   if ($DB->tableExists("glpi_plugin_example_items_devicecameras")) {
      $query = "DROP TABLE `glpi_plugin_example_items_devicecameras`;";
      $DB->query($query) or die("error deleting glpi_plugin_example_items_devicecameras");
   }
   return true;
}


function plugin_example_AssignToTicket($types) {
   $types[Example::class] = "Example";
   return $types;
}


function plugin_example_get_events(NotificationTargetTicket $target) {
   $target->events['plugin_example'] = __("Example event", 'example');
}


function plugin_example_get_datas(NotificationTargetTicket $target) {
   $target->data['##ticket.example##'] = __("Example datas", 'example');
}


function plugin_example_postinit() {
   global $CFG_GLPI;

   // All plugins are initialized, so all types are registered
   //foreach (Infocom::getItemtypesThatCanHave() as $type) {
      // do something
   //}
}


/**
 * Hook to add more data from ldap
 * fields from plugin_retrieve_more_field_from_ldap_example
 *
 * @param $datas   array
 *
 * @return un tableau
 **/
function plugin_retrieve_more_data_from_ldap_example(array $datas) {
   return $datas;
}


/**
 * Hook to add more fields from LDAP
 *
 * @param $fields   array
 *
 * @return un tableau
 **/
function plugin_retrieve_more_field_from_ldap_example($fields) {
   return $fields;
}

// Check to add to status page
function plugin_example_Status($param) {
   // Do checks (no check for example)
   $ok = true;
   echo "example plugin: example";
   if ($ok) {
      echo "_OK";
   } else {
      echo "_PROBLEM";
      // Only set ok to false if trouble (global status)
      $param['ok'] = false;
   }
   echo "\n";
   return $param;
}

function plugin_example_display_central() {
   echo "<tr><th colspan='2'>";
   echo "<div style='text-align:center; font-size:2em'>";
   echo __("Plugin example displays on central page", "example");
   echo "</div>";
   echo "</th></tr>";
}

function plugin_example_display_login() {
   echo "<div style='text-align:center; font-size:2em'>";
   echo __("Plugin example displays on login page", "example");
   echo "</div>";
}

function plugin_example_infocom_hook($params) {
   echo "<tr><th colspan='4'>";
   echo __("Plugin example displays on central page", "example");
   echo "</th></tr>";
}

function plugin_example_filter_actors(array $params = []): array {
    $itemtype = $params['params']['itemtype'];
    $items_id = $params['params']['items_id'];

    // remove users_id = 1 for assignee list
    if ($itemtype == 'Ticket' && $params['params']['actortype'] == 'assign') {
        foreach ($params['actors'] as $index => &$actor) {
            if ($actor['type'] == 'user' && $actor['items_id'] == 1) {
                unset($params['actors'][$index]);
            }
        }
    }

    return $params;
}