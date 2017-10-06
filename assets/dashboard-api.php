<?php
require_once("../../../../wp-load.php");
global $wpdb;

$return = array();

$methods = array('tag.add','config.change','tag.remove','file.upload','demo');
$request = strval($_GET['method']);

if(in_array($request,$methods)){
    if($request == 'tag.add'){
        $val = strval($_POST['value']);
        $copy = $wpdb->get_results("SELECT `id` FROM `cao_tags` WHERE `name`='$val'",ARRAY_A);
        if(count($copy)>0){
            $return['result'] = false;
            $return['error'] = 'tag exist';
        }else{
            if($wpdb->query("INSERT INTO `cao_tags` (`name`) VALUES ('$val')") == true){
                $last_id = $wpdb->insert_id;
                $return['result'] = true;
                $return['last_id'] = $last_id;
            }else{
                $return['result'] = false;
                $return['error'] = 'error';
            }
        }
    }
    if($request == 'tag.remove'){
        $id = strval($_POST['id']);
        if($wpdb->query("DELETE FROM `cao_tags` WHERE `id`='$id'") == true){
            $return['result'] = true;
        }else{
            $return['result'] = false;
            $return['error'] = 'error';
        }
    }
    if($request == 'config.change'){

        $user = strval($_POST['user']);
        $key = strval($_POST['key']);
        $val = strval($_POST['value']);

        $select = $wpdb->get_results("SELECT `id` FROM `cao_config` WHERE `user`='$user' AND `cfg`='$key'",ARRAY_A);
        if(count($select)>0){
            foreach($select as $sel){
                $wpdb->query("UPDATE `cao_config` SET `value`='$val' WHERE `id`=".$sel['id']);
                $return['result'] = true;
            }
        }else{
            if($wpdb->query("INSERT INTO `cao_config` (`user`,`cfg`,`value`) VALUES ('$user','$key','$val')") == true){
                $last_id = $wpdb->insert_id;
                $return['result'] = true;
            }else{
                $return['result'] = false;
                $return['error'] = 'error';
            }
        }
    }
    if($request == 'file.upload'){
        if($_FILES['file-image']){
            $FILEDATA = array();
			$filename = str_replace(array('.','-'),array('',''),strval(microtime()))."".strrchr($_FILES['file-image']['name'], '.');	
			$upload = wp_upload_bits($filename,null,file_get_contents($_FILES['file-image']['tmp_name']));
			if(!$upload['error']){
                $wpdb->query("UPDATE `cao_tags` SET `pic`='". $upload['url']."' WHERE `id`=".$_POST['id']);
                $return['url'] = $upload['url'];
			}else{
                $return['result'] = false;
                $return['status'] = $upload['error'];
            }
        }else{
            $return['result'] = false;
        }
    }
    if($request == 'demo'){
        offers_reorder();
    }
    exit(json_encode($return));
}else{
    $return['result'] = false;
    exit(json_encode($return));
}
?>