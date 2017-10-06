<div class="estate_postbox">

<div id="wp-data" data-homedir="<?=get_template_directory_uri();?>"></div>
<div id="hot">
	<?php
		global $wpdb;
		if($wpdb->query("SELECT 1 FROM cao_config LIMIT 1") == false){
			$wpdb->query("CREATE TABLE cao_config (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, user INT(6), cfg VARCHAR(50), value VARCHAR(50))");
		}
		$settings = $wpdb->get_results("SELECT * FROM cao_config WHERE `user`=".get_current_user_id(),"ARRAY_A");
		$USER_SETTINGS = array();
		if(count($settings)>0){
			foreach($settings as $cfg){
				$USER_SETTINGS[$cfg['cfg']] = $cfg['value'];
			}

			if($USER_SETTINGS['faccess-price']){
				if($USER_SETTINGS['faccess-price'] == 'true'){ ?>
			<div class="block" id="price">
			<label class="cao">Стоимость</label>
			<div class="cao_block_content">
				<input type="text" value="<?=get_post_meta($post->ID,'price',1);?>" name="extra[price]" placeholder="0">
			</div>
			</div>
			<?php } }
			if($USER_SETTINGS['faccess-hot']){
				if($USER_SETTINGS['faccess-hot'] == 'true'){ ?>
			<div class="block" id="hot">
			<label class="cao">Горячее предложение</label>
			<div class="cao_block_content">
				<input type="text" value="<?=get_post_meta($post->ID,'hot',1);?>" name="extra[hot]" placeholder="0">
			</div>
			</div>
			<?php } }
			if($USER_SETTINGS['faccess-freed']){
				if($USER_SETTINGS['faccess-freed'] == 'true'){ ?>
			<div class="block" id="freed">
			<label class="cao">Освободится</label>
			<div class="cao_block_content">
				<input type="date" value="<?=get_post_meta($post->ID,'freed',1);?>" name="extra[freed]" placeholder="0">
			</div>
			</div>
			<?php } }

		}
		if(count($settings) == 0 || !$USER_SETTINGS['faccess']){?>
			<div class="nonce">
				<h3>Вы еще не настроили быстрый доступ</h3>
				<a onclick="CAO.modal.init('faccess');" href="#">Настроить сейчас</a>
			</div>
		<?php }
	?>
</div>
<div class="col_left">

	<div id="cao_block-manager">
		<label class="cao">Менеджер</label>
		<div class="cao_block-content">
		<?php
		$smanager = get_post_meta($post->ID,'manager',1);
		$manager_list = get_users(array('role' => 'manager','fields' => array('ID','display_name','user_url') ));
		foreach($manager_list as $manager){
			echo('<div class="item');
			if($manager->ID == $smanager){echo(' selected');}
			echo('" data-id="'.$manager->ID.'"><div class="pic">'.get_avatar($manager->ID,60).'</div><div class="hide-ex">'.$manager->display_name.'</div></div>');
		}
		?>
		</div>
	</div>
	<div id="cao_block-specs">
		<label class="cao">Спецификации</label>
		<?php 
		
		if($wpdb->query("SELECT 1 FROM cao_tags LIMIT 1") == false){$wpdb->query("CREATE TABLE cao_tags (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50), pic TEXT)");}
		$cao_tags = $wpdb->get_results("SELECT * FROM cao_tags WHERE 1","ARRAY_A");

		if(count($cao_tags)>0){ 
			echo('<div class="cao_block-content">');
		}else{ 
			echo('<div class="cao_block-content empty"><span class="cao_info">Еще нет записей. Создайте новую</span>');
		}
		?>	
			<div id="sortable1" class="connectedSortable items-list items-list-favourites">

			</div>
			<div class="item add"><input type="text" placeholder="Название характеристики"></input><div class="add"></div></div>
			<div id="sortable2" class="connectedSortable items-list">
			<?php
				if(count($cao_tags)>0){
					foreach ($cao_tags as $tag) { ?>
						<div class='item' id='specs-<?=$tag['id']?>'>
							<input type='checkbox' data-id='<?=$tag['id']?>'></input>
					<div class="pic"><?php if(strlen($tag['pic'])>0){ ?><img src="<?=$tag['pic']?>"><?php }else{ ?><img src="<?=get_template_directory_uri();?>/assets/images/icon-wp-upload.svg"><?php } ?></div>
							<div class='info'>
								<span><?=$tag['name']?></span>
								<input type='text' placeholder='Значение'></input>
							</div><div class='cao_more' onclick='CUSTOMUI.drop(this,[{name:"Удалить",action:0,params:{id:"<?=$tag['id']?>"}}]);'></div>
						</div>
					<?php }
				}
			?>
			</div>
		</div>
	</div>
	<div id="cao_block-generall">
		<label class="cao">Основные</label>
		<div class="cao_block-content">
		<?php 
			if($USER_SETTINGS['faccess-price'] == 'false' || !$USER_SETTINGS['faccess-price']){ ?>
				<div class="row">
					<h3>Стоимость</h3>
					<input type="text" value="<?=get_post_meta($post->ID,'price',1);?>" name="extra[price]" placeholder="Укажите стоимость"></input>
				</div>
			<?php } ?>

			<div class="row">
			<h3>Площадь</h3>
			<input type="text" value="<?=get_post_meta($post->ID,'area',1);?>" name="extra[area]" placeholder="Укажите площадь"></input>
			</div>

			<?php if($USER_SETTINGS['faccess-freed'] == 'false' || !$USER_SETTINGS['faccess-freed']){ ?>
				<div class="row">
					<h3>Освободится</h3>
					<input type="date" value="<?=get_post_meta($post->ID,'freed',1);?>" name="extra[freed]" placeholder="Укажите дату когда помещение будет свободно для аренды"></input>
				</div>
			<?php }
		?>
		</div>
	</div>
	<div id="cao_block-banner">
		<label class="cao">Баннер</label>
		<div class="cao_block-content input inputFW">
			<div class="row"><h3>Вы можете загрузить изображение или использовать собственный HTML код</h3></div>
			<div class="file-preview
			<?php $IMG = json_decode(get_post_meta($post->ID,'cao_attachment-banner',1));
			if(!$IMG){echo(' off');} ?>">
				<div class="image"><img src="<?=$IMG->url?>"></div>
				<div class="info">
				<input type="text" value="<?=get_post_meta($post->ID,'cao_attachment-banner-title',1);?>" name="extra[cao_attachment-banner-title]" placeholder="Подпись"></input>
				<input type="text" value="<?=get_post_meta($post->ID,'cao_attachment-banner-url',1);?>" name="extra[cao_attachment-banner-url]" placeholder="Ссылка"></input>
					<div data-metaname="cao_attachment-banner" style="margin-top: 40px;" class="btn fill btn-upload loader"><span>Выбрать файл</span></div>
				</div>
			</div>
		</div>
		<?php
		?>
	</div>

	
	<input type="file" id="cao_attachment-pdf" name="cao_attachment-pdf" value="" size="25" />
	
	<input type="text" value="<?=get_post_meta($post->ID,'photos',1);?>" name="extra[photos]"/>
	
</div>
<div class="col_right">
	<div id="cao_block-geo">
		<label class="cao">Геоданные</label>
		<div class="cao_block-content input input-col3">
			<div id="cao_map" data-latLng="<?=get_post_meta($post->ID,'latlng',1);?>">Map</div>
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
	<div id="cao_block-com">
	<label class="cao">Коммерческие условия</label>
		<div class="cao_block-content">
			<textarea name="extra[commercial]" placeholder=""><?=get_post_meta($post->ID,'commercial',1);?></textarea>
		</div>
	</div>
	<div id="cao_block-plan">
	<label class="cao">План помещения</label>
		<div class="cao_block-content">
			<?php $layoutimage = json_decode(get_post_meta($post->ID,'layout-image',1)); ?>
			<div class="image-wide" style="">
				<img src="<?=$layoutimage->url?>">
				<div data-metaname="layout-image" style="margin-top: 40px;" class="upload loader"></div>
			</div>
			<textarea name="extra[layout]" placeholder=""><?=get_post_meta($post->ID,'layout',1);?></textarea>
		</div>
		</div>
	</div>
</div>




<!--DATA-->
<input id="cao_latLng" type="hidden" value="<?=get_post_meta($post->ID,'latlng',1);?>" name="extra[latlng]">

<input id="cao_autoabbr_street_hidden" type="hidden" value="<?=get_post_meta($post->ID,'cao_autoabbr_street',1);?>" name="extra[cao_autoabbr_street]">
<input id="cao_undergound_name" type="hidden" value="<?=get_post_meta($post->ID,'metro',1);?>" name="extra[metro]">
<input id="cao_undergound_dis" type="hidden" value="<?=get_post_meta($post->ID,'metrodis',1);?>" name="extra[metrodis]">

<input id="cao_specs" type="hidden" value='<?=get_post_meta($post->ID,'specs',1);?>' name="extra[specs]">
<input id="cao_all" type="hidden" value="<?=get_post_meta($post->ID,'specs-all',1);?>" name="extra[specs-all]">
<input id="cao_favs" type="hidden" value="<?=get_post_meta($post->ID,'specs-fav',1);?>" name="extra[specs-fav]">

<input id="cao_manager" type="hidden" value="<?=get_post_meta($post->ID,'manager',1);?>" name="extra[manager]">
<div class="settings">
	<a onclick="CAO.modal.init('faccess');" href="#">Настроить быстрый доступ</a>
</div>
</div>