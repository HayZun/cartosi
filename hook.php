<?php

/**
 * -------------------------------------------------------------------------
 * cartosi plugin for GLPI
 * Copyright (C) 2022 by the cartosi Development Team.
 * -------------------------------------------------------------------------
 *
 * MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * --------------------------------------------------------------------------
 */

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

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_cartosi_install() {
   global $DB;

   $default_charset = DBConnection::getDefaultCharset();
   $default_collation = DBConnection::getDefaultCollation();
   $default_key_sign = DBConnection::getDefaultPrimaryKeySignOption();

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

   // CronTask::Register('cartosi', 'SynchroGlpiCartoSi', DAY_TIMESTAMP, ['param' => 50]);

   return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_cartosi_uninstall()
{
    global $DB;

     // Current version tables
   if ($DB->tableExists("glpi_plugin_example_examples")) {
      $query = "DROP TABLE `glpi_plugin_example_examples`";
      $DB->query($query) or die("error deleting glpi_plugin_example_examples");
   }

    // Current version tables
   if ($DB->tableExists("glpi_plugin_cartosi_credentials")) {
    $query = "DROP TABLE `glpi_plugin_cartosi_credentials`";
    $DB->query($query) or die("error deleting glpi_plugin_cartosi_credentials");
    }

   if ($DB->tableExists("glpi_plugin_cartosi_app")) {
      $query = "DROP TABLE `glpi_plugin_cartosi_app`";
      $DB->query($query) or die("error deleting glpi_plugin_cartosi_app");
   }
   
    return true;
}