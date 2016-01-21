<? if(!defined('IN_WP')) exit('Access Denied');?>
<h2 class="line-chart-header">各版本胜率变化</h2>
<div class="chart-holder">
    <canvas tc-chartjs-line chart-data="patchRate.data" chart-options="patchRate.settings" id="canvas3" height="150" width="300"></canvas>
</div>
<div tc-chartjs-legend chart-legend="playRate"></div>
<h2 class="line-chart-header">各版本登场率变化</h2>
<div class="chart-holder">
    <canvas tc-chartjs-line chart-data="patchPlay.data" chart-options="patchPlay.settings" chart-legend="playRate" id="canvas" height="150" width="300"></canvas>
</div>
<div tc-chartjs-legend chart-legend="playRate"></div>

<h2 class="champion-stats">伤害组成</h2>
<div class="clearfix middle-graphic-holder damage-dealt">
    <div class="physical-dmg" tooltip="<?php echo  $championData['dmgComposition']['physicalDmg'] ?>% Physical" style="width:<?php echo  $championData['dmgComposition']['physicalDmg'] ?>%"></div>
    <div class="magic-dmg" tooltip="<?php echo  $championData['dmgComposition']['magicDmg'] ?>% Magic" style="left:<?php echo  $championData['dmgComposition']['physicalDmg'] ?>%;width:<?php echo  $championData['dmgComposition']['magicDmg'] ?>%"></div>
    <div class="true-dmg" tooltip="<?php echo  $championData['dmgComposition']['trueDmg'] ?>% True" style="right:0;width:<?php echo  $championData['dmgComposition']['trueDmg'] ?>%"></div>
</div>
<ul class="radar-legend" style="margin-bottom:20px">
    <li><span class="physical-dmg"></span>物理伤害</li>
    <li><span class="magic-dmg"></span>魔法伤害</li>
    <li><span class="true-dmg"></span>真实伤害</li>
</ul>

<div class="trinket-stats">
    <h2 class="champion-stats">饰品数据</h2>
    <?php  $trinkets = $championData['trinkets']; ?>
    <?php  for($i = 0; $i<count($trinkets); $i++){ ?>
    <div style="width:33%;float:left;text-align:center">
        <img class="possible-build" src="//ddragon.leagueoflegends.com/cdn/<?php echo  CORE_DDPATCH ?>/img/item/<?php echo  $trinkets[$i]['item']['id'] ?>.png" tooltip="<?php echo  $trinkets[$i]['item']['name'] ?>"/>
        <div class="build-text">
            <strong style="display:block"><?php echo  $trinkets[$i]['winPercent'] ?>%</strong>
            胜率
            <strong style="display:block;margin-top:10px"><?php echo  $trinkets[$i]['games'] ?></strong>
            样本数 </div>
    </div>
    <?php  } ?>

</div>
