<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
<link rel="stylesheet/less" type="text/css" href="<?php bloginfo("template_url"); ?>/style.less" />
<link type="text/css" href="<?php bloginfo("template_url"); ?>/fs.css" />
<script type="text/javascript" src="<?php bloginfo("template_url"); ?>/assets/less.js"></script>

<script type="text/javascript" src="<?php bloginfo("template_url"); ?>/assets/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="<?php bloginfo("template_url"); ?>/assets/js/fs.js"></script>
<script type="text/javascript" src="<?php bloginfo("template_url"); ?>/assets/core.js"></script>

</head>

<body <?php body_class(); ?>>
<div id="page" class="site">

	<div id="nav_extended">
		<div class="nbar">
			<div class="flr"><div class="title"></div><ul></ul></div>
		</div>
		<div class="nav-content"><div class="list"></div></div>
	</div>

	<header>
		<div class="nbar"><?php if($logo = get_custom_logo()){echo($logo);}else{echo('<div class="logo logo-official"><a href="'.get_home_url().'" title="'.get_bloginfo('name').'"><img src="'.get_template_directory_uri().'/assets/images/logo.svg" alt="'.get_bloginfo('name').'"></a></div>');}?></div>
		<div class="top-nav">
			<?php wp_nav_menu( array('theme_location' => 'top' )); ?>
		</div>
	</header>

