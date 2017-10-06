<?php
get_header(); 
the_post();
?>

<div class="content centred">
<div class="article-head">
	<div class="area_left">
		<h1><?=the_title('','',true);?></h1>
	</div>
</div>
<div class="article-content">
	<div class="area_middle">
		<?=the_content();?>
	</div>
</div>
</div>
<?php wp_footer();?>
