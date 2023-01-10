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

      $firstelt = ($item::getType() == Ticket::class ? 'th' : 'td');

      $out = '<tr><th colspan="' . (isset($options['colspan']) ? $options['colspan'] * 2 : '4') . '">';
      $out .= sprintf(
         __('Start %1$s hook call for %2$s type'),
         'post_item_form',
         $item::getType()
      );
      $out .= '</th></tr>';

      $out .= "<tr><$firstelt>";
      $out .= '<label for="example_post_form_hook">' . __('First post form hook') . '</label>';
      $out .= "</$firstelt><td>";
      $out .= '<input type="text" name="example_post_form_hook" id="example_post_form_hook"/>';
      $out .= "</td><$firstelt>";
      $out .= '<label for="example_post_form_hook2">' . __('Second post form hook') . '</label>';
      $out .= "</$firstelt><td>";
      $out .= '<input type="text" name="example_post_form_hook2" id="example_post_form_hook2"/>';
      $out .= '</td></tr>';

      $out .= '<tr><th colspan="' . (isset($options['colspan']) ? $options['colspan'] * 2 : '4') . '">';
      $out .= sprintf(
         __('End %1$s hook call for %2$s type'),
         'post_item_form',
         $item::getType()
      );
      $out .= '</th></tr>';

      echo $out;
   }
}