<? if(!defined('IN_WP')) exit('Access Denied');?>
<h2 class="line-chart-header">比赛时长对胜率的影响</h2>
<div class="chart-holder">
    <canvas tc-chartjs-line chart-data="gameLength.data" chart-options="gameLength.settings" id="canvas" height="150" width="300"></canvas>
</div>
<div tc-chartjs-legend chart-legend="playRate"></div>
<h2 class="line-chart-header">个人使用次数与胜率关系</h2>
<div class="chart-holder">
    <canvas tc-chartjs-line chart-data="experienceRate.data" chart-options="experienceRate.settings" chart-legend="experienceRate" id="canvas" height="150" width="300"></canvas>
</div>
<div tc-chartjs-legend chart-legend="experienceRate"></div>

<h2 class="line-chart-header" style="margin-bottom: 20px;">玩家使用次数分布</h2>
<div class="chart-holder clearfix">
    <div style="width:65%;float:left">
        <canvas tc-chartjs-pie chart-data="experienceDistribution.data.datasets" chart-options="experienceDistribution.settings" chart-legend="experienceDist" id="canvas" height="220" width="300" ></canvas>
    </div>
    <div style="width:35%;float:left">
        <div tc-chartjs-legend chart-legend="experienceDist"></div>
    </div>
</div>

<h2 class="champion-stats">最常见的召唤师技能</h2>
<div class="summoner-wrapper">
    <a href="http://leagueoflegends.wikia.com/wiki/<?php echo  $championData['summoners']['mostGames']['summoner1']['name'] ?>" rel="nofollow" target="_blank">
        <img src="//ddragon.leagueoflegends.com/cdn/<?php echo  CORE_DDPATCH ?>/img/spell/<?php echo  $championData['summoners']['mostGames']['summoner1']['url'] ?>" class="possible-build" tooltip="<?php echo  $championData['summoners']['mostGames']['summoner1']['name'] ?>"/>
    </a>

    <a href="http://leagueoflegends.wikia.com/wiki/<?php echo  $championData['summoners']['mostGames']['summoner2']['name'] ?>" rel="nofollow" target="_blank">
        <img src="//ddragon.leagueoflegends.com/cdn/<?php echo  CORE_DDPATCH ?>/img/spell/<?php echo  $championData['summoners']['mostGames']['summoner2']['url'] ?>" class="possible-build" tooltip="<?php echo  $championData['summoners']['mostGames']['summoner2']['name'] ?>"/>
    </a>
    <div class="summoner-text"><strong><?php echo  $championData['summoners']['mostGames']['winPercent'] ?>%</strong> 胜率 | <strong><?php echo  $championData['summoners']['mostGames']['games'] ?></strong> 局比赛</div>
</div>

<h2 class="champion-stats">胜率最高的召唤师技能</h2>
<div class="summoner-wrapper">
    <a href="http://leagueoflegends.wikia.com/wiki/<?php echo  $championData['summoners']['highestWinPercent']['summoner1']['name'] ?>" rel="nofollow" target="_blank">
        <img src="//ddragon.leagueoflegends.com/cdn/<?php echo  CORE_DDPATCH ?>/img/spell/<?php echo  $championData['summoners']['highestWinPercent']['summoner1']['url'] ?>" class="possible-build" tooltip="<?php echo  $championData['summoners']['highestWinPercent']['summoner1']['name'] ?>"/>
    </a>
    <a href="http://leagueoflegends.wikia.com/wiki/<?php echo  $championData['summoners']['highestWinPercent']['summoner2']['name'] ?>" rel="nofollow" target="_blank">
        <img src="//ddragon.leagueoflegends.com/cdn/<?php echo  CORE_DDPATCH ?>/img/spell/<?php echo  $championData['summoners']['highestWinPercent']['summoner2']['url'] ?>" class="possible-build" tooltip="<?php echo  $championData['summoners']['highestWinPercent']['summoner2']['name'] ?>"/>
    </a>
    <div class="summoner-text"><strong><?php echo  $championData['summoners']['highestWinPercent']['winPercent'] ?>%</strong> 胜率 | <strong><?php echo  $championData['summoners']['highestWinPercent']['games'] ?></strong> 局比赛</div>
</div>
