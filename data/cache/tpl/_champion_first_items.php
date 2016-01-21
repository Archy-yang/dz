<? if(!defined('IN_WP')) exit('Access Denied');?>
<h2 class="champion-stats">最常见的出门装</h2>
<div class="build-wrapper">
    <?php  for($k=0; $k<count($championData['firstItems']['mostGames']['items']); $k++) {?>
    <a href="http://leagueoflegends.wikia.com/wiki/<?php echo  $championData['firstItems']['mostGames']['items'][$k]['name'] ?>" rel="nofollow" target="_blank">
        <img src="//ddragon.leagueoflegends.com/cdn/<?php echo  CORE_DDPATCH ?>/img/item/<?php echo  $championData['firstItems']['mostGames']['items'][$k]['id'] ?>.png" class="possible-build" tooltip="<?php echo  $championData['firstItems']['mostGames']['items'][$k]['name'] ?>"/>
    </a>
    <?php  } ?>
    <div class="build-text">
        <strong><?php echo  $championData['firstItems']['mostGames']['winPercent'] ?>%</strong> 胜率 | <strong><?php echo  $championData['firstItems']['mostGames']['games'] ?></strong> 局比赛
    </div>
</div>
<h2 class="champion-stats" style="margin-top:40px">胜率最高的出门装</h2>
<div class="build-wrapper">
    <?php  for($l=0; $l<count($championData['firstItems']['highestWinPercent']['items']); $l++) {?>
    <a href="http://leagueoflegends.wikia.com/wiki/<?php echo  $championData['firstItems']['highestWinPercent']['items'][$l]['name']?>" rel="nofollow" target="_blank">
        <img src="//ddragon.leagueoflegends.com/cdn/<?php echo  CORE_DDPATCH ?>/img/item/<?php echo  $championData['firstItems']['highestWinPercent']['items'][$l]['id'] ?>.png" class="possible-build" tooltip="<?php echo  $championData['firstItems']['highestWinPercent']['items'][$l]['name'] ?>"/>
    </a>
    <?php  } ?>
    <div class="build-text">
        <strong><?php echo $championData['firstItems']['highestWinPercent']['winPercent'] ?>%</strong> 胜率 | <strong><?php echo  $championData['firstItems']['highestWinPercent']['games'] ?></strong> 局比赛
    </div>
</div>
