<?php
/**
 * @package        mod_qlform
 * @copyright    Copyright (C) 2023 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;
jimport('joomla.html.html');
//import the necessary class definition for formfield
jimport('joomla.form.formfield');

class JFormFieldQldbtableColumn extends JFormField
{
    /**
     * The form field type.
     *
     * @var  string
     * @since 1.6
     */
    protected $type = 'qldbtablecolumn'; //the form field type see the name is the same

    /**
     * Method to retrieve the lists that resides in your application using the API.
     *
     * @return array The field option objects.
     * @since 1.6
     */
    protected function getInput()
    {
        $type_column = 'column_' . $this->id;
        $type_label = 'label_' . $this->id;
        $type_type = 'type_' . $this->id;

        $value = explode(';', $this->value);

        $html = '';
        $html .= '<div class="row col-md-6">';
        $html .= sprintf('<div class="col-md-4"><input id="id_%s" name="%s" class="form-control class_%s" value="%s" /></div>', $type_column, $type_column, $this->id, $value[0]);
        $html .= sprintf('<div class="col-md-4"><input id="id_%s" name="%s" class="form-control class_%s" value="%s" /></div>', $type_label, $type_label, $this->id, $value[1]);
        $html .= sprintf('<div class="col-md-4"><select id="id_%s" name="%s" class="form-control class_%s" value="%s" >
            <option value="text">Text</option>
            <option value="image">Image</option>
            </select>
                </div>', $type_type, $type_type, $this->id, $value[2]);
        $html .= sprintf('<input id="%s" name="%s" class="form-control" type="hidden" value="%s"/>', $this->id, $this->name, $this->value);
        $html .= '</div>';

        $html .= sprintf('
        <script>$(".class_%s").change(function() {
          let value = $("#id_%s").val() + ";" + $("#id_%s").val() + ";" + $("#id_%s").val();
          $("#%s").val(value);
        })</script>
        
        ', $this->id, $type_column, $type_label, $type_type, $this->id);

        return $html;
    }
}