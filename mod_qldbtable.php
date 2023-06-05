<?php
/**
 * @package		mod_qldbtable
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

defined('_JEXEC') or die;
require_once dirname(__FILE__).'/QldbtableHelper.php';
require_once dirname(__FILE__).'/vendor/autoload.php';

/** @var $module  */
/** @var $params  */
$app = Factory::getApplication();
$helper = new QldbtableHelper($module, $params, Factory::getContainer()->get(DatabaseInterface::class));

if (QldbtableHelper::DISPLAY_CARDS === $params->get('display')) {
    $imageColumn = $params->get('cardImageColumn', '');
    $labelColumn = $params->get('cardLabelColumn', '');
    if (empty($imageColumn)) {
        $app->enqueueMessage('MOD_QLDBTABLE_MSG_SET_IMAGECOLUMN');
    }
    if (empty($labelColumn)) {
        $app->enqueueMessage('MOD_QLDBTABLE_MSG_SET_LABELCOLUMN');
    }
}

$columns = $helper->getColumns();
$data = $helper->getData();
if (QldbtableHelper::DISPLAY_TABLE === $params->get('display', QldbtableHelper::DISPLAY_DEFAULT)) {
    $data = $helper->alterData($data, $helper->getColumnType());
} elseif (QldbtableHelper::DISPLAY_CARDS === $params->get('display', QldbtableHelper::DISPLAY_DEFAULT) && (bool)$params->get('cardImageTag', true)) {
    $data = $helper->alterData($data, [$params->get('cardImageColumn') => QldbtableHelper::TYPE_IMAGE]);
}

require JModuleHelper::getLayoutPath('mod_qldbtable', $params->get('layout', 'default'));
