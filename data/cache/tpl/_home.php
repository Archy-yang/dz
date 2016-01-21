<? if(!defined('IN_WP')) exit('Access Denied');?>
<? include $this->getObj('header'); ?>
<div class="container-fluid">
    <div class="row summary">
        <div class="win-summary summary-side" style="border-right: 1px solid #f2f2f2;">
            <span class="champion-name champion-list-header"><a href="statistics/#?sortBy=general.winPercent&order=descend"><span class="summary-highlight">
            胜率</span></a></span>

            <table class="table table-striped">
              <thead>
                <tr >
                  <th class="first-summary">位置</th>
                  <th class="second-summary"><span class="top-half">最高胜率</span></th>
                  <th class="third-summary"><span class="bottom-half">最低胜率</span></th>
                </tr>
              </thead>
              <tbody>
                  <?php for($y = 0; $y < count($summaries); $y++) { ?>
                 <tr>
                  <td><a href="/#?a=statistic&ssortBy=general.winPercent&order=descend&roleSort=<?php echo  $summaries[$y]['title']; ?>">
                  <?php  if ($summaries[$y]['title'] === 'Top'){  ?>
                    上单
                  <?php } else if ($summaries[$y]['title'] === 'Jungle') {  ?>
                    打野
                  <?php } else if ($summaries[$y]['title'] === 'Support') {  ?>
                    辅助
                  <?php } else if ($summaries[$y]['title'] === 'Middle') {  ?>
                    中单
                  <?php } else if ($summaries[$y]['title'] === 'ADC') {  ?>
                    ADC
                  <?php } else {  ?>
                    <?php echo  $summaries[$y]['title']  ?>
                  <?php  }  ?>
                  </a></td>
                  <td><a href="/champion/<?php echo $summaries[$y]['highestWinRate']['key'] ?>/<?php echo $summaries[$y]['title'] ?>">
                            <div class="matchup-champion <?php echo $summaries[$y]['highestWinRate']['key'] ?>"></div>
                                <span class="champion-name"><?php echo  $summaries[$y]['highestWinRate']['name']  ?></span>
                            </a>
                            <span class="top-half summary-value"><?php echo  $summaries[$y]['highestWinRate']['value']  ?>%</span>
                    </td>
                  <td>
                    <a href="/champion/<?php echo $summaries[$y]['lowestWinRate']['key'] ?>/<?php echo $summaries[$y]['title'] ?>">
                            <div class="matchup-champion <?php echo $summaries[$y]['lowestWinRate']['key'] ?>"></div>
                                <span class="champion-name"><?php echo  $summaries[$y]['lowestWinRate']['name']  ?></span>
                            </a>
                            <span class="bottom-half summary-value"><?php echo  $summaries[$y]['lowestWinRate']['value'] ?>%</span>
                  </td>
                  </tr>

                <?php  }  ?>
              </tbody>
            </table>
        </div>
        <div class="overall-summary summary-side" style="border-right: 1px solid #f2f2f2;position:relative">
            <span class="champion-name champion-list-header"><a href="statistics/#?sortBy=general.overallPosition&order=ascend"><span class="summary-highlight">总体表现排行</span></a></span><span class="glyphicon glyphicon-question-sign" style="position: absolute;right: 20px;top:20px;" tooltip="Overall Performance takes more than win rate into account - including play rate, ban rate, kda, gold, cs, damage and other role dependant stats."></span>
            <table class="table table-striped">
              <thead>
                <tr >
                  <th style="width:22%">位置</th>
                  <th style="width:39%"><span class="top-half">强势</span></th>
                  <th style="width:39%"><span class="bottom-half">乏力</span></th>
                </tr>
              </thead>
              <tbody>
                 <?php  for($l=0;$l<count($summaries);$l++){  ?>
                 <tr>
                  <td><a href="?a=statistics&sortBy=general.overallPosition&order=ascend&roleSort=<?php echo  $summaries[$l]['title']  ?>">
                  <?php  if ($summaries[$l]['title'] === 'Top'){  ?>
                    上单
                  <?php } else if ($summaries[$l]['title'] === 'Jungle') {  ?>
                    打野
                  <?php } else if ($summaries[$l]['title'] === 'Support') {  ?>
                    辅助
                  <?php } else if ($summaries[$l]['title'] === 'Middle') {  ?>
                    中单
                  <?php } else if ($summaries[$l]['title'] === 'ADC') {  ?>
                    ADC
                  <?php } else {  ?>
                    <?php echo  $summaries[$l]['title']  ?>
                  <?php  }  ?>
                  </a></td>
                  <td><a href="/champion/<?php echo $summaries[$l]['bestOverall']['key'] ?>/<?php echo $summaries[$l]['title'] ?>">
                            <div class="matchup-champion <?php echo $summaries[$l]['bestOverall']['key'] ?>"></div>
                            <span class="champion-name"><?php echo  $summaries[$l]['bestOverall']['name']  ?></span></a>

                            </td>
                  <td>
                    <a href="/champion/<?php echo $summaries[$l]['worstOverall']['key'] ?>/<?php echo $summaries[$l]['title'] ?>">
                            <div class="matchup-champion <?php echo $summaries[$l]['worstOverall']['key'] ?>"></div>
                            <span class="champion-name"><?php echo  $summaries[$l]['worstOverall']['name']  ?></span></a>
                  </td>
                  </tr>

                <?php  }  ?>
              </tbody>
            </table>

        </div>
        <div class="change-summary summary-side">
            <span class="champion-name champion-list-header"><a href="statistics/#?sortBy=general.overallPositionChange&order=descend"><span class="summary-highlight">英雄排名变化</span></a></span>

            <table class="table table-striped">
              <thead>
                <tr >
                  <th class="first-summary">位置</th>
                  <th class="second-summary"><span class="top-half">上升最多</span></th>
                  <th class="third-summary"><span class="bottom-half">下降最多</span></th>
                </tr>
              </thead>
              <tbody>
                 <?php  for($x=0;$x<count($summaries);$x++){  ?>
                 <tr>
                  <td><a href="?a=statistics&sortBy=general.overallPositionChange&order=descend&roleSort=<?php echo  $summaries[$x]['title']  ?>">
                  <?php  if ($summaries[$x]['title'] === 'Top'){  ?>
                    上单
                  <?php } else if ($summaries[$x]['title'] === 'Jungle') {  ?>
                    打野
                  <?php } else if ($summaries[$x]['title'] === 'Support') {  ?>
                    辅助
                  <?php } else if ($summaries[$x]['title'] === 'Middle') {  ?>
                    中单
                  <?php } else if ($summaries[$x]['title'] === 'ADC') {  ?>
                    ADC
                  <?php } else {  ?>
                    <?php echo  $summaries[$x]['title']  ?>
                  <?php  }  ?>
                  </a></td>
                  <td><a href="/champion/<?php echo $summaries[$x]['mostImproved']['key'] ?>/<?php echo $summaries[$x]['title'] ?>">
                            <div class="matchup-champion <?php echo $summaries[$x]['mostImproved']['key'] ?>"></div>
                            <span class="champion-name"><?php echo  $summaries[$x]['mostImproved']['name']  ?></span></a>
                            <span class="top-half summary-value"><span class="glyphicon  glyphicon-arrow-up"></span> <?php echo $summaries[$x]['mostImproved']['difference']  ?></span>
                            </td>
                  <td>
                    <a href="/champion/<?php echo $summaries[$x]['leastImproved']['key'] ?>/<?php echo $summaries[$x]['title'] ?>">
                            <div class="matchup-champion <?php echo $summaries[$x]['leastImproved']['key'] ?>"></div>
                            <span class="champion-name"><?php echo  $summaries[$x]['leastImproved']['name']  ?></span></a>
                            <span class="bottom-half summary-value"><span class="glyphicon  glyphicon-arrow-down"></span> <?php echo $summaries[$x]['leastImproved']['difference']  ?> </span>
                  </td>
                  </tr>

                <?php  }  ?>
              </tbody>
            </table>
        </div>
    </div>



    <div class="row" style="margin-top:40px" id="home">
        <span class="champion-name champion-list-header">选择一个英雄查看它的深度数据和克制英雄</span>
        <div class="col-md-9 clearfix" style="margin-top:15px;">
        <?php  for($i=0;$i<count($data);$i++){  ?>
            <div class="champ-height">
                <div class="champ-index-img <?php echo  $data[$i]['key']  ?>">
                <a href="?m=champion&champKey=<?php echo  $data[$i]['key']  ?>">
                <div class="home-champion <?php echo  $data[$i]['key']  ?>"></div>
                <span class="champion-name"><?php echo  $data[$i]['name']  ?></span>
                </a>
                    <?php  foreach($data[$i]['roles'] as  $roles) {  ?>
                        <a href="?m=champion&champKey=<?php echo  $data[$i]['key']  ?>&role=<?php echo  $roles['title']  ?>" style="display:block">
                          <?php  if ($roles['title'] === 'Top'){  ?>
                            上单
                          <?php } else if ($roles['title'] === 'Jungle') {  ?>
                            打野
                          <?php } else if ($roles['title'] === 'Support') {  ?>
                            辅助
                          <?php } else if ($roles['title'] === 'Middle') {  ?>
                            中单
                          <?php } else if ($roles['title'] === 'ADC') {  ?>
                            ADC
                          <?php } else {  ?>
                            <?php echo  $roles['title']  ?>
                          <?php } ?>
                        </a>
                    <?php  }  ?>
                </div>
            </div>
        <?php  }  ?>
        </div>
        <div class="col-md-3">
        </div>


    </div>
</div>
<? include $this->getObj('scripts'); ?>
<? include $this->getObj('footer'); ?>
