<? if(!defined('IN_WP')) exit('Access Denied');?>
<h2 class="champion-stats">使用率最高的出装</h2>
<div class="build-wrapper">
    <?php  for($k=0; $k<count($championData['items']['mostGames']['items']); $k++) {?>
    <a href="http://leagueoflegends.wikia.com/wiki/<?php echo  $championData['items']['mostGames']['items'][$k]['name'] ?>" rel="nofollow" target="_blank">
        <img src="//ddragon.leagueoflegends.com/cdn/<?php echo  CORE_DDPATCH ?>/img/item/<?php echo  $championData['items']['mostGames']['items'][$k]['id'] ?>.png" class="possible-build" tooltip="<?php echo  $championData['items']['mostGames']['items'][$k]['name'] ?>"/>
    </a>
    <?php  if ($k!=5){ ?>
    <small>></small>
    <?php }?>
    <?php  } ?>
    <div class="build-text">
        <strong><?php echo  $championData['items']['mostGames']['winPercent'] ?>%</strong> 胜率 | <strong><?php echo  $championData['items']['mostGames']['games'] ?></strong> 局比赛
    </div>
</div>

<h2 class="champion-stats"  style="margin-top:40px">胜率最高的出装</h2>
<div class="build-wrapper">
    <?php  for($l=0; $l<count($championData['items']['highestWinPercent']['items']); $l++) {?>
    <a href="http://leagueoflegends.wikia.com/wiki/<?php echo  $championData['items']['highestWinPercent']['items'][$l]['name']?>" rel="nofollow" target="_blank">
        <img src="//ddragon.leagueoflegends.com/cdn/<?php echo  CORE_DDPATCH ?>/img/item/<?php echo  $championData['items']['highestWinPercent']['items'][$l]['id'] ?>.png" class="possible-build" tooltip="<?php echo  $championData['items']['highestWinPercent']['items'][$l]['name'] ?>"/>
    </a>
    <?php  if ($l!=5){ ?>
    <small>></small>
    <?php }?>
    <?php  } ?>
    <div class="build-text">
        <strong><?php echo  $championData['items']['highestWinPercent']['winPercent'] ?>%</strong> 胜率 | <strong><?php echo  $championData['items']['highestWinPercent']['games'] ?></strong> 局比赛
    </div>
</div>
