<? if(!defined('IN_WP')) exit('Access Denied');?>
<!doctype html>
<html lang="en" ng-app="<?php echo $appName? $appName:'core'  ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?php echo  pageData.description  ?>">
        <title>技术控 - <?php echo  pageData.title  ?></title>
        <!-- Bootstrap core CSS -->
        <link href="/dist/css/bootstrap.min.css" rel="stylesheet">
        <?php if(process.env.NODE_ENV === 'production') {  ?>
        <link href="/css/master.min.css?v=<?php echo core.resetCache ?>" rel="stylesheet">
        <?php } else {  ?>
        <link href="/css/master.css" rel="stylesheet">
        <link href="/css/sprite.css" rel="stylesheet">
        <?php }  ?>

        <script>
            var matchupData = {};
        </script>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <body>
        <div class="primary-hue">
            <div class="navigation-elem">
                <div class="inner-nav clearfix">
                    <ul class="nav navbar-nav">
                        <li class="first-button <?php if (pageData.name === 'home') {  ?> active <?php }  ?>"><a href="/">首页</a></li>
                        <li class="<?php if (pageData.name === 'stats') {  ?> active <?php }  ?>"><a href="/statistics/">统计</a></li>
                    </ul>
                    <div class="update-happening">
                        <?php if (core.headline) {  ?>
                        <span class="important-message"> <?php echo  core.headline  ?> </span>
                        <?php }  ?>
                    </div>
                </div>
            </div>
            <div class="main-container">
                <div class="navbar navbar-inverse" role="navigation">
                    <div class="search-holder">
                        <div class="search-fb-holder">
                            <div class="input-group" ng-controller="searchCtrl">

                                <input type="text" class="form-control" placeholder="输入英雄名称搜索" id="query" value="" ng-model="selected" ng-model-options="{debounce: 40}" typeahead="champ as champ.name for champ in championMenu | startsWith:$viewValue | limitTo: 12" typeahead-template-url="menu.html" style="width:90%;display: inline-block;" ng-keyup="determineSend($event.keyCode)" autocomplete="off">
                                <div class="input-group-btn" style="cursor:auto;display: inline-block;width:9%;">
                                    <button ng-click="getPage()" style="cursor:auto;" type="submit" class="btn btn-success"><span style="cursor:auto;" class="glyphicon glyphicon-search" ></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="analysis-holder">
                    <small>
                        版本
                        <strong><?php echo core.patch ?></strong>
                        <span class="spacer">|</span>
                        样本容量
                        <strong><?php echo core.championsAnalyzed ?></strong>
                        <span class="spacer">|</span>
                        <strong> Platinum+ Ranked </strong>
                    </small>
                </div>
                <div class="page-content">
                    <noscript>
                        <div class="update-happening">To make the most of this website, we strongly recommend turning JavaScript on! It powers the graphs, data sorting and other cool features!</div>
                    </noscript>
