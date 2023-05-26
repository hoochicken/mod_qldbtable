<?php
/**
 * @package        mod_qlform
 * @copyright    Copyright (C) 2023 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;

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
        $options = [];
        // qldbtablecolumn
        $valueRaw = empty($this->value) ? '{}' : $this->value;
        $data = json_decode($valueRaw);

        $type = isset($data->type) ? $data->type : 'text';
        $column = isset($data->column) ? $data->column : 'column';
        $value = isset($data->value) ? $data->value : 'value';

        $id = 1;

        $column = $this->name . '_column';
        $value = $this->name . '_value';
        $html = '';
        $html .= sprintf('<input name="column_%s" id="" />', $this->name, $id);
        $html .= '<textarea style="width:400px;height:400px;" name="' . $this->name . '" id="' . $this->id . '">';
        $html .= $this->value;
        $html .= '</textarea>';
        return $html;
    }
}