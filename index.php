<?php get_header(); ?>
<?php
	global $wpdb;
	//Get low-high
	if($wpdb->query("SELECT 1 FROM cao_config LIMIT 1") == false){
		$wpdb->query("CREATE TABLE cao_config (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, user INT(6), cfg VARCHAR(50), value VARCHAR(50))");
	}
	$query = $wpdb->get_results("SELECT `id`,`value`,`cfg` FROM `cao_config` WHERE `cfg` IN ('pricelow','pricehigh','arealow','areahigh')",ARRAY_A);
	$old = array();
	foreach($query as $qitem){
		$old[$qitem['cfg']] = array();
		$old[$qitem['cfg']]['id'] = $qitem['id'];
		$old[$qitem['cfg']]['value'] = $qitem['value'];
	}
?>
<div class="hidden" id="_data" data-template="<?=get_template_directory_uri();?>"></div>
<div class="front_page">
	<div id="map" class="front_home">
		<div class="overlay">
			<div class="search"><input type="text" placeholder="Найти..."><div class="filters icon icon-filters"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve"><style type="text/css">.st0{fill:#C9C9C9;transition:0.3s ease-out;}</style><rect id="l-1" x="1" class="st0" width="2" height="16"/><rect id="pl-1" y="3" class="st0" width="4" height="2"/><rect id="l-2" x="7" class="st0" width="2" height="16"/><rect id="pl-2" x="6" y="10" class="st0" width="4" height="2"/><rect id="l-3" x="13" class="st0" width="2" height="16"/><rect id="pl-3" x="12" y="6" class="st0" width="4" height="2"/></svg></div></div>
			<div class="overlay-content">
				<?php
				$hots = get_posts(array('post_type' => 'estate','numberposts' => -1,'tax_query' => array(array('taxonomy' => 'hot_offers','field' => 'slug','terms' => 'hot_offers','include_children' => false))));
				if(count($hots)>0){?>
					<div class="over-block hot-offers">
						<div class="block-title"><div class="icon"><?=file_get_contents(get_template_directory_uri().'/assets/images/icon-fire.svg');?></div> Горячие предложения</div>
						<?php
							foreach($hots as $hotpost){
								$TDATE = get_post_meta($hotpost->ID,'hot_until',1);
								$PRICE = get_post_meta($hotpost->ID,'hot_price',1);
								if(strlen($PRICE) == 0){$PRICE = get_post_meta($hotpost->ID,'price',1);}
								if(strlen($TDATE) > 0){
									$TDATE = explode('-',$TDATE);
									if(comparedate($TDATE,null) == true){
										$TDATE = $TDATE[2].".".$TDATE[1];
									}else{
										continue;
									}
								}
								?>
								<a href="<?=$hotpost->guid?>" title="<?=$hotpost->post_title?>"><div class="item item-hot">
									<div class="picture"><?php if($thum = get_the_post_thumbnail($hotpost->ID)){
                                        echo($thum);
                                    } ?></div>
									<div class="title"><?=$hotpost->post_title?></div>
									<?php if($TDATE){ ?><p>Предложение действительно до <?=$TDATE?></p> <?php } ?>
									<div class="btm">
										<div class="price"><?=$PRICE?></div>
									</div>
								</div></a>
							<?php }
						?>
					</div>
				<?php }
				?>
					<div class="over-block block-filters">
					<div class="block-title"><div class="icon"></div>Фильтры</div>
						<div class="row">
							<label>Стоимость</label>
							<div data-filter="price" id="range" data-min="<?=$old['pricelow']['value']?>" data-max="<?=$old['pricehigh']['value']?>"></div>
						</div>
						<div class="row">
							<label>Площадь</label>
							<div data-filter="area" id="range" data-min="<?=$old['arealow']['value']?>" data-max="<?=$old['areahigh']['value']?>"></div>
						</div>
						<div class="btn fill red submit">Применить</div>
					</div>
					<div class="over-block search-results"></div>
			</div>
		</div>
		<div class="map"></div>
	</div>

	<div class="content">
	</div>
</div>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCK5iuCq4O-0k--Co16Uu2xN43hXZp2Jqg&callback=initMap"></script>
<?php get_footer();
