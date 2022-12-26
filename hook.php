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
use GlpiPlugin\cartosi\cartosi;



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

   if (!$DB->tableExists("glpi_plugin_cartosi_cartosi_app")) {
      // create tab glpi_plugin_cartosi_credentials
      $query = "CREATE TABLE `glpi_plugin_cartosi_cartosi_app` (
                  `id` int {$default_key_sign} NOT NULL auto_increment,
                  `name` TEXT NOT NULL,
                  `description` TEXT NOT NULL,
                  `domain` TEXT NOT NULL,
                  `leader` TEXT NOT NULL,
                  `check` date,
                PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

      $DB->query($query) or die("error glpi_plugin_cartosi_cartosi_app ". $DB->error());
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
   if ($DB->tableExists("glpi_plugin_cartosi_credentials")) {
    $query = "DROP TABLE `glpi_plugin_cartosi_credentials`";
    $DB->query($query) or die("error deleting glpi_plugin_cartosi_credentials");
    }

   if ($DB->tableExists("glpi_plugin_cartosi_cartosi_app")) {
      $query = "DROP TABLE `glpi_plugin_cartosi_cartosi_app`";
      $DB->query($query) or die("error deleting glpi_plugin_cartosi_cartosi_app");
   }
   
    return true;
}