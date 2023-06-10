<?php

namespace Hoochicken\Datagrid;

class Datagrid
{

    private array $columns = [];
    private array $rows = [];
    private string $tableClass = '';
    private bool $headerCaps = true;

    const TABLE = '<table class="{table_class}">{thead}{tbody}</table>';
    const THEAD = '<thead>{content}</thead>';
    const TBODY = '<tbody>{content}</tbody>';
    const TR = '<tr>{content}</tr>';
    const TD = '<td>{content}</td>';
    const TH = '<th>{content}</th>';


    public function getTable(array $data, array $columns = []): string
    {
        if (0 === count($data)) return '';
        if (0 === count($columns)) {
            $columns = $this->getDefaultColumns($data[0] ?? []);
        }

        array_walk($data, function (&$item) use ($columns) {
            $item = array_intersect_key($item, $columns);
        });

        $html = str_replace('{table_class}', $this->getTableCLass(), self::TABLE);
        $html = str_replace('{thead}', $this->getTHead($columns), $html);
        $html = str_replace('{tbody}', $this->getTBody($data), $html);
        return $html;
    }

    public function getDefaultColumns(array $firstRow): array
    {
        $columns = [];
        foreach ($firstRow as $column => $content) {
            $columns[$column] = $column;
        }
        return $columns;
    }

    public function getThead(array $columns): string
    {
        if ($this->headerCaps) {
            array_walk($columns, function(&$item) { $item = ucwords($item); });
        }
        return str_replace('{content}', $this->getTr($columns, true), self::THEAD);
    }

    public function getTBody(array $data): string
    {
        $rows = [];
        foreach ($data as $row) {
            $rows[] = $this->getTr($row);
        }
        return str_replace('{content}', $this->concatTags($rows), self::TBODY);
    }

    public function getCell($content, bool $th = false, string $class = ''): string
    {
        $cell = $th ? self::TH : self::TD;
        return str_replace('{content}', $content, $cell);
    }

    public function getTr(array $row, bool $th = false): string
    {
        $cells = [];
        foreach ($row as $column => $label) {
            $cells[] = $this->getCell($label, $th, $column);
        }
        return str_replace('{content}', $this->concatTags($cells) ,self::TR);
    }
    public function concatTags(array $tags): string
    {
        return implode("\n", $tags);
    }

    public function setTableClass(string $tableClass)
    {
        $this->tableClass = $tableClass;
    }

    public function getTableClass(): string
    {
        return $this->tableClass;
    }

    public function setTableHeaderCaps(string $value)
    {
        $this->headerCaps = $value;
    }

    public function getTableHeaderCaps(): string
    {
        return $this->headerCaps;
    }
}
