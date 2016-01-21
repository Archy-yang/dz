<? if(!defined('IN_WP')) exit('Access Denied');?>
<h2 class="champion-stats">最常见的技能加点</h2>
<div class="skill-order clearfix">
    <div class="skill">
        <div class="img-placeholder"></div>
        <div class="skill-selections">
            <?php  for ($d = 0; $d < 18; $d++){ ?>
            <div>
                <span><?php echo  $d + 1 ?></span>
            </div>
            <?php  } ?>
        </div>
    </div>
    <?php  for ($i = 0 ; $i < 4; $i++){ ?>
    <div class="skill">
        <img src="//ddragon.leagueoflegends.com/cdn/<?php echo  CORE_DDPATCH ?>/img/spell/<?php echo  $championData['skills']['skillInfo'][$i]['img'] ?>" tooltip="<?php echo  $championData['skills']['skillInfo'][$i]['name'] ?>"/>
        <div class="skill-selections">
            <?php  $skillGames = $championData['skills']['mostGames']['order'] ?>
            <?php  for ($b = 0 ; $b < count($skillGames); $b++){ ?>
            <div class="<?php echo $skillGames[$b][0] == ($i+1) ? 'selected' : '' ?>">
                <span><?php echo  $skillGames[$b][0] == ($i+1) ? $championData['skills']['skillInfo'][$i]['key'] : "" ?></span>
                <?php  if(count($skillGames[$b]) == 3 && $skillGames[$b][0] == ($i+1)){ ?>
                <span><?php echo  $championData['skills']['skillInfo'][$skillGames[$b][2]-1]['key'] ?></span>
                <?php  } ?>
            </div>
            <?php  } ?>
        </div>
    </div>
    <?php  } ?>
</div>
<div class="build-text">
    <strong><?php echo  $championData['skills']['mostGames']['winPercent'] ?>%</strong> 胜率 | <strong><?php echo  $championData['skills']['mostGames']['games'] ?></strong> 局比赛
</div>


<h2 class="champion-stats" style="margin-top:55px">胜率最高的技能加点</h2>
<div class="skill-order clearfix">
    <div class="skill">
        <div class="img-placeholder"></div>
        <div class="skill-selections">
            <?php  for ($d = 0; $d < 18; $d++){ ?>
            <div>
                <span><?php echo  $d + 1 ?></span>
            </div>
            <?php  } ?>
        </div>
    </div>
    <?php  for ($i = 0 ; $i < 4; $i++){ ?>
    <div class="skill">
        <img src="//ddragon.leagueoflegends.com/cdn/<?php echo  CORE_DDPATCH ?>/img/spell/<?php echo  $championData['skills']['skillInfo'][$i]['img'] ?>" tooltip="<?php echo  $championData['skills']['skillInfo'][$i]['name'] ?>"/>
        <div class="skill-selections">
            <?php  $skillGames = $championData['skills']['highestWinPercent']['order'] ?>
            <?php  for ($b = 0 ; $b < count($skillGames); $b++){ ?>
            <div class="<?php echo $skillGames[$b][0] == ($i+1) ? 'selected' : '' ?>">
                <span><?php echo  $skillGames[$b][0] == ($i+1) ? $championData['skills']['skillInfo'][$i]['key'] : "" ?></span>
                <?php  if(count($skillGames[$b]) == 3 && $skillGames[$b][0] == ($i+1)){ ?>
                <span><?php echo  $championData['skills']['skillInfo'][$skillGames[$b][2]-1]['key'] ?></span>
                <?php  } ?>
            </div>
            <?php  } ?>
        </div>
    </div>
    <?php  } ?>
</div>
<div class="build-text">
    <strong> <?php echo  $championData['skills']['highestWinPercent']['winPercent'] ?>%</strong> 胜率 | <strong><?php echo  $championData['skills']['highestWinPercent']['games'] ?></strong> 局比赛
</div>
