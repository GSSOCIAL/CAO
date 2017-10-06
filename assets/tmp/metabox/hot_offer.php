<?php
    $terms = get_terms('hot_offers', array('hide_empty' => false));
    //var_dump($terms);
    if(count($terms) == 0){
        $terms = wp_insert_term('Использовать','hot_offers',array('description'=> 'Hot offers','slug' => 'hot_offers'));
        $terms[0]['name'] = 'Использовать';
        /*
        array(0) { } array(2) { ["term_id"]=> int(8) ["term_taxonomy_id"]=> int(8) }*/
    }
    $value = 'false';
    $HOTPRICE = '';
    $HOTDATE = '';
    if($hp = get_post_meta($post->ID,'hot_price',1)){
        $HOTPRICE = $hp;
    }else{
        $HOTPRICE = get_post_meta($post->ID,'price',1);
    }
    if($hu = get_post_meta($post->ID,'hot_until',1)){
        $HOTDATE = $hu;
    }
    if(has_term('hot_offers','hot_offers',$post->ID)){
        $value = 'true';
    }
    //

?>
<div class="widget widget-bar">
    <input id="is_hot" <?php if($value == 'true'){echo('checked');} ?> type="checkbox"> Использовать </input>
    <div id="is_hot_info">
        <input type="text" name="extra[hot_price]" placeholder="Горячая стоимость" title="Укажите стоимость, которая будет показана во время горячего предложения" value="<?=$HOTPRICE?>">
        <input type="date" name="extra[hot_until]" placeholder="Горячая дата" title="Укажите дату до которой предложение будет действительно" value="<?=$HOTDATE?>">
    </div>
</div>

<input name="hot_offer" id="is_hot_hidden" type="hidden" value="<?=$value?>">

<script type="text/javascript">
if($('#is_hot')[0].checked){
    $('#is_hot_info').addClass('show');
}
$('#is_hot').change(function(){
    $('#is_hot_hidden').val(this.checked);
    if(this.checked == true){
        $('#is_hot_info').addClass('show');
    }else{
        $('#is_hot_info').removeClass('visible');
    }
});
</script>
