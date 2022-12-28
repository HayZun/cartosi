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

// Non menu entry case
//header("Location:../../central.php");

global $DB;

// Entry menu case
include ("../../../inc/includes.php");

Session::checkRight("config", UPDATE);

// To be available when plugin in not activated
Plugin::load('plugincartosi');

Html::header("TITRE", $_SERVER['PHP_SELF'], "config", "plugins");

//datas from POST
$token = $_POST['token'];
$tenant = intval( $_POST['tenant']);

//si c'est à 0, on register la data
//si c'est à 1, on suppr la data et on register la data
//remove datas
$req = $DB->query('SELECT COUNT(*) FROM glpi_plugin_cartosi_credentials');
foreach($req as $row) {
     $count = $row["COUNT(*)"];
}
if (0 == $count) {
   $req = $DB->query("INSERT INTO `glpi_plugin_cartosi_credentials` (`id`, `token`, `tenant`) VALUES (1, '".$token."', ".$tenant.")");
}

if (1 == $count) {
   //retrieves data from table glpi_plugin_cartosi_credentials
   $req = $DB->query('SELECT * FROM glpi_plugin_cartosi_credentials');
   foreach($req as $row) {
     $tokenglpi = $row["token"];
     $tenantglpi = $row["tenant"];
   }
   if( ($tokenglpi != $token) || ($tenantglpi != $tenant) ) {
 	//delete datas from database
   	$req = $DB->query("DELETE FROM glpi_plugin_cartosi_credentials WHERE id=1");

	//insert datas
        $req = $DB->query("INSERT INTO `glpi_plugin_cartosi_credentials` (`id`, `token`, `tenant`) VALUES (1, '".$token."', ".$tenant.")");
   }
}

echo '<form method="post" action="formulaire.php">';
echo 'Token API : ';
echo '<input type="nombre" id="token" name="token" value="'.$token.'" size="50">';

echo '<td style="width: 200px">' . __('     Tenant :       ') .'</td>';
echo '<input type="nombre" id="tenant" name="tenant" value="'.$tenant.'" size="50">';
echo "</tr>";

echo "<br>";
echo "<br>";

//test connection cartosi
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
//echo $response;
curl_close($curl);
echo "<br><br>Connexion between GLPI and Carto SI ";
if (strlen($response) == 71) {
	echo "failed";
   echo "<br>";
   echo Html::submit(_sx('button', 'Sauvegarder'), ['name'  => 'add','class' => 'btn btn-primary']);
} else {
	echo "sucess\n";
	//retrieve application result
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
    echo "<br>";
    $data = json_decode($response, true);
    //retrievas datas (name,description,domain,leader and check)
    foreach( $data as $key => $value )
    {
       foreach( $value as $valeur => $value1 ) {
          if (strpos($valeur, "label") !== false) {
             $name = $value1;
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
         $quotient = $value1 / 1000;    
         $datecheck = date('Y-m-d', $quotient);
         }
    }

   $bool = true;
   $req = $DB->query("SELECT `Name` FROM glpi_plugin_cartosi_app");
   foreach($req as $row) {
      //if name_app == glpiname, no insert data
      if ($row["Name"] == $name) {
         $bool = false;
      }
   }
   if($bool == true) {
      $req = $DB->query("INSERT INTO `glpi_plugin_cartosi_app` (`name`,`domain`,`leader`,`check`) VALUES ('$name','$domain','$teamleader','$datecheck')");
      }
   }
   
   $req = $DB->query('SELECT COUNT(*) FROM glpi_plugin_cartosi_app');
   foreach($req as $row) {
      $count = $row["COUNT(*)"] + 1;
   }
   echo "$count imported tables";
}


HTML::closeForm();
Html::footer();