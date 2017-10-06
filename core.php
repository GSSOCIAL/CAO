<?php

require_once("../../../wp-load.php");
global $wpdb;

$return = array();
$methods = array('bid.send','post.get','tax.get','search','hotoffers.get');
$request = strval($_GET['method']);
date_default_timezone_set(get_option('timezone_string'));

if(in_array($request,$methods)){
    if($request == 'bid.send'){
        $PARSED = array();

        $PARSED['name'] = strval($_GET['name']);
        $PARSED['phone'] = strval($_GET['phone']);
        $PARSED['mail'] = strval($_GET['mail']);
        $PARSED['comment'] = strval($_GET['comment']);
        if( strlen(strval($_POST['objectid'])) > 0){
            $OB = get_post(intval(strval($_POST['objectid'])));
            $PARSED['object'] = $OB->post_title;
            $PARSED['url'] = $OB->guid; 
        }else{
            $PARSED['object'] = strval($_GET['object']);
            $PARSED['url'] = strval($_GET['object']);
        }

        //CHECK
        if(strlen($PARSED['phone']) == 0 && strlen($PARSED['mail']) == 0 ){
            $return['result'] = false;
            $return['error'] = 'Phone or Mail not specified. Code 4';
            exit(json_encode($return));
        }
        if(strlen($PARSED['mail']) > 0){
            if(!filter_var($PARSED['mail'], FILTER_VALIDATE_EMAIL)){
                $return['result'] = false;
                $return['error'] = 'Mail validation error. Code 5';
                $return['message'] = 'Адрес почты не прошел проверку, пожалуйста проверьте правильнось ввода.';
                exit(json_encode($return));
            }
        }
        //PULL
        if($wpdb->query("SELECT 1 FROM cao_bids LIMIT 1") == false){//Create table if not exist.
            $wpdb->query("CREATE TABLE cao_bids (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, regdate TEXT, name VARCHAR(20), phone VARCHAR(20), mail VARCHAR(50), comment TEXT, previewdate TEXT, isread INT(2), object TEXT, objecturl TEXT)");
        }
        if($wpdb->query("INSERT INTO `cao_bids` (`regdate`,`name`,`phone`,`mail`,`comment`,`previewdate`,`isread`,`object`,`objecturl`) VALUES ('".time()."','".$PARSED['name']."','".$PARSED['phone']."','".$PARSED['mail']."','".$PARSED['comment']."','','0','".$PARSED['object']."','".$PARSED['url']."')") == true){
            $return['result'] = true;
            exit(json_encode($return));
        }else{
            $return['result'] = false;
            $return['message'] = 'Не удалось.';
            $return['error'] = 'DB insert error. Code 5';
        }
    }
    if($request == 'post.get'){
        $return['result'] = true;
        $return['list'] = array();
        
        $POSTTYPES = array('estate_root','estate');
        $POSTTYPE = strval($_POST['posttype']);

        if(in_array($POSTTYPE,$POSTTYPES)){
            $EXTRA = explode(',',strval($_POST['fields']));
            
            $mypost = array('post_type'=>$POSTTYPE);
            $loop = new WP_Query($mypost);

            while($loop->have_posts()) : $loop->the_post();
                //var_dump($post);
                $ID = $post->ID;
                $return['list'][$ID] = array();
                $return['list'][$ID]['id'] = $post->ID;
                $return['list'][$ID]['name'] = $post->post_title;
                $return['list'][$ID]['url'] = $post->guid;
                foreach($EXTRA as $fieldname){
                    $return['list'][$ID][$fieldname] = get_post_meta($post->ID,$fieldname,1);
                }
            endwhile;
            wp_reset_query();
        }else{
            $return['result'] = false;
            $return['error'] = 'Unexpected param passed. Code 16';
            exit(json_encode($return));
        }
    }
    if($request == 'tax.get'){
        $return['result'] = true;
        $return['list'] = array();
        
        $POSTTYPES = array('estate_root');
        $POSTTYPE = strval($_POST['posttype']);

        if(in_array($POSTTYPE,$POSTTYPES)){
            $EXTRA = explode(',',strval($_POST['fields']));

            $myterms = get_terms($POSTTYPE, 'hide_empty=0');
            foreach($myterms as $tax){
                $ID = $tax->term_id;
                $return['list'][$ID] = array();
                $return['list'][$ID]['id'] = $tax->term_id;
                $return['list'][$ID]['name'] = $tax->name;
                $return['list'][$ID]['term_taxonomy_id'] = $tax->term_taxonomy_id;
                $return['list'][$ID]['url'] = $tax->taxonomy.'/'.$tax->slug;
                foreach($EXTRA as $fieldname){
                    $return['list'][$ID][$fieldname] = get_term_meta($tax->term_id,$fieldname,1);
                }
            }
        }else{
            $return['result'] = false;
            $return['error'] = 'Unexpected param passed. Code 17';
            exit(json_encode($return));
        }
    }
    if($request == 'search'){
        $return['result'] = true;
        $return['list'] = array();
        $REQ = strval($_POST['request']);
        $FILTER = array();
        $FILTER['price'] = $_POST['price'];
        $FILTER['area'] = $_POST['area'];
        
        $res = search(strtolower($REQ),$return,$FILTER);
        if($res != false){
            exit(json_encode($res));
        }
        $in_feng = str_replace(array('q','w','e','r','t','y','u','i','o','p','[',']','a','s','d','f','g','h','j','k','l',';','\'','z','x','c','v','b','n','m',',','.'),array('й','ц','у','к','е','н','г','ш','щ','з','х','ъ','ф','ы','в','а','п','р','о','л','д','ж','э','я','ч','с','м','и','т','ь','б','ю','.'),strtolower($REQ));
        if(strcmp(strtolower($REQ),$in_feng) !== 0){
            $res = search($in_feng,$return);
            if($res != false){
                $res['like'] = $in_feng;
                exit(json_encode($res));
            }
        }
        $return['message'] = 'empty';
    }
    if($request == 'hotoffers.get'){
        $hots = get_posts(array('post_type' => 'estate','numberposts' => -1,'tax_query' => array(array('taxonomy' => 'hot_offers','field' => 'slug','terms' => 'hot_offers'))));
        if(count($hots)>0){
            $return['list'] = array();
            foreach($hots as $hot){
                $object_until = get_post_meta($hot->ID,'hot_until',1);
                if(strtotime($object_until) < time()){ //Горячее кончилось
                    wp_delete_object_term_relationships ($hot->ID, 'hot_offers');
                }else{
                    $ID = $hot->ID;
                    $return['list'][$ID] = array();
                    $return['list'][$ID]['id'] = $hot->ID;
                    
                    $return['list'][$ID]['title'] = $hot->post_title;
                    $return['list'][$ID]['hot_until'] = $object_until;
                }
            }
        }
    }
    exit(json_encode($return));
}else{
    $return['result'] = false;
    $return['error'] = 'Unexpected method passed. Code 1';
    exit(json_encode($return));
}

function search($in,$array,$filters){
    $in = strtolower($in);
    $founded = false;
    //Search for office
    $term = get_terms( array('taxonomy' => array('estate_root'),'orderby' => 'id','hide_empty' => false,'order' => 'ASC','fields' => 'all','name__like' => $in));
    if(count($term)>0){
        $founded = true;
        $array['terms'] = array();
        foreach($term as $item){
            $ID = $item->term_id;
            $array['terms'][$ID]['id'] = $item->term_id;
            $array['terms'][$ID]['name'] = $item->name;
            $array['terms'][$ID]['url'] = get_category_link($item->term_id);
            $array['terms'][$ID]['subway'] = get_term_meta($item->term_id,'metro',1);
        }
    }
    
    //02 - Search for property type
    $ptype = get_terms(array('taxonomy' => array('property-type'),'orderby' => 'id','hide_empty' => true,'order' => 'ASC','fields' => 'all'));
    
    $properties = array();
    foreach($ptype as $property_type){
        $strl = mb_strtolower(strval($property_type->name));
        $properties[$strl] = array();
        $properties[$strl]['id'] = $property_type->term_id;
    }
    $needle = explode(' ',$in);
    foreach($needle as $word){
        switch(mb_substr($word,-1)){
            case 'а':
            case 'ы':
            $word = mb_substr($word,0,-1);
            break;
            default:
            break;
        }
        if(array_key_exists($word,$properties)){
            //Display items with tax
            $array['search-pattern'] = array('IA-property-type');
            $postsproperty = get_posts(array('posts_per_page' => -1, 'post_type' => 'estate','tax_query' => array(array('taxonomy' => 'property-type','field' => 'term_id','terms' => $properties[$word]['id']))));
            $array['spec'] = array();
            $array['spec'][$word] = array();
            foreach($postsproperty as $item){
                $ID = $item->ID;
                $price = get_post_meta($item->ID,'price',1);
                $area = get_post_meta($item->ID,'area',1);
                if(strlen($price)>0){
                    if(intval($price) >= intval($filters['price'][0]) && intval($price) <= intval($filters['price'][1])){}else{
                        continue;// skip
                    }
                }
                if(strlen($area)>0){
                    if(intval($area) >= intval($filters['area'][0]) && intval($area) <= intval($filters['area'][1])){}else{
                        continue;// skip
                    }
                }
                $founded = true;
                
                $array['spec'][$word][$ID] = array();
                $array['spec'][$word][$ID]['id'] = $item->ID;
                $array['spec'][$word][$ID]['name'] = $item->post_title;
                $array['spec'][$word][$ID]['url'] = $item->guid;
            }
            break;
        }
    }
    if($founded == false){
        return false;
    }else{
        return $array;
    }
}
?>