<?php
/**
 * @package        mod_qlqldbtable
 * @copyright    Copyright (C) 2023 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use Joomla\Input\Input;

defined('_JEXEC') or die;

class QldbtableHelper
{
    public Joomla\Registry\Registry $params;
    public stdClass $module;
    public JDatabaseDriver $db;
    const NUMBER_COLUMNS = 10;
    const DISPLAY_DEFAULT = 'table';
    const DISPLAY_TABLE = 'table';
    const DISPLAY_CARDS = 'cards';
    const TYPE_COLNAME = 'colname';
    const TYPE_LABEL = 'label';
    const TYPE_TYPE = 'type';
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const HTML_IMG = '<img src="%s" />';
    const HTML_AHREF = '<a href="%s" />%s</a>';
    const GETPARAM_MODULEID = 'modqldbtable';
    const GETPARAM_ENTRYID = 'modqldbtableentryid';
    const QLDBTABLE = 'qlbdtable';
    const QLDBTABLE_ID = 'id';
    const QLDBTABLE_MODULEID = 'module_id';
    const QLDBTABLE_LINK = 'link';

    function __construct($module, $params, $db)
    {
        $this->module = $module;
        $this->params = $params;
        $this->db = $db;
    }

    public function getData(): array
    {
        return $this->params->get('use_raw_query', false)
            ? $this->getDataByRawQuery()
            : $this->getDataByTable();
    }

    public function alterData(array $data, array $columnsDataMap = [], string $defaultImage = ''): array
    {
        if (0 === count($data) || 0 === count($columnsDataMap)) {
            return [];
        }
        array_walk($data, function (&$item) use ($columnsDataMap, $defaultImage) {
            foreach ($columnsDataMap as $colname => $type) {
                if (QldbtableHelper::TYPE_IMAGE === $type && isset($item[$colname])) {
                    if (empty($item[$colname]) && !empty($defaultImage)) {
                        $item[$colname] = $defaultImage;
                    }
                    $item[$colname] = sprintf(QldbtableHelper::HTML_IMG, $item[$colname]);
                }
            }
        });
        return $data;
    }

    public function addLink(array $data, string $linkText, int $moduleId, string $ident = 'id'): array
    {
        if (0 === count($data)) {
            return [];
        }
        $baseUrl = QldbtableHelper::getBaseUrl();$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        array_walk($data, function (&$item) use ($baseUrl, $linkText, $moduleId, $ident) {
            $id = $item[$ident];
            $item[QldbtableHelper::QLDBTABLE] = [
                QldbtableHelper::QLDBTABLE_ID => $id,
                QldbtableHelper::QLDBTABLE_MODULEID => $moduleId,
                QldbtableHelper::QLDBTABLE_LINK => QldbtableHelper::getLink($baseUrl, $linkText, $moduleId, $id),
            ];
        });
        return $data;
    }

    private static function getBaseUrl(): string
    {
        return (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public static function getLink(string $baseUrl, string $linkText, int $moduleId, int $ident): string
    {
        $link = sprintf('%s=%s&%s=%s',
            QldbtableHelper::QLDBTABLE, $moduleId,
            QldbtableHelper::QLDBTABLE . QldbtableHelper::QLDBTABLE_ID, $ident
        );
        if (false !== strpos('?', $baseUrl)) {
            $link = $baseUrl . '&' . $link;
        } else {
            $link = $baseUrl . '?' . $link;
        }
        return sprintf(QldbtableHelper::HTML_AHREF, $link, $linkText);
    }

    public function getColumnType(): array
    {
        return $this->getColumnInfo(QldbtableHelper::TYPE_TYPE);
    }

    public function getColumns()
    {
        return $this->getColumnInfo(QldbtableHelper::TYPE_LABEL);
    }

    public function getColumnInfo(string $type)
    {
        $structure = $this->getStructure();
        array_walk($structure, function (&$item) use ($type) {
            $item = $item[$type];
        });
        return $structure;
    }

    public function getStructure()
    {
        $columnField = 'column%s';

        $structure = [];
        for ($i = 1; $i <= QldbtableHelper::NUMBER_COLUMNS; $i++) {

            $fieldname = sprintf($columnField, $i);
            $column = $this->params->get($fieldname);
            $columnDisplay = explode(';', $column);
            if (3 !== count($columnDisplay)) {
                continue;
            }
            $columnName = $columnDisplay[0];
            $columnLabel = $columnDisplay[1];
            $columnType = $columnDisplay[2];

            $structure[$columnName] = [
                'column' => $columnName,
                QldbtableHelper::TYPE_COLNAME => $columnName,
                QldbtableHelper::TYPE_TYPE => $columnType,
                QldbtableHelper::TYPE_LABEL => $columnLabel,
            ];
        }
        return $structure;
    }

    private function getDataByRawQuery(): array
    {
        $this->db->setQuery($this->params->get('raw_query'));
        return $this->db->loadAssocList();
    }

    private function getDataByTable(): array
    {
        $query = $this->db->getQuery(true);
        $query->select('*');
        $query->from($this->params->get('tablename'));

        $condition = trim($this->params->get('conditions'));
        if (!empty($condition)) {
            $query->where($condition);
        }

        $orderBy = trim($this->params->get('order_by'));
        if (!empty($orderBy)) {
            $query->order($orderBy);
        }

        $this->db->setQuery($query);

        return $this->db->loadAssocList();
    }

    public function checkDisplayEntry(Joomla\Input\Input $input): bool
    {
        return
            (int)$this->module->id === (int)$input->get(QldbtableHelper::GETPARAM_MODULEID)
            && is_numeric($input->get(QldbtableHelper::GETPARAM_ENTRYID));
    }
}
