<?php
/**
 * @package		mod_qldbtable
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
use Joomla\CMS\Factory;
// no direct access
defined('_JEXEC') or die;
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerStyle('qldbtable', 'mod_qldbtable/styles.css');
$wa->useStyle('qldbtable');

/* @var stdClass $module */
/* @var \Joomla\Registry\Registry $params */
?>

<div class="qldbtable" id="module<?php echo $module->id ?>">
    sdadg jahgd jhagj
</div>