<?php
/**
 * @package		mod_qldbtable
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// no direct access
defined('_JEXEC') or die;
/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
/** @var stdClass $module */
/** @var string $chartLabelInLegend */
/** @var array $data */
/** @var array $dataForCharts */
/** @var array $dataForCharts_labels */
/** @var array $dataForCharts_counter */

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseScript('qldbtable-chartsjs', 'mod_qldbtable/chartsjs.js');
$canvasCssId = sprintf('module%sCanvas', $module->id);
$script = sprintf("
const ctx = document.getElementById('%s');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: [%s],
      datasets: [{
        label: '%s',
        data: [%s],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
"
 , $canvasCssId, implode(',', $dataForCharts_labels) , $chartLabelInLegend, implode(',', $dataForCharts_counter));
$wa->addInlineScript($script);
?>
<canvas id="<?= $canvasCssId ?>"></canvas>
