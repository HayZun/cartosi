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
echo "<br><br>";
if (strlen($response) == 71) {
  echo "<center><h1><FONT COLOR=red>Connexion between GLPI and Carto SI failed</h1>";
  echo "<br>";
  echo Html::submit(_sx('button', 'Sauvegarder'), ['name'  => 'add','class' => 'btn btn-primary']);
  echo "</center>";
} else {
	echo "<center><h1><FONT COLOR=green>Connexion between GLPI and Carto SI sucess</h1></center>";
  echo "<br>";
}
HTML::closeForm();
Html::footer();