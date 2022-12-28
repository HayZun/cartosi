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

use Glpi\Plugin\Hooks;
use GlpiPlugin\Example\Computer;
use GlpiPlugin\Example\Config;
use GlpiPlugin\Example\Dropdown;
use GlpiPlugin\Example\DeviceCamera;
use GlpiPlugin\Example\Example;
use GlpiPlugin\Example\ItemForm;
use GlpiPlugin\Example\RuleTestCollection;
use GlpiPlugin\Example\Showtabitem;

define('PLUGIN_EXAMPLE_VERSION', '0.0.1');

// Minimal GLPI version, inclusive
define('PLUGIN_EXAMPLE_MIN_GLPI', '10.0.0');
// Maximum GLPI version, exclusive
define('PLUGIN_EXAMPLE_MAX_GLPI', '10.0.99');

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_example() {
   global $PLUGIN_HOOKS,$CFG_GLPI;

   // Params : plugin name - string type - ID - Array of attributes
   // No specific information passed so not needed
   //Plugin::registerClass(Example::getType(),
   //                      array('classname'              => Example::class,
   //                        ));

   $types = ['Central', 'Computer', 'ComputerDisk', 'Notification', 'Phone',
             'Preference', 'Profile', 'Supplier'];
   Plugin::registerClass(Example::class,
                         ['notificationtemplates_types' => true,
                          'addtabon'                    => $types,
                          'link_types' => true]);

   Plugin::registerClass(RuleTestCollection::class,
                         ['rulecollections_types' => true]);

   if (version_compare(GLPI_VERSION, '9.1', 'ge')) {
      if (class_exists(Example::class)) {
         Link::registerTag(Example::$tags);
      }
   }
   // Display a menu entry ?
   $_SESSION["glpi_plugin_example_profile"]['example'] = 'w';
   if (isset($_SESSION["glpi_plugin_example_profile"])) { // Right set in change_profile hook
      $PLUGIN_HOOKS['menu_toadd']['example'] = ['plugins' => Example::class,
                                                'tools'   => Example::class];
   }

   // Config page
   if (Session::haveRight('config', UPDATE)) {
      $PLUGIN_HOOKS['config_page']['example'] = 'front/config.php';
   }

   // Item action event // See define.php for defined ITEM_TYPE

   // function to populate planning
   // No more used since GLPI 0.84
   // $PLUGIN_HOOKS['planning_populate']['example'] = 'plugin_planning_populate_example';
   // Use instead : add class to planning types and define populatePlanning in class
   $CFG_GLPI['planning_types'][] = Example::class;

   //function to display planning items
   // No more used sinc GLPi 0.84
   // $PLUGIN_HOOKS['display_planning']['example'] = 'plugin_display_planning_example';
   // Use instead : displayPlanningItem of the specific itemtype

   // Massive Action definition
   $PLUGIN_HOOKS['use_massive_action']['example'] = 1;

   $PLUGIN_HOOKS['assign_to_ticket']['example'] = 1;

   // Add specific files to add to the header : javascript or css
   $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['example'] = 'example.js';
   $PLUGIN_HOOKS[Hooks::ADD_CSS]['example']        = 'example.css';

   // request more attributes from ldap
   //$PLUGIN_HOOKS['retrieve_more_field_from_ldap']['example']="plugin_retrieve_more_field_from_ldap_example";

   // Retrieve others datas from LDAP
   //$PLUGIN_HOOKS['retrieve_more_data_from_ldap']['example']="plugin_retrieve_more_data_from_ldap_example";

   // Reports
   $PLUGIN_HOOKS['reports']['example'] = ['report.php'       => 'New Report',
                                          'report.php?other' => 'New Report 2'];

   // Stats
   $PLUGIN_HOOKS['stats']['example'] = ['stat.php'       => 'New stat',
                                        'stat.php?other' => 'New stats 2',];

   $PLUGIN_HOOKS[Hooks::POST_INIT]['example'] = 'plugin_example_postinit';

   $PLUGIN_HOOKS['status']['example'] = 'plugin_example_Status';

   // CSRF compliance : All actions must be done via POST and forms closed by Html::closeForm();
   $PLUGIN_HOOKS[Hooks::CSRF_COMPLIANT]['example'] = true;

   $PLUGIN_HOOKS[Hooks::DISPLAY_CENTRAL]['example'] = "plugin_example_display_central";
   $PLUGIN_HOOKS[Hooks::DISPLAY_LOGIN]['example'] = "plugin_example_display_login";
   $PLUGIN_HOOKS[Hooks::INFOCOM]['example'] = "plugin_example_infocom_hook";

   // pre_show and post_show for tabs and items,
   // see GlpiPlugin\Example\Showtabitem class for implementation explanations
   $PLUGIN_HOOKS[Hooks::PRE_SHOW_TAB]['example']     = [Showtabitem::class, 'pre_show_tab'];
   $PLUGIN_HOOKS[Hooks::POST_SHOW_TAB]['example']    = [Showtabitem::class, 'post_show_tab'];
   $PLUGIN_HOOKS[Hooks::PRE_SHOW_ITEM]['example']    = [Showtabitem::class, 'pre_show_item'];
   $PLUGIN_HOOKS[Hooks::POST_SHOW_ITEM]['example']   = [Showtabitem::class, 'post_show_item'];

   $PLUGIN_HOOKS[Hooks::PRE_ITEM_FORM]['example']    = [ItemForm::class, 'preItemForm'];
   $PLUGIN_HOOKS[Hooks::POST_ITEM_FORM]['example']   = [ItemForm::class, 'postItemForm'];

   // Add new actions to timeline
   $PLUGIN_HOOKS[Hooks::TIMELINE_ACTIONS]['example'] = [
      ItemForm::class, 'timelineActions'
   ];

   // declare this plugin as an import plugin for Computer itemtype
   $PLUGIN_HOOKS['import_item']['example'] = [Computer::class => ['Plugin']];

   // add additional informations on Computer::showForm
   $PLUGIN_HOOKS[Hooks::AUTOINVENTORY_INFORMATION]['example'] =  [
      Computer::class =>  [Computer::class, 'showInfo']
   ];

    $PLUGIN_HOOKS[Hooks::FILTER_ACTORS]['example'] = "plugin_example_filter_actors";

   // add new cards to dashboard grid
   $PLUGIN_HOOKS['dashboard_types']['example'] = [Example::class, 'dashboardTypes'];
   $PLUGIN_HOOKS['dashboard_cards']['example'] = [Example::class, 'dashboardCards'];
}


/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_example() {
   return [
      'name'           => 'Plugin Example',
      'version'        => PLUGIN_EXAMPLE_VERSION,
      'author'         => 'Example plugin team',
      'license'        => 'GPLv2+',
      'homepage'       => 'https://github.com/pluginsGLPI/example',
      'requirements'   => [
         'glpi' => [
            'min' => PLUGIN_EXAMPLE_MIN_GLPI,
            'max' => PLUGIN_EXAMPLE_MAX_GLPI,
         ]
      ]
   ];
}


/**
 * Check pre-requisites before install
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_example_check_prerequisites() {
   if (false) {
      return false;
   }
   return true;
}

/**
 * Check configuration process
 *
 * @param boolean $verbose Whether to display message on failure. Defaults to false
 *
 * @return boolean
 */
function plugin_example_check_config($verbose = false) {
   if (true) { // Your configuration check
      return true;
   }

   if ($verbose) {
      echo __('Installed / not configured', 'example');
   }
   return false;
}
