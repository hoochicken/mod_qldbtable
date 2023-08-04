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
/* @var string $cardCssClass */
/* @var bool $cardLinkDisplay */
?>
<div class="card-group">
<?php foreach ($data as $k => $entry) : ?>
    <?php $entryLink = $entry[QldbtableHelper::QLDBTABLE][QldbtableHelper::QLDBTABLE_URL] ?? ''; ?>
    <div class="card <?php echo $cardCssClass; ?>">
        <?php if ($cardLinkDisplay && !empty($entryLink)) : ?>
            <a href="<?= $entryLink ?>">
        <?php endif; ?>
        <?php echo $entry[$imageColumn]; ?>
        <?php if ($params->get('cardLinkDisplay', false)) : ?>
            </a>
        <?php endif; ?>

        <div class="card-body">
            <h5 class="card-title"><?php echo $entry[$labelColumn]; ?></h5>
            <?php if ($cardLinkDisplay && !empty($label)) : ?>
                <a href="<?= $entry[QldbtableHelper::QLDBTABLE_TAGS][QldbtableHelper::QLDBTABLE_URL] ?>"><?= $label ?></a>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
