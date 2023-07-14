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
    const PRFX_ENTRY = 'entry_';
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
        foreach ($data as $k => $item) {
            $data[$k] = $this->setImageDefault($item, $columnsDataMap, $defaultImage);
        }
        return $data;
    }

    public function setImageDefault(array $entry, array $columnsDataMap = [], string $defaultImage = ''): array
    {
        foreach ($columnsDataMap as $colname => $type) {
            if (QldbtableHelper::TYPE_IMAGE === $type && isset($entry[$colname])) {
                if (empty($entry[$colname]) && !empty($defaultImage)) {
                    $entry[$colname] = $defaultImage;
                }
                $entry[$colname] = sprintf(QldbtableHelper::HTML_IMG, $entry[$colname]);
            }
        }
        return $entry;
    }

    public function addLink(array $data, string $linkText, int $moduleId, string $ident = 'id'): array
    {
        if (0 === count($data)) {
            return [];
        }
        $baseUrl = QldbtableHelper::getBaseUrl();
        array_walk($data, function (&$item) use ($baseUrl, $linkText, $moduleId, $ident) {
            $id = $item[$ident];
            $item[QldbtableHelper::QLDBTABLE] = [
                QldbtableHelper::GETPARAM_ENTRYID => $id,
                QldbtableHelper::GETPARAM_MODULEID => $moduleId,
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
            QldbtableHelper::GETPARAM_MODULEID, $moduleId,
            QldbtableHelper::GETPARAM_ENTRYID, $ident
        );

        $regex = QldbtableHelper::GETPARAM_MODULEID . '|' . QldbtableHelper::GETPARAM_ENTRYID;
        $baseUrl = preg_replace('/(&|\?)(' . $regex . ')=([0-9]*)/', '', $baseUrl);

        if (false !== strpos($baseUrl, '?')) {
            $link = $baseUrl . '&' . $link;
        } else {
            $link = $baseUrl . '?' . $link;
        }
        return sprintf(QldbtableHelper::HTML_AHREF, $link, $linkText);
    }

    public function getColumnType(string $prefix = ''): array
    {
        return $this->getColumnInfo(QldbtableHelper::TYPE_TYPE, $prefix);
    }

    public function getEntryColumnType(): array
    {
        return $this->getColumnInfo(QldbtableHelper::TYPE_TYPE, QldbtableHelper::PRFX_ENTRY);
    }

    public function getColumnLabels(string $prefix = ''): array
    {
        return $this->getColumnInfo(QldbtableHelper::TYPE_LABEL, $prefix);
    }

    public function getEntryColumnLabels(): array
    {
        return $this->getColumnInfo(QldbtableHelper::TYPE_LABEL, QldbtableHelper::PRFX_ENTRY);
    }

    public function getColumnInfo(string $type, string $prefix = '')
    {
        $structure = $this->getStructure($prefix);
        array_walk($structure, function (&$item) use ($type) {
            $item = $item[$type];
        });
        return $structure;
    }

    public function getStructure(string $prefix = ''): array
    {
        $columnField = $prefix . 'column%s';

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
        $tablename = $this->params->get('tablename', '');
        if (empty($tablename)) {
            return [];
        }

        $query = $this->db->getQuery(true);
        $query->select('*');
        $query->from($tablename);

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
            $this->params->get('entry_display', false)
            && (int)$this->module->id === (int)$input->get(QldbtableHelper::GETPARAM_MODULEID)
            && is_numeric($input->get(QldbtableHelper::GETPARAM_ENTRYID));
    }

    public function getEntry(int $ident): array
    {
        $tablename = $this->params->get('tablename', '');
        $identColumn = $this->params->get('identColumn', '');
        if (empty($tablename) || empty($identColumn)) {
            return [];
        }

        $query = $this->db->getQuery(true);
        $query->select('*');
        $query->from($tablename);
        $where = sprintf('%s = %s', $identColumn, $query->escape($ident));
        $query->where($where);

        $condition = trim($this->params->get('conditions'));
        if (!empty($condition)) {
            $query->where($condition);
        }

        $this->db->setQuery($query);
        return $this->db->loadAssoc() ?? [];
    }

    public function addImage(array $entry): array
    {

        return $entry;
    }
}
