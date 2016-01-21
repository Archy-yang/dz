<? if(!defined('IN_WP')) exit('Access Denied');?>
<div class="container-fluid"  ng-controller="matchupData">

    <div class="matchup-wrapper">
        <div class="matchup-settings">
            <span>显示至少对阵过</span>
            <select class="minimum-games" ng-change="saveMinGames()" ng-model="minGames">
                <option value="0">不限</option>
                <option value="100">100+</option>
                <option value="250">250+</option>
                <option value="500">500+</option>
                <option value="1000">1000+</option>
                <option value="10000">10000+</option>
            </select>
            <span>局的英雄</span>
        </div>
    </div>

    <div class="row counter-row">

        <div class="col-xs-12 col-sm-12 col-md-6 counter-column">
            <div class="matchup-header">
                <h2>最克制<?php echo  $champion['name'] ?>的<?php echo  $champion['roleTitle'] ?>英雄</h2>
                <span class="glyphicon glyphicon-question-sign" tooltip="The coloured bars below show <?php echo  $champion['name'] ?>'s performance in the matchup. Red = Weak, Yellow = Even, Green = Strong."></span>
            </div>
            <filters matchup-type="matchups"></filters>
            <matchups order="" matchup-type="matchups"></matchups>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="matchup-header">
                <h2>最被<?php echo  $champion['name'] ?>克制的<?php echo  $champion['roleTitle'] ?>英雄</h2>
                <span class="glyphicon glyphicon-question-sign" tooltip="The coloured bars below show <?php echo  $champion['name'] ?>'s performance in the matchup. Green = Strong, Yellow = Even, Red = Weak."></span>
            </div>
            <filters matchup-type="matchups"></filters>
            <matchups order="-" matchup-type="matchups"></matchups>
        </div>

    </div>

    <?php if ($champion['role'] == 'DUO_SUPPORT' || $champion['role'] == 'DUO_CARRY') { ?>

    <div class="row counter-row">
        <div class="col-xs-12 col-sm-12 col-md-6 counter-column">
            <div class="matchup-header">
                <h2>最克制<?php echo  $champion['name'] ?>的<?php if ($champion['role'] == 'DUO_SUPPORT') { ?> ADC <?php  } else { ?> Support <?php  } ?></h2>
            </div>
            <filters matchup-type="adcsupport"></filters>
            <matchups order="" matchup-type="adcsupport"></matchups>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="matchup-header">
                <h2>最被<?php echo  $champion['name'] ?>克制的<?php if ($champion['role'] == 'DUO_SUPPORT') { ?> ADC <?php  } else { ?> Support <?php  } ?></h2>
            </div>
            <filters matchup-type="adcsupport"></filters>
            <matchups order="-" matchup-type="adcsupport"></matchups>
        </div>
    </div>

    <div class="row counter-row">
        <div class="col-xs-12 col-sm-12 col-md-6 counter-column">
            <div class="matchup-header">
                <h2>与<?php echo  $champion['name'] ?>搭配胜率最低的<?php if ($champion['role'] == 'DUO_SUPPORT') { ?> ADC <?php  } else { ?> Support <?php  } ?></h2>
            </div>
            <filters matchup-type="synergy"></filters>
            <matchups order="" matchup-type="synergy"></matchups>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="matchup-header">
                <h2>与<?php echo  $champion['name'] ?>搭配胜率最高的<?php if ($champion['role'] == 'DUO_SUPPORT') { ?> ADC <?php  } else { ?> Support <?php  } ?></h2>
            </div>
            <filters matchup-type="synergy"></filters>
            <matchups order="-" matchup-type="synergy"></matchups>
        </div>
    </div>

    <?php  } ?>

</div>

<script type="text/ng-template" id="filters.html">
    <div class="clearfix">
        <div class="search-sorter">
            <input class="matchupSearch" type="text" ng-model="search[matchupType].name" ng-model-options="{debounce: 30}" placeholder="Search By Name" />
        </div>
        <div ng-click="saveSort('statScore')" class="stat-sorter" ng-class="{'selected-sorter': sortExpression.sortBy === 'statScore'}">
            统计排名 <span class="glyphicon glyphicon-question-sign" tooltip="Statistical rating takes more than win rate into account. Click the graph button under the champion name for more details."></span>
        </div>
        <div ng-click="saveSort('winRate')" class="winrate-sorter" ng-class="{'selected-sorter': sortExpression.sortBy === 'winRate'}">
            {{champion.name}}的胜率
        </div>
    </div>
</script>



<script type="text/ng-template" id="matchups.html">

    <div ng-repeat="matchup in filtered[matchupType] = (allMatchups[matchupType] | orderBy:[order+sortExpression.sortBy,order+'overallScore'] | startsWith:search[matchupType].name | filter:matchAmount) | limitTo:itemsLimit(matchupType,order)" ng-controller="specificMatchup" class="animate-repeat" ng-class="{'left': order === '', 'right': order === '-'}">
        <div class="row counter-matchups">

            <div class="matchup-champion-info">
                <a href="/champion/{{matchup.key}}/{{generateChampUrl(matchupType)}}">
                    <div class="matchup-champion {{matchup.key}}"></div>
                    <h3>{{matchup.title}}</h3>
                </a>
                <span class="glyphicon glyphicon-stats view-more-stats" tooltip="View Indepth Stats" ng-click="toggleMatchup(matchupType, matchup.key)"></span>
                <a href="{{generateMatchupUrl(matchup.key, matchupType)}}">
                    <span class="glyphicon glyphicon-link view-linked-page" tooltip="View Matchup Page"></span>
                </a>
            </div>

            <div class="matchup-stats">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" ng-class="{'above-average': matchup.statScore > 6, 'below-average': matchup.statScore < 4, 'average': matchup.statScore >= 4 && matchup.statScore <= 6}" ng-style="{'width': order==='' ? (10-matchup.statScore)*10+'%' : matchup.statScore*10+'%'}">
                    </div>
                </div>
                <small><strong>{{matchup.games}}</strong> Games </small>
            </div>

            <div class="winrating-area">
                <strong>{{matchup.winRate}}%</strong>
            </div>
        </div>

        <div class="row" ng-if="matchupType === 'matchups' && showMatchups && matchupRetrieved">
            <div class="col-xxs-12 col-xs-6">
                <h4 ng-if="showMatchups && matchupRetrieved"> 交战数据对比 </h4>
                <div class="middle-graphic-holder">
                    <canvas ng-if="showMatchups && matchupRetrieved" tc-chartjs-radar chart-data="champComparison.data" chart-options="champComparison.settings" chart-legend="championMatchup" height="400" width="560"></canvas>
                </div>
            </div>
            <ul class="radar-legend visible-xxs">
                <li><span class="magic-dmg"></span>{{champion.name}}</li>
                <li><span class="physical-dmg"></span>{{matchup.name}}</li>
            </ul>
            <div class="col-xxs-12 col-xs-6">
                <h4 ng-if="showMatchups && matchupRetrieved"> 总金钱 </h4>
                <div class="middle-graphic-holder">
                    <canvas ng-if="showMatchups && matchupRetrieved" tc-chartjs-line chart-data="goldIncome.data" chart-options="goldIncome.settings" height="400" width="560"> </canvas>
                </div>
            </div>
            <ul class="radar-legend">
                <li><span class="magic-dmg"></span>{{champion.name}}</li>
                <li><span class="physical-dmg"></span>{{matchup.name}}</li>
            </ul>
        </div>

        <div class="row chart-area matchup-stat-area" ng-if="showMatchups">
            <div class="loading-symbol" ng-if="!matchupRetrieved">读取中...</div>

            <div class="col-xs-12 col-sm-12 col-md-12" ng-if="matchupRetrieved">
                <div class="matchup-title-width matchup-div-header"></div>
                <div class="matchup-champ-img-width matchup-div-header">
                    <div class="matchup-champion matchup-table {{champion.key}}"></div>
                </div>
                <div class="matchup-champ-img-width matchup-div-header">
                    <div class="matchup-champion matchup-table {{matchup.key}}"></div>
                </div>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="matchup-title-width" >类型</th>
                            <th class="matchup-values-width">对阵数据</th>
                            <th class="matchup-values-width">变化</th>
                            <th class="matchup-values-width">对阵数据</th>
                            <th class="matchup-values-width">变化</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="matchupInfo in matchup.specificMatchup.general">
                            <td class="matchup-title-width">
                                {{matchupInfo.title}}
                            </td>
                            <td class="matchup-values-width">
                                {{matchupInfo[champtype(matchup.key, true)].val}}<span ng-if="$index===0">%</span>
                            </td>
                            <td class="matchup-values-width">
                                <span class="glyphicon {{matchupInfo.title}}-title" ng-class="{'glyphicon-arrow-up': matchupInfo[champtype(matchup.key, true)].change > (0), 'glyphicon-arrow-down': matchupInfo[champtype(matchup.key, true)].change < 0}"></span>
                                {{Math.abs(matchupInfo[champtype(matchup.key, true)].change)}}
                            </td>
                            <td class="matchup-values-width">
                                {{matchupInfo[champtype(matchup.key)].val}}<span ng-if="$index===0">%</span>
                            </td>
                            <td class="matchup-values-width">
                                <span class="glyphicon {{matchupInfo.title}}-title" ng-class="{'glyphicon-arrow-up': matchupInfo[champtype(matchup.key)].change > (0), 'glyphicon-arrow-down': matchupInfo[champtype(matchup.key)].change < 0}"></span>
                                {{Math.abs(matchupInfo[champtype(matchup.key)].change)}}</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="show-more" ng-if="hasMoreItemsToShow(matchupType,order)" ng-click="showMoreItems(matchupType,order)">
    <small>{{itemsLimit(matchupType,order)}} / {{matchupsCount(matchupType)}} - </small><strong>查看更多</strong>
</div>
</script>
