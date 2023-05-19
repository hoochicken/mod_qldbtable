<?php
/**
 * @package		mod_qldbtable
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
require_once dirname(__FILE__).'/helper.php';

/** @var $module  */
/** @var $params  */
$obj_helper = new modQldbtableHelper($module, $params);

require JModuleHelper::getLayoutPath('mod_qldbtable', $params->get('layout', 'default'));
