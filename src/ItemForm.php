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

namespace GlpiPlugin\Cartosi;
use Html;
use Ticket;

/**
 * Summary of GlpiPlugin\Example\ItemForm
 * Example of *_item_form implementation
 * @see http://glpi-developer-documentation.rtfd.io/en/master/plugins/hooks.html#items-display-related
 * */
class ItemForm {

   /**
    * Display contents at the begining of item forms.
    *
    * @param array $params Array with "item" and "options" keys
    *
    * @return void
    */
   static public function postItemForm($params) {
      $item = $params['item'];
      $options = $params['options'];
      switch ($item::getType()) {
         case "Ticket":
            foreach($item as $key => $element) {
               foreach($element as $keys => $elements) {
                  if($keys == "id") {
                     $id = $elements;
                  }
               }
            }
            echo '<form method="post" action="formulaire.php">';
            echo "<center>";
            echo '<h2>Carto-SI : </h2>';
            echo "<br>";
            echo '<input type="nombre" id="token" name="token" placeholder="Entrer le token de API carto-si" value="'.$token.'"  size="50">';
            echo "<br>";
            echo Html::submit(_sx('button', 'Sauvegarder'), ['name'  => 'add','class' => 'btn btn-primary']);
            HTML::closeForm();
      }
   }
}