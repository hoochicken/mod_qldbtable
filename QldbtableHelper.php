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
    const HTML_AHREF = '<a class="btn btn-outline-secondary" href="%s" />%s</a>';
    const GETPARAM_MODULEID = 'modqldbtable';
    const GETPARAM_ENTRYID = 'modqldbtableentryid';
    const QLDBTABLE = 'qlbdtable';
    const QLDBTABLE_TAGS = 'qlbdtable_tags';
    const QLDBTABLE_ID = 'id';
    const QLDBTABLE_MODULEID = 'module_id';
    const QLDBTABLE_LINK = 'link';
    const QLDBTABLE_URL = 'url';
    const URL_SCHEME = '%s://%s%s';

    function __construct($module, $params, $db)
    {
        $this->module = $module;
        $this->params = $params;
        $this->db = $db;
    }

    public function getData(): array
    {
        $data = $this->getDataRaw();
        foreach ($data as $k => $entry) {
            $data[$k] = $this->enrichEntryWithDefaults($entry);
        }
        return $data;
    }

    public function getDataRaw(): array
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
            if (QldbtableHelper::TYPE_IMAGE !== $type || !isset($entry[$colname])) {
                continue;
            }
            if (empty($entry[$colname]) && !empty($defaultImage)) {
                $entry[$colname] = $defaultImage;
            }
            $entry[QldbtableHelper::QLDBTABLE_TAGS][$colname] = static::generateHtmlImage($entry[$colname]);
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
            $item[QldbtableHelper::QLDBTABLE_TAGS][QldbtableHelper::QLDBTABLE_LINK] = QldbtableHelper::getLink($baseUrl, $linkText, $moduleId, $id);
            $item[QldbtableHelper::QLDBTABLE][QldbtableHelper::GETPARAM_ENTRYID] = $id;
            $item[QldbtableHelper::QLDBTABLE][QldbtableHelper::GETPARAM_MODULEID] = $moduleId;
            $item[QldbtableHelper::QLDBTABLE][QldbtableHelper::QLDBTABLE_URL] = QldbtableHelper::getUrl($baseUrl, $moduleId, $id);
        });
        return $data;
    }

    public function flattenData(array $data, $typeMapping, bool $imageTag = false, array $columnsLinked = []): array
    {
        if (0 === count($data)) {
            return $data;
        }
        array_walk($data, function (&$entry) use ($typeMapping, $imageTag, $columnsLinked) {
            foreach($typeMapping as $columnName => $type) {
                if ($imageTag && static::TYPE_IMAGE === $type) {
                    $entry[$columnName] = $entry[QldbtableHelper::QLDBTABLE_TAGS][$columnName] ?? $entry[$columnName];
                }
                if (in_array($columnName, $columnsLinked)) {
                    $url = $entry[static::QLDBTABLE][static::QLDBTABLE_URL];
                    $entry[$columnName] = static::generateHtmlLink($url, $entry[$columnName]);
                }
            }
        });
        return $data;
    }

    private static function getBaseUrl(): string
    {
        return (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public static function getLink(string $baseUrl, string $linkText, int $moduleId, int $ident): string
    {
        $url = static::getUrl($baseUrl, $moduleId, $ident);
        return static::generateHtmlLink($url, $linkText);
    }

    public static function generateHtmlLink(string $url, string $linkText): string
    {
        return sprintf(QldbtableHelper::HTML_AHREF, $url, $linkText);
    }

    public static function generateHtmlImage(string $imagePath): string
    {
        return sprintf(QldbtableHelper::HTML_IMG, $imagePath);
    }

    public static function getUrl(string $baseUrl, int $moduleId, int $ident): string
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
        return $link;
    }

    public function getColumnType(string $prefix = ''): array
    {
        return $this->getColumnInfo(QldbtableHelper::TYPE_TYPE, $prefix);
    }

    public function getEntryColumnType(): array
    {
        return $this->getStructure(QldbtableHelper::PRFX_ENTRY);
    }

    public function getEntryStructure(): array
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
        $entry = $this->getEntryRaw($ident);
        return $this->enrichEntryWithDefaults($entry);
    }

    public function enrichEntryWithDefaults(array $entry): array
    {
        $entry[QldbtableHelper::QLDBTABLE_TAGS] = [];
        $entry[QldbtableHelper::QLDBTABLE] = [];
        return $entry;
    }

    public function getEntryRaw(int $ident): array
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

    public function addImage(array $entry, array $typeMapping, bool $entryImageTag): array
    {
        if (!$entryImageTag) {
            return $entry;
        }
        foreach ($typeMapping as $columnName => $type) {
            if (static::TYPE_IMAGE !== $type) {
                continue;
            }
            $entry[$columnName] = static::generateHtmlImage($entry[$columnName]);
        }
        return $entry;
    }

    public function getCurrentUrl(): string
    {
        return sprintf(static::URL_SCHEME, $_SERVER['REQUEST_SCHEME'], $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']);
    }

    public function getOriginalUrl(string $url): string
    {
        $regex = sprintf('/([?&])%s=([0-9]*)/', static::GETPARAM_ENTRYID);
        return preg_replace($regex, '', $url);
    }
}
