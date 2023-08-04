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
    $baseUrl = QldbtableHelper::getBaseUrl();
    $columnsLinked = explode(',', $params->get('columnsLinked', ''));
    array_walk($columnsLinked, function(&$item) {$item = trim($item);});

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

    // display vars initiated
    $displayEntry = $helper->checkDisplayEntry($input);
    $displayList = !$displayEntry || $params->get('list_display', true);
    $displayBackToList = (bool)$params->get('back_to_list', false);

    /* get data of single entry, if needed */
    if ($displayEntry) {
        $ident = $input->getInt(QldbtableHelper::GETPARAM_ENTRYID, 0);
        $entry = $helper->getEntry($ident);
        $entry = $helper->setImage($entry, $typeMappingEntry, $params->get('entry_image_default', ''));
        $entry = $helper->addTags($entry, $ident, $module->id, $baseUrl, $params->get('linkIdent', 'id'));
        $entry = $helper->flattenData($entry, $typeMapping, (bool)$params->get('entry_display', false), (bool)$params->get('imageTag', false), $columnsLinked);
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
    $data = $helper->setImageMultiple($data, $typeMapping);
    $data = $helper->addTagsMultiple($data, $params->get('linkText', 'Link'), $module->id, $baseUrl, $params->get('linkIdent', 'id'));
    $dataFlattened = $helper->flattenDataMultiple($data, $typeMapping, (bool)$params->get('entry_display', false), (bool)$params->get('imageTag', false), $columnsLinked);

    /* finally display */
    require JModuleHelper::getLayoutPath('mod_qldbtable', $params->get('layout', 'default'));
} catch (Exception $e) {
    $app->enqueueMessage(implode('<br >', [$e->getMessage(), $e->getFile(), $e->getLine()]));
}
