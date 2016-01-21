<? if(!defined('IN_WP')) exit('Access Denied');?>
<? include $this->getObj('header'); ?>

<div ng-controller="generalChampion">
    <div class="champion-area" ng-controller="championData">
        <div class="container-fluid">
            <div class="row">

                <div class="col-xs-12 col-sm-3 col-md-2 champion-profile">
                    <? include $this->getObj("champion/champion_image_roles"); ?>
                </div>

                <div class="col-xs-12 col-sm-9 col-md-4 champion-statistics">
                    <? include $this->getObj("champion/champion_statistics"); ?>
                </div>

                <div class="col-xxs-12 col-xs-6 col-sm-6 col-md-3">
                    <? include $this->getObj("champion/winrate_playrate_damage_advert_trinket"); ?>
                </div>

                <div class="col-xxs-12 col-xs-6 col-sm-6 col-md-3">
                    <? include $this->getObj("champion/gamelength_experience_summoners"); ?>
                </div>

            </div>
        </div>
    </div>

    <div class="champion-area">
        <div class="container-fluid">

            <div class="row">

                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div>
                        <?php if ($championData['skills']['highestWinPercent']['winPercent']) { ?>
                        <? include $this->getObj("champion/skill_order"); ?>
                        <?php } ?>
                    </div>
                    <div>
                        <?php if ($championData['runes']['highestWinPercent']) { ?>
                        <? include $this->getObj("champion/runes"); ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="row" style="margin-top:0px">
                        <div class="col-xs-12 col-sm-12 col-md-7">
                            <?php if ($championData['items']['highestWinPercent']) { ?>
                            <? include $this->getObj("champion/core_build"); ?>
                            <?php } ?>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-5">
                            <? include $this->getObj("champion/first_items"); ?>
                        </div>
                    </div>
                    <div class="row">
                        <?php if ($championData['masteries']['mostGames']['masteries'][0]) { ?>
                        <? include $this->getObj("champion/masteries"); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="matchups">
        <? include $this->getObj("champion/counters_matchups"); ?>
    </div>


</div>

<script>
    matchupData.champion = <?php echo json_encode($champion) ?>;
    matchupData.generalRole = <?php echo json_encode($generalRole) ?>;
    matchupData.championData = <?php echo json_encode($championData) ?>;
    matchupData.patchHistory = <?php echo  json_encode($overallStats['patchHistory']) ?>;
    /* Has general data on the chosen champion */
</script>
<? include $this->getObj('scripts'); ?>
<? include $this->getObj('footer'); ?>

