<?php
/*
Template Name: Контакты
*/
get_header();
the_post();
$DATA = array();
$DATA['city'] = get_option('cao_city');
$DATA['street'] = get_option('cao_streetname');
$DATA['streetname'] = get_option('cao_streetnum');
$DATA['subway'] = get_option('subway');
$DATA['phones'] = explode(',',get_option('cao_phones'));
$DATA['latlng'] = get_option('latlng');

if(get_option('cao_autoabbr') == 'true'){
    $DATA['street'] = str_replace(array('улица'),array('ул.'),$DATA['street']);
    $DATA['streetname'] = str_replace(array('строение'),array('стр.'),$DATA['streetname']);  
}
$DATA['streetstr'] = ', '.$DATA['street'].' '.$DATA['streetname'];
?>
<div class="hidden" id="_data" data-template="<?=get_template_directory_uri();?>"></div>
<div class="front_page">
    <div id="map" class="front_contacts">
        <div class="overlay">
            <h1><?=the_title()?></h1>
            <div class="description"><?=the_content();?></div>
                <?php 
                if(strlen($DATA['city'])>0 || strlen($DATA['street'])>0){
                    echo('<div class="row"><label>Адрес:</label><span>');
                    if(strlen($DATA['postindex'])>0){
                        echo($DATA['postindex'].', ');
                    }
                    if(strlen($DATA['city'])>0){
                        echo($DATA['city'].'');
                    }
                    if(strlen($DATA['subway'])>0){
                        echo(', м.'.$DATA['subway'].' ');
                    }
                    echo($DATA['streetstr'].'</span></div>');
                }
                if(count($DATA['phones'])>0){ ?>
                <div class="row"><label>Телефоны:</label><ul>
                <?php
                    foreach($DATA['phones'] as $phone){
                        echo('<li>'.$phone.'</li>');
                    }
                ?>
                </ul></div>
                <?php }
                ?>
                <div class="row">
                    <label>Появились вопросы?</label>
                    <div class="form">
                        <textarea placeholder="Сообщение"></textarea>
                    </div>
                </div>
        </div>
        <div data-latlng="<?=get_option('cao_latlng');?>" class="map"></div>
    </div>
</div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCK5iuCq4O-0k--Co16Uu2xN43hXZp2Jqg&callback=initMap"></script>
<?php wp_footer();?>