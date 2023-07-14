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

/** @var stdClass $module */
/** @var \Joomla\Registry\Registry $params */
$app = Factory::getApplication();
$helper = new QldbtableHelper($module, $params, Factory::getContainer()->get(DatabaseInterface::class));
$input = Factory::getApplication()->getInput();

$displayEntry = $helper->checkDisplayEntry($input);

if ($displayEntry) {
    $ident = $input->getInt(QldbtableHelper::GETPARAM_ENTRYID, 0);
    $entry = $helper->getEntry($ident);
}

if (QldbtableHelper::DISPLAY_CARDS === $params->get('display')) {
    $imageColumn = $params->get('cardImageColumn', '');
    $labelColumn = $params->get('cardLabelColumn', '');
    $cardCssClass = $params->get('cardCssClass', 'col-md-2');
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
    $data = $helper->alterData($data, [$params->get('cardImageColumn', '') => QldbtableHelper::TYPE_IMAGE], trim($params->get('cardImageDefault', '')));
}
$data = $helper->addLink($data, $params->get('linkText', 'Link'), $module->id, $params->get('linkIdent', 'id'));

require JModuleHelper::getLayoutPath('mod_qldbtable', $params->get('layout', 'default'));
