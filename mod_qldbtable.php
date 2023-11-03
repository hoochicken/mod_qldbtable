<?php
/**
 * @package		mod_qldbtable
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

defined('_JEXEC') or die;
require_once __DIR__ . '/QldbtableHelper.php';
require_once __DIR__ . '/php/classes/QldbtableError.php';
require_once __DIR__ . '/vendor/autoload.php';

/** @var stdClass $module */
/** @var \Joomla\Registry\Registry $params */

try {
    $errores = new QldbtableError;
    $app = Factory::getApplication();
    $input = Factory::getApplication()->getInput();
    $helper = new QldbtableHelper($module, $params, Factory::getContainer()->get(DatabaseInterface::class));
    $originalUrl = $helper->getOriginalUrl($helper->getCurrentUrl());
    $baseUrl = QldbtableHelper::getBaseUrl();
    $columnsLinked = explode(',', $params->get('columnsLinked', ''));
    $entry = [];
    $displayNavigation = (bool)$params->get('navigation', false);
    $cardLinkDisplay = $params->get('cardLinkDisplay', false);
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
    $ident = $input->getInt(QldbtableHelper::GETPARAM_ENTRYID, 0);
    $label = Text::_($params->get('label_more', ''));

    /* get data of single entry, if needed */
    if ($displayEntry) {
        $entry = $helper->getEntry($ident);
        $entry = $helper->setImage($entry, $typeMappingEntry, $params->get('entry_image_default', ''));
        $entry = $helper->addTags($entry, $label, $module->id, $baseUrl, $params->get('linkIdent', 'id'));
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

    if (file_exists(__DIR__ . '/php/classes/QlDatabasetableDataFilter.php')) {
        require_once __DIR__ . '/php/classes/QlDatabasetableDataFilter.php';
        $filter = new QlDatabasetableDataFilter($module, $params, Factory::getContainer()->get(DatabaseInterface::class));
        $data = $filter->filter($data);
    }

    foreach ($data as $k => $item) {
        $item = $helper->setImage($item, $typeMappingEntry, $params->get('entry_image_default', ''));
        $item = $helper->addTags($item, Text::_($params->get('label_more', '')), $module->id, $baseUrl, $params->get('linkIdent', 'id'));
        $item = $helper->flattenData($item, $typeMapping, (bool)$params->get('entry_display', false), (bool)$params->get('imageTag', false), $columnsLinked);
        $data[$k] = $item;
    }

    $prev = $helper->getPrev($data, $entry, $params->get('identColumn', 'id'));
    $next = $helper->getNext($data, $entry, $params->get('identColumn', 'id'));

    // data for charts
    $displayCharts = $params->get('charts_display', 0);
    if ($displayCharts) {
        $chartLimit = $params->get('charts_limit', 5);
        $chartLabelInLegend = $params->get('charts_label_in_legend', '# in counts');
        $chartsLabelColumn = $params->get('charts_label_column', 'label');
        $dataForCharts = array_slice($data, 0, $chartLimit > 0 ? $chartLimit : 0);
        $dataForCharts_labels = array_column($dataForCharts, $chartsLabelColumn);
        if (empty($dataForCharts_labels)) {
            $errores->addError(sprintf('Column "%s" could not be found.', $chartsLabelColumn));
            $dataForCharts_labels = range(1, $dataForCharts);
        }
        array_walk($dataForCharts_labels, function(&$item) {
            $item = sprintf('"%s"', $item);
        });
        $dataForCharts_counter = array_column($dataForCharts, $params->get('charts_counter_column', 'counter'));
    }
    /* finally display */
    require ModuleHelper::getLayoutPath('mod_qldbtable', $params->get('layout', 'default'));
} catch (Exception $e) {
    $app->enqueueMessage(implode('<br >', [$e->getMessage(), $e->getFile(), $e->getLine()]));
}
