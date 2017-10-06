<div class="calendar-pop"></div>
<!--
-->
<?php
    /*
    Мета-значения:
    city - Город
    metro - Станция метро
    metrodis - Расстояние от объекта до метро по прямой линии
    streetname - Улица
    streetnumber - Номер дома, улицы
    latLng - Координаты latLng
    cao_autoabbr_street - Флаг, указывающий что нужно форматировать название улицы, дома
    manager - ID пользователя сайта который указан менеджером
    cao_attachment-pdf - JSON Объект содержащий информацию о прикрепляемом PDF файле
    cao_attachment-banner - JSON Объект содержащий информацию о изображении - баннере
    cao_attachment-banner-title - Подпись изображения баннера
    cao_attachment-banner-url - Ссылка для изображения баннера
    layout-image - JSON объект баннера
    layout - Описание
    price - Стоимость
    area - Площадь
    media - Массив ссылок фотографий разделенных запятой
    commercial - Коммерческие условия
    // Характеристики
    specs: JSON объект с данными полей
    specs-all: Перечень идентификаторов характеристик, которые были отмечены как "показывать"
    specs-fav: Перечень  идентификаторов характеристик, которые были помечены как "избранные"
    */
    wp_head();
    get_header();
    the_post();
    global $wpdb;
    $STORAGE = array();
    $STORAGE['city'] = get_post_meta($post->ID,'city',1);
    $STORAGE['street'] = get_post_meta($post->ID,'streetname',1);
    $STORAGE['streetnumber'] = get_post_meta($post->ID,'streetnumber',1);
    $STORAGE['metro'] = get_post_meta($post->ID,'metro',1);
    $STORAGE['price'] = intval(str_replace(' ','',strval(get_post_meta($post->ID, 'price', 1))));
    $STORAGE['price'] = number_format($STORAGE['price'],0,' ',' ');
    $STORAGE['media'] = explode(',',get_post_meta($post->ID,'photos',1));
    $STORAGE['area'] = get_post_meta($post->ID,'area',1);
    $STORAGE['commercial'] = get_post_meta($post->ID,'commercial',1);
    if(get_post_meta($post->ID,'cao_autoabbr_street',1) == 'true'){
        $STORAGE['street'] = str_replace(array('улица'),array('ул.'),$STORAGE['street']);
        $STORAGE['streetnumber'] = str_replace(array('строение'),array('стр.'),$STORAGE['streetnumber']);  
    }
    //
    $STORAGE['street_str'] = "".$STORAGE['street']." ".$STORAGE['streetnumber'];
    $STORAGE['IMGbanner'] = json_decode(get_post_meta($post->ID,'cao_attachment-banner',1));
    $STORAGE['HTMLbanner'] = get_post_meta($post->ID,'cao_attachment-html-banner',1);
    $STORAGE['layout'] = json_decode(get_post_meta($post->ID,'layout',1));
    $dslide = json_decode(get_post_meta($post->ID,'cao_attachment-pdf',1));
    $STORAGE['slide'] = array();$STORAGE['slide']['url'] = $dslide->url;$STORAGE['slide']['size_str'] = number_format(floatval($dslide->size)/1048576, 1, '.', '');
    $STORAGE['term'] = get_the_terms($post->ID,'estate_root');
    //
    $STORAGE['offer-type'] = get_the_terms($post->ID,'offer-type');
    $STORAGE['property-type'] = get_the_terms($post->ID,'property-type');
    $STORAGE['layout'] = array();
    $STORAGE['layout']['image'] = json_decode(get_post_meta($post->ID,'layout-image',1));
    $STORAGE['layout']['description'] = get_post_meta($post->ID,'layout',1);
    //
    $STORAGE['specs-all'] = explode(',',get_post_meta($post->ID,'specs-all',1));
    $STORAGE['specs-fav'] = explode(',',get_post_meta($post->ID,'specs-fav',1));
    $STORAGE['specs-cfg'] = json_decode(get_post_meta($post->ID,'specs',1));
    //
?>
<div class="hidden" id="_data" data-id="<?=$post->ID?>" data-template="<?=get_template_directory_uri();?>"></div>
<div class="hierarchy centred"><span><a title="<?=get_bloginfo('name')?>" href="<?=get_home_url()?>">Главная</a></span><span><a href="<?=get_category_link($STORAGE['term'][0]->term_id)?>" title="<?=$STORAGE['term'][0]->name?>"><?=$STORAGE['term'][0]->name?></a></span><span><a href="#" title="<?=the_title('','',true);?>"><?=the_title('','',true);?></a></span><div class="divider"></div></div>
<div class="content centred">
    <div class="article-head">
        <div class="area_left">
            <h1><?=the_title('','',true);?></h1>
            <span class="location"><span class="city"><?=$STORAGE['city'];?></span>,<?php if(count($STORAGE['metro'])>0){echo(" <span class='underground'>м. ".$STORAGE['metro']."</span>,");} ?> <span class="street"><?=$STORAGE['street_str'];?></span>
        </div>
        <div class="area_right">
            <div class="price notag">
                <span class="total"><?=$STORAGE['price']?></span><div class="cur"><?=the_icon('rub');?><span>в месяц</span></div>
            </div>
            <div class="tags">
                <?php if(count($STORAGE['offer-type'])>0){ ?><div title="" class="item"><?=$STORAGE['offer-type'][0]->name?></div><?php } ?>
                <?php if(count($STORAGE['property-type'])>0){ ?><div title="" class="item"><?=$STORAGE['property-type'][0]->name?></div><?php } ?>
                <?php if(strlen($STORAGE['area'])>0){ ?><div title="" class="item nofill"><?=$STORAGE['area']?> м<sup>2</sup></div><?php } ?>
            </div>
        </div>
    </div>
    <div class="article-content">
        <div class="area_left">
            <div id="carousel">
                <div class="container-main"></div>
                <div data-len="<?=count($STORAGE['media'])?>" class="container-selector">
                    <?php
                        foreach($STORAGE['media'] as $item){ ?>
                            <div style="background-image:url(<?=$item?>);" class="item item-preview" data-original="<?=$item?>"></div>
                        <?php }
                    ?>
                </div>
            </div>
            <section class="block">
                <label data-trigger="visible" class="motion motion-label">ИНФОРМАЦИЯ</label>
                <div class="block-content"><?php the_content(); ?></div>
            </section>
            <?php 
                if($STORAGE['layout']['image']){
                    $row = '1';
                    if(strlen($STORAGE['layout']['description'])>0){
                        $row = '2';
                    }
            ?>
            <section class="block">
                <label>ПЛАН ПОМЕЩЕНИЯ</label>
                <div class="block-content cols-<?=$row?>">
                    <div class="col"><img src="<?=$STORAGE['layout']['image']->url?>"></div>
                    <?php 
                        if(strlen($STORAGE['layout']['description'])>0){ ?>
                        <div class="col"><?=$STORAGE['layout']['description']?></div>
                        <?php } ?>
                </div>
            </section>
            <?php }
            $cao_tags = $wpdb->get_results("SELECT `name`,`id` FROM cao_tags WHERE `id` IN (".implode(',',array_map('intval', $STORAGE['specs-all'])) .")","ARRAY_A");
            $cao_favs = $wpdb->get_results("SELECT `name`,`id`,`pic` FROM cao_tags WHERE `id` IN (".implode(',',array_map('intval', $STORAGE['specs-fav'])) .")","ARRAY_A");
            ?>
            <section class="block no-offset-bottom">
                <label data-trigger="visible" class="motion motion-label">ХАРАКТЕРИСТИКИ</label>
                <div class="block-content specs">
                    <?php if(count($cao_favs)>0){ ?>
                    <div class="grid gridlist-3 favs">
                        <?php 
                            for($i=0;$i<3;$i++){
                                if($cao_favs[$i]){ 
                                    $fval = false;
                                    if(property_exists($STORAGE['specs-cfg'],'id-'.$cao_favs[$i]['id']) ){
                                        $fval = get_object_vars($STORAGE['specs-cfg']);
                                    }
                                    
                                    ?>
                                    <div class="item"><div class="pic"><?php if(strlen($cao_favs[$i]['pic'])>0){ ?><img src="<?=$cao_favs[$i]['pic']?>"><?php } ?></div><div class="title"><?=$cao_favs[$i]['name']?></div><div class="value"><?php if($fval != false){ echo($fval['id-'.$cao_favs[$i]['id']]->value); } ?></div></div>
                            <?php } }
                        ?>
                    </div>
                    <?php }
                    if(count($cao_tags)>0){ ?>
                    <div class="divider ignore-offset"></div>
                    <div class="grid gridlist-3 favs-list ignore-offset">
                        <?php 
                        $cnt_tags = count($cao_tags);
                        $rows = array();
                        $rows['row0'] = '<div class="col"><ul>';
                        if($cnt_tags > 1){$rows['row1'] = '<div class="col"><ul>';}
                        if($cnt_tags > 2){$rows['row2']= '<div class="col"><ul>';}
                        for($i=0;$i<$cnt_tags;$i++){
                            if(in_array($cao_tags[$i]['id'],$STORAGE['specs-fav'])){
                                continue;
                            }
                            $needle = 0;
                            $fl = $i/3;
                            if(is_float($fl)){
                                $fl = explode('.',strval($fl));
                                $nd = strval($fl[1][0]);
                                if($nd == '3'){
                                    $needle = 1;
                                }else{
                                    $needle = 2;
                                }
                            }else{
                                $needle = 0;
                            }
                            $rows['row'.$needle] .= '<li>'.$cao_tags[$i]['name'].'</li>';
                        }
                        echo($rows['row0'].'</ul></div>');
                        if($rows['row1']){echo($rows['row1'].'</ul></div>');}
                        if($rows['row2']){echo($rows['row2'].'</ul></div>');}
                        ?>
                    </div> <?php } ?>
                </div>
            </section>
            <?php 
            if(strlen($STORAGE['commercial'])>0){ ?>
            <section class="block">
                <label data-trigger="visible" class="motion motion-label">КОММЕРЧЕСКИЕ УСЛОВИЯ</label>
                <div class="block-content"><?=$STORAGE['commercial']?></div>
            </section>
            <?php } ?>
        </div>
        <div class="area_right">
            <section class="block form">
                <div class="manager">
                    <?php
                    $manager_q = get_post_meta($post->ID, 'manager', 1);
                    if($manager_q != false){$manager = get_user_by('id',$manager_q);?>
                        <div class="pic"><?=get_avatar($manager_q,56);?></div><div class="info"><div class="name"><?=$manager->data->display_name?></div><div class="cnt"><?=the_icon('phone')?><?=formatnumber(get_user_meta($manager_q,'phone',true))?></div><div class="cnt"><?=the_icon('mail')?><?=$manager->data->user_email?></div></div>
                    <?php }else{ ?>
                        <div class="pic"></div><div class="info"><div class="name">Ваш консультант</div><div class="cnt">000</div><div class="cnt">000</div></div>
                    <?php
                        }
                    ?>
                </div>
                <div class="divider ignore-offset"></div>
                <div class="form">
                    <div class="title">Назначить просмотр</div>
                    <form id="req_bidpost">
                        <input name="name" type="text" placeholder="Ваше имя" title="Укажите ваше имя"></input>
                        <input name="phone" type="text" placeholder="Телефон" title="Укажите ваш контактный номер телефона"></input>
                        <input name="mail" type="text" placeholder="Адрес эл.почты" title="Укажите ваш адрес электронной почты"></input>
                        <textarea name="comment" placeholder="комментарий" title="Если необходимо, вы можете оставить комментарий к заявке"></textarea>
                        <p>Вы можете указать желаемую дату просмотра</p>
                        <div class="form-footer">
                            <div class="form-spec"><div class="calendar-drop calendar"></div><span class="status"></span></div>
                            <div class="btn fill red loader"><span>Отправить</span></div> 
                        </div>
                    </form>
                </div>
            </section>
            <section class="block total no-offset">
                <div id="map" data-latLng="<?=get_post_meta($post->ID, 'latlng', 1);?>"></div>
                <div class="blockoffset">
                    <?php
                        if(count($STORAGE['metro'])>0){
                            echo('<div class="tag-item"> <div class="pic"></div><span>'.$STORAGE['metro'].'</span>');
                            if(get_post_meta($post->ID, 'metrodis', 1)){
                                echo('<div class="badge" title="Расстояние по прямой от метро до объекта на основе Google Maps">'.get_post_meta($post->ID, 'metrodis', 1).' м</div>');
                            }
                            echo('</div>');
                        }
                    ?>
                    <div class="tag-item" <?php if(strlen($STORAGE['layout']['description'])==0){echo('style="margin-bottom:20px;" ');} ?>><div class="pic"></div><span><?=$STORAGE['street_str'];?></span></div>
                    <?php if(strlen($STORAGE['layout']['description'])==0){ ?>
                        <div class="divider"></div>
                        <a href="<?=$STORAGE['slide']['url'];?>" target="_blank" title="Открыть презентацию в новом окне"><div class="slides"><div class="pic"></div><span>Смотреть презентацию</span><div class="size"><?=$STORAGE['slide']['size_str']?> МБ</span></div>
                    <?php } ?>
                </div>
            </section>
            <?php if($STORAGE['IMGbanner'] || $STORAGE['HTMLbanner']){
                echo('<div class="banner">');
                if($STORAGE['HTMLbanner']){
                    echo($STORAGE['HTMLbanner']);
                }else{
                    echo('<a href="'.get_post_meta($post->ID, 'cao_attachment-banner-url', 1).'" target="_blank"><img title="'.get_post_meta($post->ID, 'cao_attachment-banner-title', 1).'" src="'.$STORAGE['IMGbanner']->url.'"></a>');
                }
                echo('</div>');
            }?>
        </div>  
    </div>
</div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCK5iuCq4O-0k--Co16Uu2xN43hXZp2Jqg&callback=initMap"></script>
<?php wp_footer();?>