<?php
$STORAGE = get_queried_object();
$DATA = array();
$DATA['metro'] = get_term_meta($STORAGE->term_id,'metro',1);
$DATA['metrodis'] = get_term_meta($STORAGE->term_id,'metrodis',1);
$DATA['city'] = get_term_meta($STORAGE->term_id,'city',1);
$DATA['streetname'] = get_term_meta($STORAGE->term_id,'streetname',1);
$DATA['streetnumber'] = get_term_meta($STORAGE->term_id,'streetnumber',1);
$DATA['latlng'] = get_term_meta($STORAGE->term_id,'latlng',1);

if(get_term_meta($STORAGE->term_id,'cao_autoabbr_street',1) == 'true'){
    $DATA['streetname'] = str_replace(array('улица'),array('ул.'),$DATA['streetname']);
    $DATA['streetnumber'] = str_replace(array('строение'),array('стр.'),$DATA['streetnumber']);  
}
//
$DATA['street_str'] = "".$DATA['streetname']." ".$DATA['streetnumber'];
if(strval($_GET['pull']) == 'nav'){
    $return = array();
    $return['title'] = $STORAGE->name;
    $return['url'] = get_category_link($STORAGE->term_id);
    $return['pull-content'] = '<div class="title">'.$STORAGE->name.'</div><div class="specs"><div class="item"><span>м. '.get_term_meta($STORAGE->term_id,'metro',1).'</span><div class="badge" title="Расстояние по прямой от метро до объекта на основе Google Maps">'.get_term_meta($STORAGE->term_id,'metrodis',1).' м</div></div></div><div class="divider"></div><ul>';
    if(have_posts()):
    while (have_posts()) : the_post();
    $return['pull-content'] .= '<a title="'.$post->post_title.'" href="'.$post->guid.'"><li>'.$post->post_title.'</li></a>';
    endwhile;
    endif;
    $return['pull-content'] .= '</ul>';
    $return['subway'] = $DATA['metro'];
    $return['subway-distance'] = $DATA['metrodis'];
    $return['street'] = $DATA['streetname'];
    $return['street-num'] = $DATA['streetnumber'];
    exit(json_encode($return));
}

get_header();

/*
object(WP_Term)#2826 (10) { 
    ["term_id"]=> int(3) 
    ["name"]=> string(47) "Бизнес центр Соляной двор" 
    ["slug"]=> string(135) "%d0%b1%d0%b8%d0%b7%d0%bd%d0%b5%d1%81-%d1%86%d0%b5%d0%bd%d1%82%d1%80-%d1%81%d0%be%d0%bb%d1%8f%d0%bd%d0%be%d0%b9-%d0%b4%d0%b2%d0%be%d1%80" 
    ["term_group"]=> int(0) 
    ["term_taxonomy_id"]=> int(3) 
    ["taxonomy"]=> string(11) "estate_root" 
    ["description"]=> string(0) "" 
    ["parent"]=> int(0) 
    ["count"]=> int(1) 
    ["filter"]=> string(3) "raw" 
}
*/
?>
<div class="hidden" id="_data" data-id="<?=$term->ID?>" data-template="<?=get_template_directory_uri();?>"></div>
<div class="property_page">
<div class="hierarchy centred"><span><a title="<?=get_bloginfo('name')?>" href="<?=get_home_url()?>">Главная</a></span><span><a href="<?=get_category_link($STORAGE->term_id)?>" title="<?=$STORAGE->name?>"><?=$STORAGE->name?></a><span></div>
<div class="banner-wide" style="background-image:url(<?=get_template_directory_uri();?>/assets/images/photo.png)">
    <div class="centred"><div class="str" id="sticktotop">
        <h1><?=$STORAGE->name;?></h1>
        <span class="location"><span class="city"><?=$DATA['city'];?></span>,<?php if(count($DATA['metro'])>0){echo(" <span class='underground'>м. ".$DATA['metro']."</span>,");} ?> <span class="street"><?=$DATA['street_str'];?></span></span>
    </div></div>
</div>
<div class="content centred">
    <div class="article-content">
        <div class="area_middle">
            <?php if (have_posts()) : ?>
                <h2>Свободные помещения</h2>
                <?php 
                    $posts = get_posts(array('posts_per_page' => -1, 'post_type' => 'estate', 'tax_query' => array(array('taxonomy' => 'estate_root','field' => 'term_id','terms' => $STORAGE->term_id))));
                    if(count($posts)>0){ $class = ''; ?>
                        <div data-count="<?=count($posts);?>" class="carousel grid gridlist-<?=count($posts)?>">
                            <div class="list">
                            <?php foreach($posts as $post){ ?>
                                <a href="<?=$post->guid?>" title="<?=$post->post_title?>"><div class="item estate-object">
                                    <div class="pic"><?php
                                    if($thum = get_the_post_thumbnail($post->ID)){
                                        echo($thum);
                                    }
                                    ?></div>
                                    <div class="object-content">
                                        <div class="title"><?=$post->post_title?></div>
                                        <div class="bottom">
                                            <?php $area = get_post_meta($post->ID,'area',1); if(strlen($area)>0){ ?><div  title="Найти офисы с похожей площадью" class="area"><?=$area?></div> <?php } ?>
                                            <?php $post_price = intval(str_replace(' ','',strval(get_post_meta($post->ID, 'price',1)))); if($post_price){ ?><div title="Найти офисы с похожей стоимостью" class="price"><?php echo(number_format($post_price,0,' ',' '));?></div> <?php } ?>
                                        </div>
                                    </div>
                                </div></a>
                            <?php } ?>
                            </div>
                        </div>
                    <?php }
                ?>
                <?php endif; ?>
        </div>
        <div class="area_left">
            <section class="block">
                <label data-trigger="visible" class="motion motion-label">ИНФОРМАЦИЯ</label>
                <div class="block-content">
                Бизнес-центр «Соляной двор» (БЦ) — это прямая аренда офисных помещений без посредников, центр Москвы 
и выгодные условия. Шестиэтажный корпус дореволюционной постройки (1914 года) был возведен в стиле 
неоклассицизма знаменитым архитектором Шервудом. Он находится на пересечении улиц Забелина и Солянки. 

Богатый фасад (лепнина, пилястры), монументальная входная группа, высокие витринные окна на уровне 
первого и второго этажа — это величественное сооружение и сегодня является украшением города.

Китай-город — очаровательный уголок старой Москвы в центре столицы. С одной стороны здесь все дышит 
патриархальностью а с другой — кипит деловая жизнь города. Одновременно такое расположение 
предоставляет множество выгод для успешных и стабильных компаний, тесно сотрудничающих с 
государственными органами или представляющих таковые. 
                </div>
            </section>
            <section class="block no-offset-bottom">
                <label data-trigger="visible" class="motion motion-label">ХАРАКТЕРИСТИКИ</label>
                <div class="block-content specs">
                    <div class="grid gridlist-3 favs">
                        <div class="item"><div class="pic"></div><div class="title">Отдельный вход</div><div class="value">Есть</div></div>
                        <div class="item"><div class="pic"></div><div class="title">Охрана</div><div class="value">Есть</div></div>
                        <div class="item"><div class="pic"></div><div class="title">Парковка</div><div class="value">20 мест</div></div>
                    </div>
                    <div class="divider ignore-offset"></div>
                    <div class="grid gridlist-3 favs-list ignore-offset">
                        <div class="col">
                            <ul>
                                <li>Интернет</li>
                                <li>Кондиционер</li>
                                <li>Клининговые услуги</li>
                            </ul>
                        </div>
                        <div class="col">
                            <ul>
                                <li>Интернет</li>
                                <li>Кондиционер</li>
                                <li>Клининговые услуги</li>
                            </ul>
                        </div>
                        <div class="col">
                            <ul>
                                <li>Интернет</li>
                                <li>Кондиционер</li>
                                <li>Клининговые услуги</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
            </section>
            <section class="block">
                <label data-trigger="visible" class="motion motion-label">ВАМ МОЖЕТ ПОНРАВИТЬСЯ</label>
                <div class="block-content">
                    
                </div>
            </section>
        </div>
        <div class="area_right">
            <section class="block total no-offset">
                <div id="map" data-latlng="<?=$DATA['latlng']?>"></div>
                <div class="blockoffset">
                    <?php if(strlen($DATA['metro'])>0){ ?><div class="tag-item"><div class="pic"></div><span>Китай город</span><div class="badge" title="Расстояние по прямой от метро до объекта на основе Google Maps"><?=$DATA['metrodis']?> м</div></div><?php } ?>
                            <?php if(strlen($DATA['street_str'])>0){ ?><div class="tag-item"><div class="pic"></div><span><?=$DATA['street_str']?></span></div> <?php } ?>
                </div>
                <div class="divider"></div>
                <div class="blockoffset">
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
                </div>
            </section>
        </div>  
    </div>
</div>

</div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCK5iuCq4O-0k--Co16Uu2xN43hXZp2Jqg&callback=initMap"></script>
<?php wp_footer();?>