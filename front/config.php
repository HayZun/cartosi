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

// Entry menu case
include ("../../../inc/includes.php");

Html::header("TITRE", $_SERVER['PHP_SELF'], "config", "plugins");
$req = $DB->query('SELECT COUNT(*) FROM glpi_plugin_cartosi_credentials');
foreach($req as $row) {
     $count = $row["COUNT(*)"];
}
if (0 == $count) {
    echo '<form method="post" action="formulaire.php">';
    echo 'Token API : ';
    echo '<input type="nombre" id="token" name="token" placeholder="Entrer le token de API carto-si"  size="50">';
    echo '<td style="width: 200px">' . __('     Tenant :       ') .'</td>';
    echo '<input type="nombre" id="tenant" name="tenant" placeholder="Entrer votre tenant carto-si" size="50">';
    echo "</tr>";
    echo "<br>";
    echo "<br>";
}

if (1 == $count) {
    $req = $DB->query('SELECT * FROM glpi_plugin_cartosi_credentials');
   foreach($req as $row) {
     $token = $row["token"];
     $tenant = $row["tenant"];
   }
   echo '<form method="post" action="formulaire.php">';
    echo "<center>";
    echo 'Token API : ';
    echo "<br>";
    echo '<input type="nombre" id="token" name="token" placeholder="Entrer le token de API carto-si" value="'.$token.'"  size="50">';
    echo "<br>";
    echo '<td style="width: 200px">' . __('     Tenant :       ') .'</td>';
    echo "<br>";
    echo '<input type="nombre" id="tenant" name="tenant" placeholder="Entrer votre tenant carto-si" value="'.$tenant.'" size="50">';
    echo "</tr>";
    echo "</center>";
    echo "<br>";
    echo "<br>";
}

echo "<center>";
echo Html::submit(_sx('button', 'Sauvegarder'), ['name'  => 'add','class' => 'btn btn-primary']);
echo "</center>";
Html::closeForm();
Html::footer();