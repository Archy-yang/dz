<? if(!defined('IN_WP')) exit('Access Denied');?>
<h2 style="margin-bottom:8px">统计</h2>
<table class="table table-striped">
    <thead>
        <tr >
            <th class="first-column"><span class="text-indent">位置</span></th>
            <th class="second-column">平均</th>
            <th class="third-column"><span class="glyphicon glyphicon-question-sign" tooltip="<?php echo  $champion['name'] ?>在所有<?php echo  $champion['roleTitle'] ?>英雄中的排名/辅助英雄数"></span>同位置排行</th>
            <th class="fourth-column">当前版本变化</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($championData['general'] as $i => $general) { ?> 
        <tr id=<?php echo  'statistics-'. strtolower(str_replace(' ', '-', $general['title'])) . '-row' ?> >
            <td>
                <a href="/statistics/#?roleSort=<?php echo  $champion['roleTitle'] ?>&sortBy=general.<?php echo  $general['titleLink'] ?>&order=descend">
                    <?php echo  $general['title'] ?>
                </a>
            </td>

            <td><?php echo  $general['val'] ?><?php  if($i < 3){ ?>%<?php }?></td>
            <td>
                <strong class="<?php if ($general['position'] <= ($generalRole['totalNumber']/2)) { ?> top-half <?php  } else { ?> bottom-half <?php  } ?>">
                    <?php echo  $general['position'] ?>
                </strong>
                <small>/ <?php echo  $generalRole['totalNumber'] ?></small>
            </td>
            <td>
                <span class="glyphicon <?php if ($general['change'] > 0) { ?> glyphicon-arrow-up <?php  } ?> <?php if ($general['change'] < 0) { ?> glyphicon-arrow-down <?php  } ?> <?php if ($general['change'] === 0) { ?> same-position <?php  } ?>">
                </span>
                <?php echo abs($general['change']) ?></td>
        </tr>

        <?php  } ?>

        <tr>
            <td colspan=2> <a href="/statistics/#?roleSort=<?php echo  $champion['roleTitle'] ?>&sortBy=general.overallPosition&order=ascend">综合排名</a></td>
            <td style="text-align:center"><strong class="<?php if ($championData['overallPosition']['position'] <= ($generalRole['totalNumber']/2)) { ?> top-half <?php  } else { ?> bottom-half <?php  } ?>"><?php echo  $championData['overallPosition']['position'] ?> </strong><small>/ <?php echo  $generalRole['totalNumber'] ?></small></td>
            <td><span class="glyphicon <?php if ($championData['overallPosition']['change'] > 0) { ?> glyphicon-arrow-up <?php  } ?> <?php if ($championData['overallPosition']['change'] < 0) { ?> glyphicon-arrow-down <?php  } ?> <?php if ($championData['overallPosition']['change'] == 0) { ?> same-position <?php  } ?>">
            </span> <?php echo abs($championData['overallPosition']['change']) ?></td>
        </tr>
    </tbody>
</table>


<h2>全英雄对比图</h2>
<div class="chart-holder" style="max-width:310px">
    <canvas tc-chartjs-radar chart-data="overallComparison.data" chart-options="overallComparison.settings" chart-legend="generalComparison" id="canvas2" height="242" width="339"></canvas>
</div>
<div tc-chartjs-legend chart-legend="generalComparison"></div>
