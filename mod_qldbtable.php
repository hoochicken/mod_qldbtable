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

try {
    $app = Factory::getApplication();
    $input = Factory::getApplication()->getInput();
    $helper = new QldbtableHelper($module, $params, Factory::getContainer()->get(DatabaseInterface::class));
    $originalUrl = $helper->getOriginalUrl($helper->getCurrentUrl());

    /* initiate mappings of table, cards and entry */
    $entryStructure = $helper->getEntryColumnType();
    $typeMappingEntry = $helper->getEntryColumnType();
    $typeMappingTable = $helper->getColumnType();
    $typeMappingCards = [
        $params->get('cardImageColumn', '') => QldbtableHelper::TYPE_IMAGE,
        $params->get('cardLabelColumn', '') => QldbtableHelper::TYPE_TEXT,
    ];
    $typeMapping = $params->get('display', QldbtableHelper::DISPLAY_TABLE) === QldbtableHelper::DISPLAY_CARDS
        ? $typeMappingCards
        : $typeMappingTable;

    /* get data of single entry, if needed */
    $displayEntry = $helper->checkDisplayEntry($input);
    if ($displayEntry) {
        $ident = $input->getInt(QldbtableHelper::GETPARAM_ENTRYID, 0);
        $entry = $helper->getEntry($ident);
        $entry = $helper->setImageDefault($entry, $typeMappingEntry, $params->get('entry_image_default', ''));
        $entry = $helper->addImage($entry, $typeMappingEntry, $params->get('entryImageTag', ''));
    }

    /* get data image for cards */
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

    /* get data of rows */
    $columns = $helper->getColumnLabels();
    $data = $helper->getData();
    $data = $helper->alterData($data, $typeMapping);
    $data = $helper->addLink($data, $params->get('linkText', 'Link'), $module->id, $params->get('linkIdent', 'id'));

    /* flatten data as a preparation for displaing it with qldbtable */
    $columnsLinked = explode(',', $params->get('columnsLinked', ''));
    array_walk($columnsLinked, function(&$item) {$item = trim($item);});
    $dataFlattened = $helper->flattenData($data, $typeMapping, (bool)$params->get('imageTag', false), $columnsLinked);

    /* finally display */
    require JModuleHelper::getLayoutPath('mod_qldbtable', $params->get('layout', 'default'));
} catch (Exception $e) {
    $app->enqueueMessage(implode('<br >', [$e->getMessage(), $e->getFile(), $e->getLine()]));
}
