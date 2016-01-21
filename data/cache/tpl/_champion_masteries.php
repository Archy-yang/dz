<? if(!defined('IN_WP')) exit('Access Denied');?>
<h2 class="champion-stats">最常见的天赋</h2>
<div class="mastery-container clearfix">
    <?php  for ($k = 0; $k<count($masteryOrder); $k++){ ?>
    <div class="mastery<?php echo  $k ?>">
        <div class="mastery-header">
            <?php echo  $championData['masteries']['mostGames']['masteries'][$k]['tree'] ?> - <?php echo  $championData['masteries']['mostGames']['masteries'][$k]['total'] ?>
        </div>
        <?php  for ($z = 1; $z<=6; $z++){ ?>
        <?php  $masteryColumn = $championData['masteries']['mostGames']['masteries'][$k]['data']['row'.$z]; ?>
        <div class="clearfix mastery-row">
            <?php  for($y = 0; $y < count($masteryColumn); $y++) { ?>
            <?php  if($masteryColumn[$y]['mastery']){ ?>
            <?php  if($masteryColumn[$y]['points']){ ?>
            <div class="mastery-icon mastery-<?php echo  $masteryColumn[$y]['mastery'] ?> mastery-active" champion-tip api-type="masteries" api-primary-id="<?php echo  $masteryColumn[$y]['mastery'] ?>" api-secondary-id="<?php echo $masteryColumn[$y]['points']-1 ?>">
                <div class="points">
                    <?php  for($p = 0; $p < $masteryColumn[$y]['points']; $p++){ ?>
                    <div class="point"></div>
                    <?php  } ?>
                </div>
            </div>
            <?php  } else { ?>
            <div class="mastery-icon mastery-<?php echo  $masteryColumn[$y]['mastery'] ?>" champion-tip api-type="masteries" api-primary-id="<?php echo  $masteryColumn[$y]['mastery'] ?>">
            </div>
            <?php  } ?>
            <?php  if($z % 2 == 1 && $y == 0){ ?>
            <div class="mastery-spacer"></div>
            <?php  } ?>
            <?php  } ?>
            <?php  } ?>
        </div>
        <?php  } ?>
    </div>
    <?php  } ?>
</div>
<div class="build-text">
    <strong><?php echo  $championData['masteries']['mostGames']['winPercent'] ?>%</strong> 胜率 | <strong><?php echo  $championData['masteries']['mostGames']['games'] ?></strong> 局比赛
</div>


<h2 class="champion-stats">胜率最高的天赋</h2>
<div class="mastery-container clearfix">
    <?php  for ($k = 0; $k<count($masteryOrder); $k++){ ?>
    <div class="mastery<?php echo  $k ?>">
        <div class="mastery-header">
            <?php echo  $championData['masteries']['highestWinPercent']['masteries'][$k]['tree'] ?> - <?php echo  $championData['masteries']['highestWinPercent']['masteries'][$k]['total'] ?>
        </div>
        <?php  for ($z = 1; $z<=6; $z++){ ?>
        <?php  $masteryColumn = $championData['masteries']['highestWinPercent']['masteries'][$k]['data']['row'.$z]; ?>
        <div class="clearfix mastery-row">
            <?php  for($y = 0; $y < count($masteryColumn); $y++) { ?>
            <?php  if($masteryColumn[$y]['mastery']){ ?>
            <?php  if($masteryColumn[$y]['points']){ ?>
            <div class="mastery-icon mastery-<?php echo  $masteryColumn[$y]['mastery'] ?> mastery-active" champion-tip api-type="masteries" api-primary-id="<?php echo  $masteryColumn[$y]['mastery'] ?>" api-secondary-id="<?php echo $masteryColumn[$y]['points']-1 ?>">
                <div class="points">
                    <?php  for($p = 0; $p < $masteryColumn[$y]['points']; $p++){ ?>
                    <div class="point"></div>
                    <?php  } ?>
                </div>
            </div>
            <?php  } else { ?>
            <div class="mastery-icon mastery-<?php echo  $masteryColumn[$y]['mastery'] ?>" champion-tip api-type="masteries" api-primary-id="<?php echo  $masteryColumn[$y]['mastery'] ?>">
            </div>
            <?php  } ?>
            <?php  if($z % 2 == 1 && $y == 0){ ?>
            <div class="mastery-spacer"></div>
            <?php  } ?>
            <?php  } ?>
            <?php  } ?>
        </div>
        <?php  } ?>
    </div>
    <?php  } ?>
</div>
<div class="build-text">
    <strong><?php echo  $championData['masteries']['highestWinPercent']['winPercent'] ?>%</strong> 胜率 | <strong><?php echo  $championData['masteries']['highestWinPercent']['games'] ?></strong> 局比赛
</div>
