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
/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('qldbtable', 'mod_qldbtable/styles.css');

/* @var stdClass $module */
/* @var \Joomla\Registry\Registry $params */
/* @var array $columns */
/* @var array $data */
/* @var array $errores */
/* @var bool $displayEntry */
/* @var bool $displayCharts */
/* @var bool $displayList */
?>

<div class="qldbtable" id="module<?php echo $module->id ?>">
    <?php
    if (0 < count($errores->getErrors())) {
        $errores = array_column($errores->getErrors(), QldbtableError::ATTR_MESSAGE);
        echo sprintf('<div class="alert alert-info">%s</div>', implode('<br />', $errores));
    }
    if ($displayEntry) require ModuleHelper::getLayoutPath('mod_qldbtable', 'default_entry');
    if ($displayCharts) require ModuleHelper::getLayoutPath('mod_qldbtable', 'default_charts');
    if ($displayList && $params->get('display', QldbtableHelper::DISPLAY_DEFAULT) === QldbtableHelper::DISPLAY_CARDS) {
        require ModuleHelper::getLayoutPath('mod_qldbtable', 'default_cards');
    } elseif ($displayList) {
        require ModuleHelper::getLayoutPath('mod_qldbtable', 'default_table');
    }
    ?>
</div>