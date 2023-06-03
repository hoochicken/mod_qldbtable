<?php
/**
 * @package        mod_qlqldbtable
 * @copyright    Copyright (C) 2015 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class modQldbtableHelper
{
    public Joomla\Registry\Registry $params;
    public stdClass $module;
    public JDatabaseDriver $db;
    const NUMBER_COLUMNS = 7;

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

    private function getDataByRawQuery(): array
    {
        return [];
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

    public function getColumns()
    {
        $header = $this->getStructure();
        array_walk($header, function (&$item) {
            $item = $item['label'];
        });
        return $header;
    }

    public function getStructure()
    {
        $column = 'column%s';
        $type = $column . '_type';
        $label = $column . '_text';

        $structure = [];
        for ($i = 1; $i <= self::NUMBER_COLUMNS; $i++) {
            $columnName = $this->params->get(sprintf($column, $i));
            if (empty($columnName)) {
                continue;
            }
            $columnType = $this->params->get(sprintf($type, $i));
            $columnLabel = $this->params->get(sprintf($label, $i));
            $structure[$columnName] = [
                'column' => $columnName,
                'type' => $columnType,
                'label' => $columnLabel,
            ];
        }
        return $structure;
    }
}
