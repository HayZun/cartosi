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

use GlpiPlugin\Cartosi\Cartosi;

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_cartosi_install() {
   global $DB;

   ProfileRight::addProfileRights(['example:read']);

   $default_charset = DBConnection::getDefaultCharset();
   $default_collation = DBConnection::getDefaultCollation();
   $default_key_sign = DBConnection::getDefaultPrimaryKeySignOption();

   if (!$DB->tableExists("glpi_plugin_cartosi_cartosis")) {
      $query = "CREATE TABLE `glpi_plugin_cartosi_cartosis` (
                  `id` int {$default_key_sign} NOT NULL auto_increment,
                  `name` TEXT NOT NULL,
                  `description` TEXT NOT NULL,
                  `domain` TEXT NOT NULL,
                  `leader` TEXT NOT NULL,
                  `check` date,
                PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

      $DB->query($query) or die("error creating glpi_plugin_cartosi_cartosis ". $DB->error());
   }

   if (!$DB->tableExists("glpi_plugin_cartosi_credentials")) {
      // create tab glpi_plugin_cartosi_credentials
      $query = "CREATE TABLE `glpi_plugin_cartosi_credentials` (
                  `id` int {$default_key_sign} NOT NULL auto_increment,
                  `token` TEXT NOT NULL,
                  `tenant` INT NOT NULL,
                PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

      $DB->query($query) or die("error glpi_plugin_cartosi_credentials ". $DB->error());
   }

   // To be called for each task the plugin manage
   // task in class
   CronTask::Register(Cartosi::class, 'CartoSI', DAY_TIMESTAMP,
         array(
            'comment'   => '',
            'mode'      => Crontask::MODE_EXTERNAL
         ));
   return true;
}


/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_cartosi_uninstall() {
   global $DB;

   // Current version tables
   if ($DB->tableExists("creating glpi_plugin_cartosi_cartosis")) {
      $query = "DROP TABLE `creating glpi_plugin_cartosi_cartosis`";
      $DB->query($query) or die("error deleting creating glpi_plugin_cartosi_cartosis");
   }
   
   if ($DB->tableExists("glpi_plugin_cartosi_credentials")) {
      $query = "DROP TABLE `glpi_plugin_cartosi_credentials`";
      $DB->query($query) or die("error deleting glpi_plugin_cartosi_credentials");
   }

   return true;
}

function plugin_cartosi_display_central() {
   echo "<tr><th colspan='2'>";
   echo "<div style='text-align:center; font-size:2em'>";
   echo __("Plugin example displays on central page", "example");
   echo "</div>";
   echo "</th></tr>";
}