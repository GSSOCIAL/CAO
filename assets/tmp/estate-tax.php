<div class="tax-block estate_postbox">
<div id="wp-data" data-homedir="<?=get_template_directory_uri();?>"></div>
    <div class="form-field">
        <label for="tag-description" style="margin-bottom:10px;">Геоданные</label>
        <div id="cao_map"></div>
    </div>
    <div class="geo_info">
				<input id="cao_city" type="text" value="Москва" name="extra[city]" placeholder="Город"></input>
				<input id="cao_streetname" type="text" value="" name="extra[streetname]" placeholder="Улица"></input>
				<input id="cao_streetnumber" type="text" value="" name="extra[streetnumber]" placeholder="Дом"></input>
            </div>
    <div class="form-field">
        <label for="tag-description" style="margin-bottom:10px;">Параметры</label>        
        <input id="cao_autoabbr_street" type="checkbox" value="" name="">Автоматически сокращать название улицы и дома</input>
    </div>
    <div class="form-field">
        <label for="tag-description" style="margin-bottom:10px;">Станция метро</label>
        <select id="cao_map_undergrounds"><option value="null">Не показывать</option></select>
    </div>
            
    <!--HIDDEN-->
    <input id="cao_latLng" type="hidden" value="" name="extra[latlng]">

    <input id="cao_autoabbr_street_hidden" type="hidden" value="" name="extra[cao_autoabbr_street]">
    <input id="cao_undergound_name" type="hidden" value="" name="extra[metro]">
    <input id="cao_undergound_dis" type="hidden" value="" name="extra[metrodis]">
    <input id="cao_specs" type="hidden" value="" name="extra[specs]">
</div>