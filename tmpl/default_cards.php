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
/* @var array $dataFlattened */
/* @var Joomla\Application\ $app */
/* @var string $imageColumn */
/* @var string $labelColumn */
/* @var string $cardCssClass */
?>
<div class="card-group">
<?php foreach ($dataFlattened as $k => $entry) : ?>
    <?php $entryLink = $entry[QldbtableHelper::QLDBTABLE][QldbtableHelper::QLDBTABLE_URL] ?? ''; ?>
    <div class="card <?php echo $cardCssClass; ?>">
        <?php if ($params->get('imageLinked', false) && !empty($entryLink)) : ?>
            <a href="<?= $entryLink ?>">
        <?php endif; ?>
        <?php echo $entry[$imageColumn]; ?>
        <?php if ($params->get('imageLinked', false)) : ?>
            </a>
        <?php endif; ?>

        <div class="card-body">
            <h5 class="card-title"><?php echo $entry[$labelColumn]; ?></h5>
            <?php if ($params->get('cardLinkDisplay', false)) : ?>
            <?php echo $entry[QldbtableHelper::QLDBTABLE][QldbtableHelper::QLDBTABLE_LINK]; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
