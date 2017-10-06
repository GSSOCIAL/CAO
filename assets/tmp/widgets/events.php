<?php date_default_timezone_set(get_option('timezone_string')); ?>
<div id="cao_wgt_events" data-sysdate="<?=date('d.m.Y.H.i')?>" class="widget-root <?=get_user_option('admin_color')?>">

    <div id="events-list"></div> 
    <div id="cao_wgt_events_nonce" style="display:none;" class="nonce"><h4>Можно расслабиться и выпить чай.<br>Ближайших событий нет</h4></div>
    <script type="text/javascript">
        var cdate = $('#cao_wgt_events')[0].dataset.sysdate.split('.');
        var mons = ['января','февраля','марта','апреля','мая','июня','июля','августа','сентрября','октября','ноября','декабря'];
        $(document).ready(function(){
            $.post(GLOBALS.ROOT+'/core.php?method=hotoffers.get',{},function(response){
                response = JSON.parse(response);
                console.log(response);
                if(response.list.length == 0){
                    $('#cao_wgt_events_nonce').show();
                    return false;
                }
                $('#cao_wgt_events_nonce').remove();
                for(_n in response.list){
                    var tap = null, label = '';
                    var item = response.list[_n];
                    var _until = item['hot_until'].split('-');
                    var oToday = new Date(''+cdate[1]+' '+cdate[0]+' '+cdate[2]);
                    var oDeadLineDate = new Date(''+_until[1]+' '+_until[2]+' '+_until[0]);
                    var nDaysLeft = oDeadLineDate > oToday ? Math.ceil((oDeadLineDate - oToday) / (1000 * 60 * 60 * 24)) : null; // а тут мы вычисляем, сколько же осталось дней — находим разницу в миллисекундах и переводим её в дни
                    switch(nDaysLeft){
                        case 1:
                            tap = '1d';label = 'Завтра';
                        break;
                        case 2:
                            tap = '2d';label = 'Послезавтра';
                        break;
                        default:
                            tap = _until[1]+'_'+_until[2]+'_'+_until[0];label = parseInt(_until[2])+' '+mons[_until[1]-1];
                        break;
                    }
                    if($('#events-list .'+tap).length == 0){
                        $('#events-list').append('<div class="group '+tap+'"><h4>'+label+'</h4></div>');
                    }
                    var _item = document.createElement('div');
                    _item.className = 'widget-item widget-event';
                    _item.innerHTML = ('<div class="info"><div class="title">'+item.title+'</div><div class="cat">Истекает горячее предложение</div></div>');
                    $('#events-list .'+tap).append(_item);  
                }
            });
        });
        /*
        function appendevent(date,data){
            var compare = new Date(cdate[2], cdate[1], cdate[0]) = new Date(date[2], date[1], date[0]);
            if(cdate[2] == date[2] && cdate[1] == date[1] && cdate[0] == date[0]){

            }
            if($('#events-list .'+date[0]+'_'+date[1]+'_'+date[2]).length == 0){
                $('#events-list').append('<div class="group '+date[0]+'_'+date[1]+'_'+date[2]+'"><h4>'+date[0]+' '+mons[date[1]-1]+'</h4></div>');
            }
            var _item = document.createElement('div');
            _item.className = 'widget-item widget-event';
            _item.innerHTML = ('<div class="info"><div class="title">'+data.title+'</div><div class="cat">'+data.cat+'</div></div>');
            $('#events-list .'+date[0]+'_'+date[1]+'_'+date[2]).append(_item);  
        }*/
        
    </script>
</div>