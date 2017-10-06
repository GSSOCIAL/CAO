<?php
    date_default_timezone_set(get_option('timezone_string'));

    if(!$wpdb) global $wpdb;
    $bids = $wpdb->get_results("SELECT * FROM cao_bids WHERE 1 ORDER BY id DESC LIMIT 10","ARRAY_A");
    if(count($bids) == 0){
        ?>
        <div class="nonce"><h4>Заявок на просмотр еще не поступало</h4></div>
        <?php
    }else{
        foreach($bids as $bid){
            $DATE = date('d.m в H:i',$bid['regdate']);
            ?>
            <div onclick="CAO.modal.init('bid','?id=<?=$bid['id']?>');" class="widget-item widget-bid <?=get_user_option('admin_color')?>">
                <div class="read-status <?php if($bid['isread'] == 0){echo('read');}?>"><span></span></div>
                <div class="info">
                    <div class="title"><?=$bid['name']?></div>
                    <div class="cat">Просмотр объекта</div>
                    <div class="regdate"><?=$DATE?></div>
                </div>
            </div>
        <?php }
    }
?>
