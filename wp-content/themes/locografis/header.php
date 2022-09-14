<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="HandheldFriendly" content="True">
  <meta name="MobileOptimized" content="320">
  <meta name="apple-touch-fullscreen" content="yes" />

  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width,height=device-height">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  
  <title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
  <?php 
    $meta_desc = get_option( 'blogdescription', '' );
    $meta_keywords = get_option( 'meta_keywords', '' );

    if($meta_desc!=''){
      ?>
        <meta property="og:description" content="<?php echo $meta_desc; ?>" />
        <meta name="description" content="<?php echo $meta_desc; ?>">
      <?php
    }

    if($meta_keywords!=''){
      ?>
        <meta name="keywords" content="<?php echo $meta_keywords; ?>">
      <?php
    }
  ?> 
  <meta property="og:url" content="" /> 
  <meta property="og:title" content="<?php bloginfo('name'); ?><?php wp_title(); ?>" /> 
  
  <meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/favicon/16x16.png<?php echo $v; ?>" />

  <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,600,700" rel="stylesheet">
  <?php wp_head(); ?>
  <?php $v = '?v=1.0.0'.time(); ?>

  <link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_template_directory_uri(); ?>/favicon/57x57.png<?php echo $v; ?>">
  <link rel="apple-touch-icon" sizes="60x60" href="<?php echo get_template_directory_uri(); ?>/favicon/60x60.png<?php echo $v; ?>">
  <link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_template_directory_uri(); ?>/favicon/72x72.png<?php echo $v; ?>">
  <link rel="apple-touch-icon" sizes="76x76" href="<?php echo get_template_directory_uri(); ?>/favicon/76x76.png<?php echo $v; ?>">
  <link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_template_directory_uri(); ?>/favicon/114x114.png<?php echo $v; ?>">
  <link rel="apple-touch-icon" sizes="120x120" href="<?php echo get_template_directory_uri(); ?>/favicon/120x120.png<?php echo $v; ?>">
  <link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_template_directory_uri(); ?>/favicon/144x144.png<?php echo $v; ?>">
  <link rel="apple-touch-icon" sizes="152x152" href="<?php echo get_template_directory_uri(); ?>/favicon/152x152.png<?php echo $v; ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/favicon/180x180.png<?php echo $v; ?>">
  <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo get_template_directory_uri(); ?>/favicon/196x196.png<?php echo $v; ?>">
  
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_template_directory_uri(); ?>/favicon/16x16.png?v=1.2">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_template_directory_uri(); ?>/favicon/32x32.png<?php echo $v; ?>">
  <!--<link rel="icon" type="image/png" sizes="96x96" href="<?php echo get_template_directory_uri(); ?>/favicon/96x96.png<?php echo $v; ?>">-->
  
  
  <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/favicon/manifest.json<?php echo $v; ?>">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/favicon/32x32.png<?php echo $v; ?>">
  <meta name="theme-color" content="#ffffff">




  <meta name="google-site-verification" content="Znb44ea7YHSAsIegAQM7GlpcP8clmIglLCIbBNk8Jmg" />
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-92623410-1', 'auto');
    ga('send', 'pageview');

  </script>
</head>
<body class="body page-index <?php if (is_front_page()) echo 'state-menu-transparent' ?> clearfix">
<?php 
if (is_front_page()) get_template_part( 'content', 'slide' );

$opt = get_option('my_option_name');
$arr = array('facebook','instagram');
$has_sosmed =  false;
$item_sosmed = '';
foreach ($arr as $key => $sosmed) {
  if(isset($opt[$sosmed]) && !empty($opt[$sosmed])){
    $item_sosmed .= '<li class="link-'.$sosmed.'"><a href="'.$opt[$sosmed].'" target="_blank"><img src="'.get_template_directory_uri().'/images/'.$sosmed.'.svg"></a></li>';
    $has_sosmed = true;
  }
}
?>

<!-- Header Start -->
<div id="mobilenav" class="mobilenav">
  <div class="middle-mv">
    <div class="center">
      <?php 
        wp_nav_menu( 
              array(
                  'menu' => 'Header',
                  'menu_id'=> 'menu-mobile',
                  'menu_class'=>'mobile-menu clearfix',
                  'link_before'     => '<span>',
                  'link_after'      => '</span>',
              ) 
          );


        if($has_sosmed){?>
          <div class="sosmed-mobile"><ul class="sosmed-mobile-table"><?php echo $item_sosmed; ?></ul></div>
          <?php 
        }
      ?>


    </div>  
  </div>  
</div>


<div id="header" class="header clearfix <?php if (is_front_page()) echo 'header-state-menu-transparent' ?>" data-rel= "<?php if (is_front_page()) echo 'state-menu-transparent' ?>">
  <a href="<?php echo get_site_url(); ?>" class="logo-header fleft"></a>
  
  <div class="menu-mobile fright">
    <a href="javascript:void(0)" class="icon">
      <div class="hamburger">
        <div class="menui top-menu"></div>
        <div class="menui mid-menu"></div>
        <div class="menui bottom-menu"></div>
      </div>
    </a>
  </div>
  <div class="menu-item-header fright">
  <?php 
    

    if($has_sosmed){?>
    <div class="sosmed-header fright"><ul class="sosmed-header-table"><?php echo $item_sosmed; ?></ul></div>
    
    <?php }
  ?>
  <?php wp_nav_menu( array('menu' => 'Header','menu_class'=>'menu-header clearfix','link_before'=> '<span>','link_after'=> '</span>',) ); ?>
  </div>

  <?php wp_nav_menu( array('menu' => 'Slide','menu_class'=>'menu-smt clearfix','link_before'=> '<span>','link_after'=> '</span>',) ); ?>
</div>
<!-- Header End -->
