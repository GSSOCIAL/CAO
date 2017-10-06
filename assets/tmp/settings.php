<script type="text/javascript">
    window.themename = "<?=get_user_option('admin_color')?>";
</script>
<form method="post" action="options.php">
<?php settings_fields( 'cao_config' ); ?>
<div id="wp-data" data-homedir="<?=get_template_directory_uri();?>"></div>
<div id="block-settings" class="wrap">
    <h2>Настройки</h2>
    <div id="tabber">
    <div class="tabs">
        <div class="tab wp-color-border">Контакты</div>
    </div>
    <div class="tabs-content">
        <div style="display:none" class="tab">
            <h2>Контактная информация</h2>
            <div class="form-field">
                <label style="margin-bottom:10px;">Геоданные</label>
                <div data-latlng="<?=get_option('cao_latlng');?>" id="cao_map"></div>
            </div>
            <div class="geo_info">
				<input id="cao_city" type="text" value="<?=get_option('cao_city');?>" name="cao_city" placeholder="Город"></input>
				<input id="cao_streetname" type="text" value="<?=get_option('cao_streetname');?>" name="cao_streetname" placeholder="Улица"></input>
				<input id="cao_streetnumber" type="text" value="<?=get_option('cao_streetnum');?>" name="cao_streetnum" placeholder="Дом"></input>
            </div>
            <div class="form-field">
                <label for="tag-description" style="margin-bottom:10px;">Параметры</label>        
                <input id="cao_autoabbr_street" type="checkbox" value="" name="">Автоматически сокращать название улицы и дома</input>
            </div>
            <div class="form-field">
                <label for="tag-description" style="margin-bottom:10px;">Станция метро</label>
                <select id="cao_map_undergrounds"><option value="null">Не показывать</option></select>
            </div>
            <div class="form-field">
                <label for="tag-description" style="margin-bottom:10px;">Контактные телефоны:</label>
                <input id="cao_phones" type="text" value="<?=get_option('cao_phones');?>" name="cao_phones" placeholder="Укажите телефоны через запятую">
            </div>
        </div>
    </div>
    </div>
    <input id="cao_latLng" type="hidden" value="<?=get_option('cao_latlng');?>" name="cao_latlng">

    <input id="cao_autoabbr_street_hidden" type="hidden" value="<?=get_option('cao_autoabbr');?>" name="cao_autoabbr">
    <input id="cao_undergound_name" type="hidden" value="<?=get_option('cao_subway');?>" name="cao_subway">
    <input id="cao_undergound_dis" type="hidden" value="<?=get_option('cao_subway_distance');?>" name="cao_subway_distance">

    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="cao_city,cao_streetname,cao_streetnum,cao_phones,cao_latlng" />

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</div>
</form>
<script type="text/javascript">
$(document).ready(function(){
    var googlemapsscr = document.createElement('script');
    googlemapsscr.setAttribute('async','');
    googlemapsscr.setAttribute('defer','');
    googlemapsscr.setAttribute('src','https://maps.googleapis.com/maps/api/js?key=AIzaSyCK5iuCq4O-0k--Co16Uu2xN43hXZp2Jqg&callback=initMap&libraries=geometry,places');
    $('#block-settings').append(googlemapsscr);
});
</script>
