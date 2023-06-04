<?php
/**
 * @package		mod_qldbtable
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

use Hoochicken\Datagrid\Datagrid;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

// no direct access
defined('_JEXEC') or die;
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerStyle('qldbtable', 'mod_qldbtable/styles.css');
$wa->useStyle('qldbtable');

/* @var stdClass $module */
/* @var \Joomla\Registry\Registry $params */
/* @var array $columns */
/* @var array $data */
?>

<div class="qldbtable" id="module<?php echo $module->id ?>">
    <?php
    if ($params->get('display', QldbtableHelper::DISPLAY_DEFAULT) === QldbtableHelper::DISPLAY_CARDS) {
        require ModuleHelper::getLayoutPath('mod_qldbtable', 'default_cards');
    } else {
        require ModuleHelper::getLayoutPath('mod_qldbtable', 'default_table');
    }
    ?>
</div>