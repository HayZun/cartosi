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

namespace GlpiPlugin\Cartosi;
use CommonDBTM;
use CommonGLPI;

// Class of the defined type
class Cartosi extends CommonDBTM {

   static $tags = '[EXAMPLE_ID]';

   // Should return the localized name of the type
   static function getTypeName($nb = 0) {
      return 'Applications';
   }


   static function canCreate() {

      if (isset($_SESSION["glpi_plugin_cartosi_profile"])) {
         return ($_SESSION["glpi_plugin_cartosi_profile"]['cartosi'] == 'w');
      }
      return false;
   }


   static function canView() {

      if (isset($_SESSION["glpi_plugin_cartosi_profile"])) {
         return ($_SESSION["glpi_plugin_cartosi_profile"]['cartosi'] == 'w'
                 || $_SESSION["glpi_plugin_cartosi_profile"]['cartosi'] == 'r');
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

      $links['config'] = '/plugins/cartosi/front/config.php';
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
      global $DB;

      $this->initForm($ID, $options);
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_1'>";

      echo "<td>" . __('<strong>ID</strong>') . "</td>";
      echo "<td>";
      echo $ID;
      echo "</td>";
      echo "</tr>";

      $req = $DB->query("SELECT * FROM glpi_plugin_cartosi_cartosis where id=$ID");
      foreach($req as $row) {
         $name = $row["name"];
         $domain = $row["domain"];
         $description = $row["description"];
         $leader = $row["leader"];
         $check = $row["check"];
      }

      echo "<tr class='tab_bg_2'>";
      echo "<br>";
      echo "<td>" . __('<strong>Name</strong>') . "</td>";
      echo "<td>";
      echo $name;
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_2'>";
      echo "<br>";
      echo "<td>" . __('<strong>Description</strong>') . "</td>";
      echo "<td>";
      echo $description;
      echo "</td>";
      echo "</tr>";

      
      echo "<tr class='tab_bg_3'>";
      echo "<br>";
      echo "<td>" . __('<strong>App Domain</strong>') . "</td>";
      echo "<td>";
      echo $domain;
      echo "</td>";
      echo "</tr>";


      echo "<tr class='tab_bg_4'>";
      echo "<br>";
      echo "<td>" . __('<strong>Leader</strong>') . "</td>";
      echo "<td>";
      echo $leader;
      echo "</td>";
      echo "</tr>";


      echo "<tr class='tab_bg_5'>";
      echo "<br>";
      echo "<td>" . __('<strong>Last check</strong>') . "</td>";
      echo "<td>";
      echo $check;
      echo "</td>";
      echo "</tr>";


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
         case 'CartoSI' :
            return ['description' => __('Synchronisation application CartoSI à GLPI', 'cartosi')];
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
   static function cronCartoSI($task) {

      global $DB;
      
      $task->log("Initalisation synchro cartoSI");

      //get-VARs
      $req = $DB->query('SELECT COUNT(*) FROM glpi_plugin_cartosi_credentials');
      foreach($req as $row) {
         $count = $row["COUNT(*)"];
      }

      //
      if (1 == $count) {
         $req = $DB->query('SELECT * FROM glpi_plugin_cartosi_credentials');
        foreach($req as $row) {
          $token = $row["token"];
          $tenant = $row["tenant"];
        }
         //test connexion
         $curl = curl_init();
         curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.carto-si.com/api/v2/activity/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
               'Authorization: Bearer {"myTenant":{"id":"'.$tenant.'"},"token":"'.$token.'"}'
            ),
            ));
   
         $response = curl_exec($curl);
         curl_close($curl);
         if (strlen($response) == 71) {
            $task->log("Arrêt de la synchronisation");
            $task->log("Tenant ou token invalide");
         } else {
            $task->log("Token/tenant valide");
            //import appplication from cartoSI to GLPI

            $curl = curl_init();

            curl_setopt_array($curl, array(
               CURLOPT_URL => 'https://app.carto-si.com/api/v2/application',
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'GET',
               CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer {"myTenant":{"id":"'.$tenant.'"},"token":"'.$token.'"}'
            ),
            ));
            $response = curl_exec($curl);

            curl_close($curl);
            $data = json_decode($response, true);
            //retrievas datas (name,description,domain,leader and check)
            $nbapps = 0;
            foreach( $data as $key => $value ) {
               $count = 0;
               foreach( $value as $valeur => $value1 ) {
                  if (strpos($valeur, "id") !== false) {
                     $idapp = $value1;
                  }
                  if (strpos($valeur, "label") !== false) {
                     $name = str_replace("'", " ","$value1");
                  }
                  if (strpos($valeur, "description") !== false) {
                     $description = str_replace("'", " ","$value1");
                  }
                  if (strpos($valeur, "businesses") !== false) {
                     foreach($value1 as $valeur2 => $value2) {
                        foreach($value2 as $valeur3 => $value3) {
                           if (strpos($valeur3, "label") !== false) {
                              $domain = $value3;
                           }
                        }
                     }
                  }
                  if (strpos($valeur, "teamleader") !== false) {
                           foreach($value1 as $valeur2 => $value2) {
                              if (strpos($valeur2, "label") !== false) {
                                    $teamleader = $value2;
                              }
                           }
                        }
                  if (strpos($valeur, "dateMaj") !== false) {
                        $quotient = $value1 / 1000 + 1000;    
                        $datecheck = date('Y-m-d', $quotient);
                        }
               }

               $req = $DB->query("SELECT COUNT(*) FROM glpi_plugin_cartosi_cartosis WHERE id_app='".$idapp."'");
               foreach($req as $row) {
                  $count = $row["COUNT(*)"];
               }

               if (0 == $count) {
                  $task->log("création de la table $name");
                  //application doesn't exist in db of glpi
                  $req = $DB->query("INSERT INTO `glpi_plugin_cartosi_cartosis` (`name`,`id_app`,`description`,`domain`,`leader`,`check`) VALUES ('$name','$idapp','$description','$domain','$teamleader','$datecheck')");
                  $nbapps = $nbapps + 1;
               } else {
                  //application exists
                  $req = $DB->query("UPDATE glpi_plugin_cartosi_cartosis SET name='".$name."',
                                                                             description='".$description."',
                                                                             domain='".$domain."',
                                                                             leader='".$teamleader."',
                                                                             glpi_plugin_cartosi_cartosis.check='".$datecheck."'
                                                                        WHERE id_app='".$idapp."'");
               }
               $name = "";
               $description = "";
               $domain = "";
               $teamleader = "";
               $datecheck = "";

            }  
            if ($nbapps > 0) {
               $task->log("$nbapps applications ajoutées");
            } else {
               $task->log("Pas d'applications ajoutées");
            }
         } 
      } else {
         $task->log("Token/tenant invalide");
      }
      //retrieve business
      $curl = curl_init();
      $task->log(json_encode($idapp));
      curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://app.carto-si.com/api/v2/link/search',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
         "fields":[
         {
            "name":"to",
            "value": "e639db72-3b2a-f40d-9688-e6e4a5f5ae4a"
         },
         {"name":"type",
         "value":"process2application"
         }
         ],
         "pageSize":1000000,
         "pagination":1
      }',
      CURLOPT_HTTPHEADER => array(
         'Authorization: Bearer {"myTenant":{"id":"'.$tenant.'"},"token":"'.$token.'"}',
         'Content-Type: application/json'
      ),
      ));

      $response = curl_exec($curl);
      $task->log($response);
      $task->log(gettype($response));

      curl_close($curl);
      return 1;
   }
}