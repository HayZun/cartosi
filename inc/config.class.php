<?php

/*
   ------------------------------------------------------------------------
   Barcode
   Copyright (C) 2009-2016 by the Barcode plugin Development Team.
   https://forge.indepnet.net/projects/barscode
   ------------------------------------------------------------------------
   LICENSE
   This file is part of barcode plugin project.
   Plugin Barcode is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.
   Plugin Barcode is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with Plugin Barcode. If not, see <http://www.gnu.org/licenses/>.
   ------------------------------------------------------------------------
   @package   Plugin Barcode
   @author    David Durieux
   @co-author
   @copyright Copyright (c) 2009-2016 Barcode plugin Development team
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @link      https://forge.indepnet.net/projects/barscode
   @since     2009
   ------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class cartosiConfig extends CommonDBTM {

   static $rightname = 'plugin_cartosi_config'

   function showForm($ID, array $options = []) {
    echo "test"

    Session::checkRight("config", UPDATE);

    // To be available when plugin in not activated
    Plugin::load('cartosi');

    Html::header("TITRE", $_SERVER['PHP_SELF'], "config", "plugins");

    echo '<form method="post" action="formulaire.php">';

    echo 'Token API : ';
    echo '<input type="nombre" id="token" name="token" placeholder="Entrer le token de API carto-si"  size="50">';

    echo '<td style="width: 200px">' . __('     Tenant :       ') .'</td>';
    echo '<input type="nombre" id="tenant" name="tenant" placeholder="Entrer votre tenant carto-si" size="50">';
    echo "</tr>";

    echo "<br>";
    echo "<br>";

    echo Html::submit(_sx('button', 'Sauvegarder'), ['name'  => 'add','class' => 'btn btn-primary']);
    HTML::closeForm();
    Html::footer();
   }
}