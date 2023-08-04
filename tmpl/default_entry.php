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
/* @var bool $displayBackToList */
/* @var bool $displayNavigation */
/* @var ?array $next */
/* @var ?array $prev */

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
    <div class="navigation d-flex justify-content-between">
    <?php if ($displayNavigation) : ?>
        <?php if (is_null($prev)) : ?>
            <span class="btn btn-secondary disabled"><?= Text::_('MOD_QLDBTABLE_PREV') ?></span>
        <?php else : ?>
            <a class="btn btn-secondary" href="<?= $prev[QldbtableHelper::QLDBTABLE][QldbtableHelper::QLDBTABLE_URL] ?>"><?= $params->get('linkTextPrev', Text::_('MOD_QLDBTABLE_PREV')) ?></a>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($displayBackToList) : ?>
        <a class="btn btn-secondary" href="<?= $originalUrl ?>"><?= $params->get('linkTextBackToList', Text::_('MOD_QLDBTABLE_BACKTOLIST')) ?></a>
    <?php endif; ?>
    <?php if ($displayNavigation) : ?>
        <?php if (is_null($next)) : ?>
            <span class="btn btn-secondary disabled"><?= Text::_('MOD_QLDBTABLE_PREV') ?></span>
        <?php else : ?>
            <a class="btn btn-secondary" href="<?= $next[QldbtableHelper::QLDBTABLE][QldbtableHelper::QLDBTABLE_URL] ?>"><?= $params->get('linkTextNext', Text::_('MOD_QLDBTABLE_NEXT')) ?></a>
        <?php endif; ?>
    <?php endif; ?>
    </div>
</div>
