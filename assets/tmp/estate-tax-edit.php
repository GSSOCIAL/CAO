<div class="tax-block estate_postbox">
<div id="wp-data" data-homedir="<?=get_template_directory_uri();?>"></div>
    <tr class="form-field">
    <th scope="row" valign="top"><label>Геоданные</label></th>
    <td>
        <div id="cao_map" data-latlng="<?=get_term_meta($term->term_id,'latlng',1);?>"></div>
        <div class="geo_info">
				<input id="cao_city" type="text" value="<?=get_term_meta($term->term_id,'city',1);?>" name="extra[city]" placeholder="Город"></input>
				<input id="cao_streetname" type="text" value="<?=get_term_meta($term->term_id,'streetname',1);?>" name="extra[streetname]" placeholder="Улица"></input>
				<input id="cao_streetnumber" type="text" value="<?=get_term_meta($term->term_id,'streetnumber',1);?>" name="extra[streetnumber]" placeholder="Дом"></input>
        </div>
    </td>
    <tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label>Параметры</label></th>     
        <td>
            <input id="cao_autoabbr_street" type="checkbox" value="" name="">Автоматически сокращать название улицы и дома</input>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label>Станция метро</label></th>     
        <td>
            <select id="cao_map_undergrounds"><option value="null">Не показывать</option></select>
        </td>
    </tr>
            
    <!--HIDDEN-->
    <input id="cao_latLng" type="hidden" value="<?=get_term_meta($term->term_id,'latlng',1);?>" name="extra[latlng]">

    <input id="cao_autoabbr_street_hidden" type="hidden" value="<?=get_term_meta($term->term_id,'cao_autoabbr_street',1);?>" name="extra[cao_autoabbr_street]">
    <input id="cao_undergound_name" type="hidden" value="<?=get_term_meta($term->term_id,'metro',1);?>" name="extra[metro]">
    <input id="cao_undergound_dis" type="hidden" value="<?=get_term_meta($term->term_id,'metrodis',1);?>" name="extra[metrodis]">
    <input id="cao_specs" type="hidden" value="<?=get_term_meta($term->term_id,'specs',1);?>" name="extra[specs]">
</div>