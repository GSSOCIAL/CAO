$(document).ready(function(){
    if($('#cao_map').length > 0){
        window.gmap = {marker:null,map:null};
        $('.estate_postbox').append('<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCK5iuCq4O-0k--Co16Uu2xN43hXZp2Jqg&callback=initMap&libraries=geometry,places"></script>');
    }
    $('.loader').each(function(){
        $(this).on('click',function(){
            $(this).css('width',$(this).width()+'px');
            $(this).addClass('proccess');
            $(this).append(CUSTOMUI.loader());
        });
        this.stoploader = function(){
            $(this).css('width','');
            $(this).removeClass('proccess');
            $(this).find('.co-load').remove();
        }
    });
    $('.upload').each(function(){
        $(this).append('<input type="file" id="'+this.dataset.metaname+'" name="'+this.dataset.metaname+'" value="" size="25" />');
        $(this).on('click',function(){
            this.getElementsByTagName('input')[0].click();
        });
        $(this).find('input').change(function(){
            this.parentNode.stoploader();
        });
    });
    $('.custom-ui').each(function(){
        if($(this).hasClass('dropdown')){

        }
    });
    window.GLOBALS = {
        ROOT: null
    }
    if(document.getElementById('wp-data')){
        GLOBALS.ROOT = document.getElementById('wp-data').dataset.homedir;
    }else{
        GLOBALS.ROOT = null;
        for(var _style in document.styleSheets){
            if(document.styleSheets[_style].href != null){
            if(document.styleSheets[_style].href.indexOf('/themes/cao/') != -1){
                var _match = document.styleSheets[_style].href.toString().match(/(.*)themes.cao/g);
                if(_match.length > 0){
                GLOBALS.ROOT = _match[0];
                }
            }
            }
        }
    }
    if($('#tabber').length > 0){
        $('#tabber .tabs .tab').each(function(i,e){
            this.index = i;
            this.content = $('#tabber .tabs-content .tab')[i];
            $('#tabber')[0].pre = null;
            if(i == 0){
                $(this.content).show();
                $('#tabber')[0].pre = this;
                $(this).addClass('selected').addClass(themename);

            }
            $(this).on('click',function(){
                if($('#tabber')[0].pre == this) return;
                $($('#tabber')[0].pre).removeClass('selected').removeClass(themename);
                $($('#tabber')[0].pre.content).hide();
                $(this).addClass('selected').addClass(themename);
                $(this.content).show();
            });
        });
    }
});
function initMap(){
    if($('#cao_map')[0].dataset.latlng){
        var lg = $('#cao_map')[0].dataset.latlng.replace('(','').replace(')','').split(',');
        var uluru = {lat:parseFloat(lg[0]),lng:parseFloat(lg[1])};
    }else{
        var uluru = {lat: 55.755826, lng: 37.6172999};
    }
    if(!window.gmap) window.gmap = {map:null,marker:null}
    gmap.geocoder = new google.maps.Geocoder;
    gmap.map = new google.maps.Map(document.getElementById('cao_map'), {zoom: 16,center: uluru,mapTypeControl:true});
    gmap.marker = new google.maps.Marker({position: uluru,map: gmap.map}); 
    //
    gmap.map.addListener('click', function(e) {
        gmap.marker.setPosition(e.latLng);
        $('#cao_latLng').val(e.latLng);
        //Поиск улицы
        gmap.geocoder.geocode({'location':e.latLng},function(response,status){
            if(status === 'OK'){
                console.log(response);
                if(response[0]){
                    $('#cao_city').val(response[0].address_components[3].long_name);
                    $('#cao_streetname').val(response[0].address_components[1].long_name);
                    $('#cao_streetnumber').val(response[0].address_components[0].long_name);
                }
            }else{
                CAO.modal.init();
            }
        });
        //Поиск метро
        var lowest = {distance:100000,under:null}
        for(var underloc in gmap.undergoundmap){
            var _d = google.maps.geometry.spherical.computeDistanceBetween(new google.maps.LatLng(gmap.undergoundmap[underloc].latlng),e.latLng);
            if(_d < lowest.distance){
                lowest.distance = _d;
                lowest.under = underloc;
            }
        }
        $('#cao_map_undergrounds')[0].selectedIndex = gmap.undergoundmap[lowest.under].index+1;
        $('#cao_undergound_name').val(gmap.undergoundmap[lowest.under].name);
        $('#cao_undergound_dis').val(parseInt(lowest.distance));
        //$('#cao_map_undergrounds').change();
    });
    $.ajax({
        url: document.getElementById('wp-data').dataset.homedir+'/assets/tmp/undergroundmap.json',
        success: function(data){
            gmap.undergoundmap = data;
            var _selected = 0;
            for(var undermap in data){
                var chil = document.createElement('option');
                chil.value = undermap;
                chil.innerHTML = data[undermap].name;
                if(document.getElementById('cao_undergound_name').value == data[undermap].name){
                    _selected = data[undermap].index+1;
                }
                $('#cao_map_undergrounds').append(chil);
            }
            $('#cao_map_undergrounds')[0].selectedIndex = _selected;
        }
    });
    $('#cao_map_undergrounds').change(function(){
        if(this.selectedIndex != 0){
            for(var underloc in gmap.undergoundmap){
                if(gmap.undergoundmap[underloc].index+1 == this.selectedIndex){
                    var _d = google.maps.geometry.spherical.computeDistanceBetween(new google.maps.LatLng(gmap.undergoundmap[underloc].latlng),gmap.marker.getPosition());
                    $('#cao_undergound_name').val(gmap.undergoundmap[underloc].name);
                    $('#cao_undergound_dis').val(parseInt(_d));
                    return;
                }
            }
        }else{
            $('#cao_undergound_name').val('');
            $('#cao_undergound_dis').val('');
        }
    });
    //Manager
    $('#cao_block-manager .item').each(function(){
        if($(this).hasClass('selected')){
            this.parentNode.pre = this;
        }
        $(this).on('click',function(){
            if(this.parentNode.pre){
                if(this.parentNode.pre == this) return;
            }else{
                if(!this.parentNode.pre) this.parentNode.pre = this;
            }
            $(this.parentNode.pre).removeClass('selected');
            $(this).addClass('selected');
            this.parentNode.pre = this;
            //
            $('#cao_manager').val(this.dataset.id);
        });
    });
    //Specs
    $('#cao_block-specs .add input').on('keydown',function(e){
        if(e.keyCode == 13){
            var v = this.value.toString();
            var o = this;
            $.post(GLOBALS.ROOT+'/assets/dashboard-api.php?method=tag.add',{value:v},function(response){
                response = JSON.parse(response);
                if(response.result == true){
                    if($('#cao_block-specs .cao_block-content').hasClass('empty') == true){
                        setTimeout(function(){$('#cao_block-specs .cao_block-content').removeClass('empty');},200);
                        $('#cao_block-specs .cao_block-content .cao_info').addClass('hide');
                        setTimeout(function(){
                            $('#cao_block-specs .cao_block-content .cao_info').remove();
                        },500);
                    }
                    var _new = document.createElement('div');
                    _new.className = 'item';
                    _new.innerHTML = "<input type='checkbox' data-id='"+response.last_id+"'>"+v+"</input><div class='cao_more'></div>";
                    $('#cao_block-specs .items-list').append(_new);
                    o.value = "";
                }
            });
            e.preventDefault();
        }
    });
    
    window.list_specs = [document.getElementById('cao_favs').value.split(','),document.getElementById('cao_all').value.split(',')]; //0 - ID which fav
    if(document.getElementById('cao_specs').value){ //2 - Values
        try{
            window.list_specs.push(JSON.parse(document.getElementById('cao_specs').value));
        }catch(e){
            window.list_specs.push({});    
        }
    }else{
        window.list_specs.push({});  
    }

    $("#sortable1, #sortable2").sortable({
        connectWith: ".connectedSortable",
        receive:function(event,ui){
            if(window.list_specs){
                if(!$(ui.sender[0]).hasClass('items-list-favourites')){
                    if(list_specs[0].indexOf(ui.item.find('input[type="checkbox"]')[0].dataset.id) == -1){
                        list_specs[0].push(ui.item.find('input[type="checkbox"]')[0].dataset.id);
                    }
                }else{
                    var _index = list_specs[0].indexOf(ui.item.find('input[type="checkbox"]')[0].dataset.id);
                    if(_index != -1){
                        list_specs[0].splice(_index,1);
                    }
                }
                document.getElementById('cao_favs').value = list_specs[0];    
            }else{
                alert('Error');
            }
        }
    }).disableSelection();

    $('#cao_block-specs .items-list input[type="checkbox"]').each(function(){
        var _n = 0;
        if(list_specs[0].indexOf(''+this.dataset.id) != -1){
            _n = 1;
            $(this.parentNode).appendTo(document.getElementById('sortable1'));
        }
        if(list_specs[1].indexOf(this.dataset.id) != -1){
            this.checked = true;
            $(this.parentNode).addClass('on');
        }
        if(typeof list_specs[2]['id-'+this.dataset.id] != 'undefined'){
            $(this.parentNode).find('input[type="text"]').val(list_specs[2]['id-'+this.dataset.id].value);
        }
        $(this.parentNode).find('input[type="text"]').on('blur',function(){
            if(typeof list_specs[2]['id-'+$(this.parentNode.parentNode).find('input[type="checkbox"]')[0].dataset.id] == 'undefined'){
                list_specs[2]['id-'+$(this.parentNode.parentNode).find('input[type="checkbox"]')[0].dataset.id] = {
                    value: ''
                }
            }
            list_specs[2]['id-'+$(this.parentNode.parentNode).find('input[type="checkbox"]')[0].dataset.id].value = this.value;
            document.getElementById('cao_specs').value = JSON.stringify(list_specs[2]);
        });
        $(this.parentNode).find('.pic').each(function(i,e){
            var uploader = document.createElement('input');
            uploader.type = 'file';
            $(uploader).change(function(){
                var upl = this;
                var fd = new FormData();
                fd.append('id',$(upl.parentNode.parentNode).find('input[type="checkbox"]')[0].dataset.id);
                fd.append('file-image',upl.files[0]);
                jQuery.ajax({
                    url: GLOBALS.ROOT+'/assets/dashboard-api.php?method=file.upload',
                    data: fd,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(data){
                        data = JSON.parse(data);
                        $(upl.parentNode).find('img')[0].src = data.url;
                        //$(upl.parentNode.parentNode).find('input[type="checkbox"]')[0].dataset.id;
                    }
                });
                
            });
            $(this).append(uploader);
            $(this).on('click',function(){
                $(this).find('input')[0].click();
            });
        });
        $(this).on('change',function(){
            if(this.checked == true){
                if(!$(this.parentNode).hasClass('on')){$(this.parentNode).addClass('on');}
                if(list_specs[1].indexOf(this.dataset.id) == -1){
                    list_specs[1].push(this.dataset.id);
                }
            }else{
                if($(this.parentNode).hasClass('on')){$(this.parentNode).removeClass('on');}
                var _index = list_specs[1].indexOf(this.dataset.id);
                if(_index != -1){
                    list_specs[1].splice(_index,1);
                }
            }
            document.getElementById('cao_all').value = list_specs[1];
        });
    });

    //cao_autoabbr_street
    if($('#cao_autoabbr_street')){
        $('#cao_autoabbr_street')[0].checked = $('#cao_autoabbr_street_hidden').val();
        $('#cao_autoabbr_street').change(function(){
            $('#cao_autoabbr_street_hidden').val(this.checked);
        });
    }
}
window.CAO = {
    modal:{
        init:function(url,prm){
            if(!prm) prm = '';
            if($('.cao-modal').length == 0){
                var _back = document.createElement('div');
                $(_back).on('click',function(e){
                    if($(e.target).hasClass('back')){
                        $(e.target).removeClass('in');
                        setTimeout(function(){
                            e.target.style.opacity = 0;
                            setTimeout(function(){
                                $(e.target).remove();
                            },400);
                        },200);
                    };
                });
                _back.className = 'cao-modal back';
                _back.innerHTML = '<div class="popup"></div>';
                $.post(GLOBALS.ROOT+'/assets/tmp/'+url+'.php'+prm,{},function(res){
                    $(_back).find('.popup').html(res);
                    _back.style.opacity = 1;
                    setTimeout(function(){
                        $(_back).addClass('in');
                    },200); 
                });
                $(document).find('body').append(_back);
                   
            }
        }
    }
}
var API = {
    call:function(method,params,callback){
        if(!GLOBALS) GLOBALS = {ROOT:document.getElementById('wp-data').dataset.homedir.toString()}
        $.post(GLOBALS.ROOT+'/assets/dashboard-api.php?method='+method,params,function(res){
            res = JSON.parse(res);
            if(typeof callback == 'function'){
                callback.call(this,res);
            }
        });
    }
}
var CUSTOMUI = {
    drop:function(_r,_list){
        if(!_r.dropdown){
            _r.dropdown = document.createElement('div');
            _r.dropdown.className = 'cui cui-dropdown';
            _r.dropdown.style.display = 'none';
            _r.dropdown.innerHTML = '<ul></ul>';
            for(var _item in _list){
                var _li = document.createElement('li');
                _li.innerHTML = _list[_item].name;
                $(_li).on('click',function(){
                        if(_list[_item].params){
                            action_list[_list[_item].action].call(this,_list[_item].params);
                        }else{
                            setTimeout(function(){_list[_item].action.call(this);},100);
                        }
                });
                $(_r.dropdown).find('ul').append(_li);
            }
            _r.appendChild(_r.dropdown);
        }
        if($(_r.dropdown).hasClass('opened')) return;
        _r.dropdown.hide = function(_isemulated){
            $(window).off('click',_r.dropdown.hide);
            $(_r.dropdown).removeClass('opened');
            $(_r.dropdown).hide();
        }
        $(_r.dropdown).show();
        $(_r.dropdown).addClass('opened');
        setTimeout(function(){$(window).on('click',_r.dropdown.hide);},100);
    },
    loader:function(){
        return '<div class="co-load"><span></span><span></span><span></span></div>';
    }
}
var action_list = [
    function(_id){//REMOVE TAG
        API.call('tag.remove',_id,function(res){
            $('#cao_block-specs .items-list #specs-'+_id.id).addClass('hide');
            setTimeout(function(){
                $('#cao_block-specs .items-list #specs-'+_id.id).remove();
            },400);
        });
    },
];