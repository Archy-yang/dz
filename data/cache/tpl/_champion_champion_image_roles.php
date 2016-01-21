<? if(!defined('IN_WP')) exit('Access Denied');?>
<img src="http://ossweb-img.qq.com/images/lol/img/champion/<?php echo $champion['key'] ?>.png" class="champ-img"/>
<h1><?php echo $champion['name'] ?></h1>
<ul>

  <?php  foreach($champion['roles'] as $roles) {?>
  <li <?php if ($champion['role'] === $roles['role']) { echo 'class="selected-role"'; } ?>>
    <a href="?m=champion&champKey=<?php echo  $champion['key'] ?>&role=<?php echo $roles['title'] ?>">
      <h3>
      <?php  if ($roles['title'] === 'Top'){ ?>
        上单
      <?php } else if ($roles['title'] === 'Jungle') { ?>
        打野
      <?php } else if ($roles['title'] === 'Support') { ?>
        辅助
      <?php } else if ($roles['title'] === 'Middle') { ?>
        中单
      <?php } else if ($roles['title'] === 'ADC') { ?>
        ADC
      <?php } else { ?>
        <?php echo $roles['title'] ?>
      <?php  } ?>
      </h3>
    </a>
    <small> 占比 <?php echo $roles['percentPlayed'] ?>%  </small>
    <small style="display:block"> 当前版本<?php echo $roles['games'] ?>局 </small>
  </li>
  <?php  } ?>

  <?php  if ($champion['name'] === 'Viktor'){ ?>
    <? include $this->getObj("champion/viktor_upgrade"); ?>
  <?php  } ?>
</ul>
