<?php
/**
 * @package        mod_qlqldbtable
 * @copyright    Copyright (C) 2015 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class QldbtableHelper
{
    public Joomla\Registry\Registry $params;
    public stdClass $module;
    public JDatabaseDriver $db;
    const NUMBER_COLUMNS = 7;
    const DISPLAY_DEFAULT = 'table';
    const DISPLAY_TABLE = 'table';
    const DISPLAY_CARDS = 'cards';
    const TYPE_COLNAME = 'colname';
    const TYPE_LABEL = 'label';
    const TYPE_TYPE = 'type';
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const HTML_IMG = '<img src="%s" />';

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

    public function alterData(array $data, array $columnsDataMap = []): array
    {
        if (0 === count($data) || 0 === count($columnsDataMap)) {
            return [];
        }
        array_walk($data, function (&$item) use ($columnsDataMap) {
            foreach($columnsDataMap as $colname => $type){
                if (QldbtableHelper::TYPE_IMAGE === $type && isset($item[$colname])) {
                    $item[$colname] = sprintf(QldbtableHelper::HTML_IMG, $item[$colname]);
                }
            }
        });
        return $data;
    }

    public function getColumnType(): array
    {
        return $this->getColumnInfo(self::TYPE_TYPE);
    }

    public function getColumns()
    {
        return $this->getColumnInfo(self::TYPE_LABEL);
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
        for ($i = 1; $i <= self::NUMBER_COLUMNS; $i++) {

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
                self::TYPE_COLNAME => $columnName,
                self::TYPE_TYPE => $columnType,
                self::TYPE_LABEL => $columnLabel,
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

        $this->db->setQuery($query);

        return $this->db->loadAssocList();
    }
}
