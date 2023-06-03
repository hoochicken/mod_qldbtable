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
        $html = '';
        $html .= '<div class="row col-md-6">';
        $html .= sprintf('<div class="col-md-4"><input name="column_%s" class="form-control" /></div>', $this->name);
        $html .= sprintf('<div class="col-md-4"><select name="type_%s" class="form-control">
            <option value="text">Text</option>
            <option value="image">Image</option>
            </select>
                </div>', $this->name);
        $html .= sprintf('<div class="col-md-4"><input name="value_%s" class="form-control" /></div>', $this->name);
        $html .= '</div>';
        // $html .= '<textarea class="form-control" style="width:400px;height:400px;" name="' . $this->name . '" id="' . $this->id . '">';
        // $html .= $this->value;
        // $html .= '</textarea>';
        return $html;
    }

    public function filter($value, $group = null, Registry $input = null)
    {
        $test = 0;
    }

    public function validate($value, $group = null, Registry $input = null)
    {
        $test = 0;
    }
}