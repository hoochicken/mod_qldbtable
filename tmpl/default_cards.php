<?php
/**
 * @package		mod_qldbtable
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

use Hoochicken\Datagrid\Datagrid;
use Joomla\CMS\Factory;
// no direct access
defined('_JEXEC') or die;

/* @var stdClass $module */
/* @var \Joomla\Registry\Registry $params */
/* @var array $columns */
/* @var array $data */
/* @var Joomla\Application\ $app */
/* @var string $imageColumn */
/* @var string $labelColumn */

?>
<div class="card-group">
<?php foreach ($data as $k => $entry) : ?>
    <div class="card">
        <?php echo $entry[$imageColumn]; ?>
        <div class="card-body">
            <h5 class="card-title"><?php echo $entry[$labelColumn]; ?></h5>
        </div>
    </div>
<?php endforeach; ?>
</div>
