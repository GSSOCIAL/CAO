<div class="estate_postbox">
<div id="wp-data" data-homedir="<?=get_template_directory_uri();?>"></div>

<div class="col_left">
a</div>
<div class="col_right">
	<div id="cao_block-geo">
		<label class="cao">Геоданные</label>
		<div class="cao_block-content input input-col3">
			<div id="cao_map" data-latLng="<?=get_post_meta($post->ID,'latLng',1);?>">Map</div>
			<div class="geo_info">
				<input id="cao_city" type="text" value="<?=get_post_meta($post->ID,'city',1);?>" name="extra[city]" placeholder="Город"></input>
				<input id="cao_streetname" type="text" value="<?=get_post_meta($post->ID,'streetname',1);?>" name="extra[streetname]" placeholder="Улица"></input>
				<input id="cao_streetnumber" type="text" value="<?=get_post_meta($post->ID,'streetnumber',1);?>" name="extra[streetnumber]" placeholder="Дом"></input>
			</div>
			<input id="cao_autoabbr_street" type="checkbox" value="" name="">Автоматически сокращать название улицы и дома</input>
		</div>
		<label class="cao">Информация поблизости</label>
		<div class="cao_block-content">
			<div class="row">
				<h3>Метро</h3>
				<select id="cao_map_undergrounds"><option value="null">Не показывать</option></select>
			</div>
		</div>	
	</div>
</div>




<!--DATA-->
<input id="cao_latLng" type="hidden" value="<?=get_post_meta($post->ID,'latLng',1);?>" name="extra[latLng]">

<input id="cao_autoabbr_street_hidden" type="hidden" value="<?=get_post_meta($post->ID,'cao_autoabbr_street',1);?>" name="extra[cao_autoabbr_street]">
<input id="cao_undergound_name" type="hidden" value="<?=get_post_meta($post->ID,'metro',1);?>" name="extra[metro]">
<input id="cao_undergound_dis" type="hidden" value="<?=get_post_meta($post->ID,'metrodis',1);?>" name="extra[metrodis]">
<input id="cao_specs" type="hidden" value="<?=get_post_meta($post->ID,'specs',1);?>" name="extra[specs]">
</div>