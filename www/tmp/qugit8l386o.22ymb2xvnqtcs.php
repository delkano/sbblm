<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php if($page['title']): ?><?php echo $page['title']; ?> - <?php endif ?><?php echo $cfg['name']; ?></title>
        <meta name="description" content="<?php echo $page['description']; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->

        <link rel="stylesheet" href="<?php echo $BASE; ?>/css/normalize.css">
        <link rel="stylesheet" href="<?php echo $BASE; ?>/css/chosen.min.css">
        
<?php \Assets::instance()->addNode(array('type'=>'css','src'=>'less/main.less',)); ?>

        <script src="<?php echo $BASE; ?>/js/vendor/modernizr-2.8.3.min.js"></script>
    <!-- assets-head -->
</head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <header>
            <a href="<?php echo $BASE; ?>/">
                <div id='title'><img src="<?php echo $BASE; ?>/<?php echo $cfg['logo']; ?>" id="logo" /><h1><?php echo $cfg['name']; ?></h1></div>
            </a>
            
            <nav id="main-menu">
                <ul>
                    <li><a href="<?php echo \Base::instance()->alias('coach_list'); ?>" title="Coaches"><?php echo $LANG['menu']['coaches']; ?></a>
                    <?php if($MANAGER): ?> 
                        <li><a href="<?php echo \Base::instance()->alias('config'); ?>" title="Configuration"><?php echo $LANG['menu']['config']; ?></a>
                    <?php endif ?>
                    <?php if($LOGGEDIN): ?> 
                        <li><a href="<?php echo \Base::instance()->alias('coach_profile'); ?>" title="Profile"><?php echo $LANG['menu']['profile']; ?></a>
                        <li><a href="<?php echo \Base::instance()->alias('logout'); ?>" title="Logout"><?php echo $LANG['menu']['logout']; ?></a>
                    <?php else: ?>
                        <li><a href="<?php echo \Base::instance()->alias('login'); ?>" title="Login"><?php echo $LANG['menu']['login']; ?></a>
                    <?php endif ?>
                </ul>
            </nav>
        </header>
        <div class="container" id="main">

            
<?php if ($ERROR && $ERROR['code']!=400) echo $this->render('error.html',$this->mime,get_defined_vars(),0); ?>


            
<?php if (isset($page['template'])) echo $this->render('templates/'.$page['template'].'.html',$this->mime,get_defined_vars(),0); ?>


        </div>
        <footer>
            
        </footer>

        <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.12.0.min.js"><\/script>')</script>
        <script src="<?php echo $BASE; ?>/js/vendor/less.min.js"></script>
        <script src="<?php echo $BASE; ?>/js/vendor/jquery.singleuploadimage.js"></script>
        <script src="<?php echo $BASE; ?>/js/vendor/chosen.jquery.min.js"></script>
        
<?php \Assets::instance()->addNode(array('src'=>'js/plugins.js','type'=>'js')); ?>

        
<?php \Assets::instance()->addNode(array('src'=>'js/main.js','type'=>'js')); ?>


        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <!--
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X','auto');ga('send','pageview');
        </script>
        -->
    
<!-- assets-footer -->
</body>
</html>
