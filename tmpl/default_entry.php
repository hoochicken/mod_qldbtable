<?php
/**
 * @package		mod_qldbtable
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

use Hoochicken\Datagrid\Datagrid;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// no direct access
defined('_JEXEC') or die;

/* @var stdClass $module */
/* @var Joomla\Registry\Registry $params */
/* @var Joomla\Application\ $app */
/* @var array $entryStructure */
/* @var array $entry */
/* @var string $originalUrl */

?>
<div class="entry">
    <?php foreach ($entryStructure as $column => $columInfo) : ?>
        <div class="column">
            <?php if (!empty($columInfo['label'])) : ?>
                <strong class="column label-<?= $column ?>"><?= $columInfo['label'] ?></strong>
            <?php endif; ?>
            <span class="value <?= $column ?>"><?= $entry[$column] ?? '' ?></span>
        </div>
    <?php endforeach;?>
    <?php if (!empty($label)) : ?>
        <a class="btn btn-primary" href="<?= $originalUrl ?>"><?= Text::_('MOD_QLDBTABLE_BACKTOLIST') ?></a>
    <?php endif; ?>
</div>
