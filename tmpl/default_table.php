<?php
/**
 * @package		mod_qldbtable
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

use Hoochicken\Datagrid\Datagrid;

// no direct access
defined('_JEXEC') or die;

/* @var stdClass $module */
/* @var \Joomla\Registry\Registry $params */
/* @var array $columns */
/* @var array $data */

array_walk($data, function(&$item) {
    unset($item['qlbdtable_raw_data']);
    unset($item['qlbdtable_tags']);
    unset($item['qlbdtable']);
});
?>

<?php
$datagridtable = new Datagrid();
$datagridtable->setTableClass('table table-striped');
echo $datagridtable->getTable($data, $columns);
?>