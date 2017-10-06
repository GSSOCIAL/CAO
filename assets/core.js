$(document).ready(function(){
    $('#page').addClass('ready');
    var w = window,d = document,e = d.documentElement,g = d.getElementsByTagName('body')[0],x = w.innerWidth || e.clientWidth || g.clientWidth,y = w.innerHeight|| e.clientHeight|| g.clientHeight;

    window.GLOBAL = {
        template_uri: document.getElementById('_data').dataset.template.toString(),
        styles: null,
        height:y
    }
    GLOBAL.styles = document.createElement('style');
    GLOBAL.styles.type = 'text/css';
    if(GLOBAL.styles.styleSheet){
        GLOBAL.styles.styleSheet.cssText = '';
    }else{
        GLOBAL.styles.appendChild(document.createTextNode(''));
    }
    document.head.appendChild(GLOBAL.styles);
    
    $('.loader').each(function(){
        $(this).on('click',function(){
            $(this).css('width',$(this).width()+'px');
            $(this).addClass('proccess');
        });
        this.stoploader = function(){
            $(this.parentNode).removeClass('status');
            $(this).removeClass('proccess');
            $(this).removeClass('fault');$(this).removeClass('success');
            $(this).css('width','');
        }
    });
    //Prepare form
    $('#req_bidpost').find('.btn').on('click',function(){
        var _t = this;
        var _data = $('#req_bidpost').serialize();
        var _id = document.getElementById('_data').dataset.id.toString();
        $.post(GLOBAL.template_uri+'/core.php?method=bid.send&'+$('#req_bidpost').serialize(),{objectid:_id},function(res){
            res = JSON.parse(res);
            $(_t.parentNode).addClass('status');
            if(res.result != false){
                $(_t).addClass('success');
                $(_t.parentNode).find('.status').html('Успешно');
                setTimeout(function(){_t.stoploader();},1600);
            }else{
                $(_t).addClass('fault');
                $(_t.parentNode).find('.status').html('Ошибка');
                setTimeout(function(){_t.stoploader();},1600);
            }
        });
    });
    $('.filters').each(function(){
        this.filters = {
            opened:false,
            price:[0,0],
            area:[0,0]
        }
        this.filters._this = this;
        this.filters.hide = function(){
            $(this._this.parentNode.parentNode).removeClass('filters-enabled');
            $(this._this).removeClass('active');
            window.search(this); 
        }
        var _this = this;
        $('#map .overlay .overlay-content .block-filters').find('.submit').on('click',function(){
            _this.filters.hide();
        });
        $(this).on('click',function(){
            if(!this.filters){
                this.filters = {
                   opened:false,
                   price:0,
                   area:0,
                }
                this.filters._this = this;
                this.filters.hide = function(){
                    $(this._this.parentNode.parentNode).removeClass('filters-enabled');
                    $(this._this).removeClass('active');
                }
            }
            this.filters.opened = !this.filters.opened;
            if(this.filters.opened == true){
                $(this).addClass('active');
                $(this.parentNode.parentNode).addClass('filters-enabled');
            }else{
                this.filters.hide();
            } 
        });
    });
    $('#page .top-nav ul li').each(function(){
        if($(this).hasClass('menu-item-has-children')){
            $(this).find('a:first-child svg').remove();
            if(!$(this.parentNode).hasClass('sub-menu')){
                var _extended = $('#nav_extended').clone();
                $(_extended).find('.nbar .title').html($(this).find('a:first-child').html());
                $(this).find('.sub-menu .menu-item-object-estate_root').each(function(i,e){
                    $.post($(e).find('a')[0].getAttribute('href')+'?pull=nav',{},function(res){
                        res = JSON.parse(res);
                        var _e = document.createElement('li'), _ed = document.createElement('div');
                        _e.innerHTML = '<a href="'+res.url+'">'+res.title+'</a>';
                        _ed.innerHTML = res['pull-content'];_ed.className = 'list-item';
                        _e.sub = _ed;

                        $(_e).mouseover(function(){
                            if(!this.parentNode.pre){
                                this.parentNode.pre = this.sub;
                            }
                            $(this.parentNode.pre).hide();
                            $(this.sub).show();
                            this.parentNode.pre = this.sub;
                        });

                        $(_extended).find('.nbar ul').append(_e);
                        $(_extended).find('.nav-content .list').append(_ed);
                    });
                });
                $(this).append(_extended);
            }
        }
    });
    if($('#sticktotop').length > 0){
        var _this = $('#sticktotop');
        $(window).scroll(function(ex){
            if(_this[0].prepos){
                if(window.scrollY < _this[0].prepos){
                    _this.removeClass('sticky');
                }
            }
            if(_this[0].getBoundingClientRect().top <= 0){
                if(!_this.hasClass('sticky')){
                    _this[0].prepos = window.scrollY;
                    _this.addClass('sticky');
                }
            }
        });
    }
    if($('#carousel').length > 0){
        $('#carousel').each(function(){
            new CUSTOMUI.carousel(this);
        });
    }
    $('.motion').each(function(){
        var _this = this;
        switch(this.dataset.trigger){
            case 'visible':
                this._f = function(e){
                    if(e.data.o.getBoundingClientRect().top < GLOBAL.height-200){
                        if($(e.data.o).hasClass('in')){
                            $(window).off('scroll',e.data.o._f);
                        }else{
                            $(e.data.o).addClass('in');
                        }
                    }
                }
                $(window).on('scroll',{o:_this},_this._f);
            break;
            case 'domready':
            default:
                $(this).addClass('in');
            break;
        }
    });
    $("#map .overlay .overlay-content .block-filters .row #range").each(function(){
        var _root = this.dataset.filter;
        var _min = parseInt(this.dataset.min);
        var _max = parseInt(this.dataset.max);
        if(_min == _max){
           _min -= 1;
        }
        $(this).freshslider({
            range: true,
            step: 1,
            text: true,
            min: _min,
            max: _max,
            enabled: true,
            value: 15000,
            onchange:function(low,high){
                if($('#map .overlay .search .filters')[0].filters){
                    $('#map .overlay .search .filters')[0].filters[_root] = [low,high]; 
                }
            } 
        });
    });
});
$(window).resize(function(){
    var w = window,d = document,e = d.documentElement,g = d.getElementsByTagName('body')[0],x = w.innerWidth || e.clientWidth || g.clientWidth,y = w.innerHeight|| e.clientHeight|| g.clientHeight;
    GLOBAL.height = y;
});

function initMap(){
    //CHECK IF MAP
    if($('#map').length > 0){
    window.GMAP = {};
    if(!window.GLOBAL) window.GLOBAL = {template_uri:document.getElementById('_data').dataset.template.toString()}
    GMAP.o = $('#map')[0];
    if($('#map').find('.map').length > 0){
        GMAP.o = $('#map').find('.map')[0];
    }
        if(GMAP.o.dataset.latlng){
            var lg = GMAP.o.dataset.latlng.replace('(','').replace(')','').split(',');
            var uluru = {lat:parseFloat(lg[0]),lng:parseFloat(lg[1])};
        }else{
            var uluru = {lat: 55.755826, lng: 37.6172999};
        }
        GMAP.map = new google.maps.Map(GMAP.o, {zoom: 17,center: uluru,mapTypeControl:false});
        GMAP.markers = {};
        if(GMAP.o.dataset.latlng){
            GMAP.markers.static = new google.maps.Marker({position: uluru,map: GMAP.map});
        }
        if($('#map').hasClass('front_home')){
            API.call('tax.get',{posttype:'estate_root',fields:'latlng'},function(response){
                if(response.result != false){
                    GLOBAL.estates = response.list;
                    for(_item in response.list){
                        var _coor = response.list[_item].latlng.replace('(','').replace(')','').split(',');
                        GMAP.markers['tax-'+response.list[_item].id] = new google.maps.Marker({position:new google.maps.LatLng(_coor[0],_coor[1]),map:GMAP.map});
                        GMAP.markers['tax-'+response.list[_item].id].info = new google.maps.InfoWindow({
                            content: '<h3>'+response.list[_item].name+'</h3><p><a href="'+response.list[_item].url+'">Перейти</a></p>'
                        });
                        GMAP.markers['tax-'+response.list[_item].id].addListener('click',function(){
                            this.info.open(GMAP.map,this);
                        });
                    }
                }
            });
            setTimeout(function(){$('#map .overlay').addClass('in');},800);
            if($('#map .overlay .search').length >0){
                window.search = function(filters,input){
                    if(!filters){
                        filters = $('#map .overlay .search .filters')[0].filters;
                    }
                    if(!input){
                        input = typeof input != 'undefined' || input != null?input:$('#map .overlay .search input').val().toString();
                    }
                    console.log(filters);

                    API.call('search',{request:input,price:filters.price,area:filters.area},function(response){
                        $('#map .overlay .overlay-content .search-results').html('');
                        if(response.message == 'empty'){
                            $('#map .overlay .overlay-content .search-results').html('<div class="null-results">Ничего не найдено</div>');
                        }else{
                            if(response.result != false){
                                if(response.terms){
                                    $('#map .overlay .overlay-content .search-results').append('<div class="group terms-list"><label>Центры</label></div>');
                                    for(term in response.terms){
                                        $('#map .overlay .overlay-content .terms-list').append(new mapconstructor.search_item('term',response.terms[term]));    
                                    }
                                }
                                if(response.spec){
                                    var _count = 0;
                                    for(var spec_name in response.spec){
                                        _count++;
                                        var _id =  'spec-'+_count+'-list';
                                        $('#map .overlay .overlay-content .search-results').append('<div class="group '+_id+'"><label>'+spec_name+'ы</label></div>');
                                        console.log(response.spec[spec_name]);
                                        for(var _specitem in response.spec[spec_name]){
                                            $('#map .overlay .overlay-content .'+_id).append(new mapconstructor.search_item(spec_name,response.spec[spec_name][_specitem]));
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
                $('#map .overlay .search input').on('keyup',function(){
                    var _this = this;
                    if(GLOBAL.search_timeout){window.clearTimeout(GLOBAL.search_timeout);}
                    if(this.value.length > 2){
                        $('#map .overlay').addClass('typing');
                        $('#map .overlay').addClass('search-active');
                        setTimeout(function(){$('#map .overlay .overlay-content .hot-offers').hide();},300);
                        GLOBAL.search_timeout = setTimeout(function(){
                            $('#map .overlay').removeClass('typing');
                            window.search(null,_this.value.toString());
                        },1000);
                    }else{
                        setTimeout(function(){
                            $('#map .overlay .overlay-content .hot-offers').show();
                        },400);
                        $('#map .overlay').removeClass('search-active');
                        if($('#map .overlay').hasClass('typing')){
                            $('#map .overlay').removeClass('typing');
                        }
                    }
                });
            }
        }
        if($('#map').hasClass('front_contacts')){
            setTimeout(function(){$('#map .overlay').addClass('in');},800);
        }
    }
    
}
var calendar = {
    init:function(o){
        if(typeof o.calendar == 'undefined' || o.calendar == null){
           o.calendar = {
               opened: true,
               view_type: 2,
               startDate: new Date(),
           };
           o.calendar.currentDate = o.calendar.startDate;
        }
    },
    getTotalDaysinMon:function(date){
        return new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
    }
}
var API = {
    call:function(method,params,callback){
        if(!window.GLOBAL) window.GLOBAL = {template_uri:document.getElementById('_data').dataset.template.toString()}
        $.post(GLOBAL.template_uri+'/core.php?method='+method,params,function(res){
            res = JSON.parse(res);
            if(typeof callback == 'function'){
                callback.call(this,res);
            }
        });
    }
}
var mapconstructor = {
    search_item:function(type,data){
        var _div = document.createElement('div');
        _div.className = 'item '+type;
        _div.innerHTML = '<div class="title">'+decodeURI(data.name)+'</div>';
        _div._coredata = data;
        switch(type){
            case 'term':
                $(_div).on('mouseover',function(){
                    if(!GMAP.pre){
                        GMAP.pre = {zoom:GMAP.map.getZoom(),center:GMAP.map.getCenter()}
                    }
                    if(GMAP.markers['tax-'+this._coredata.id]){
                        GMAP.map.setCenter(GMAP.markers['tax-'+this._coredata.id].getPosition());
                        GMAP.map.setZoom(16);
                    }
                });
                $(_div).on('mouseout',function(e){
                    
                });
            break;
        }
        return _div;
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
        return '<div class="co-load"> <div class="loading-bar"></div><div class="loading-bar"></div><div class="loading-bar"></div><div class="loading-bar"></div></div>';
    },
    carousel:function(o){
        if(!o.carousel){
            o.carousel = {
                obj:o,
                current:0,
                len:0,
                selector: $(o).find('.container-selector')[0],
                main: $(o).find('.container-main')[0],
                pre:{}
            }
            var _c = o.carousel;
            o.carousel.len = parseInt(o.carousel.selector.dataset.len);
            o.carousel.pre.selector = $(o.carousel.selector).find('.item')[0];

            o.carousel.next = function(){return;}
            o.carousel.prev = function(){return;}
            o.carousel.goto = function(NUM){
                $(this.main).find('.list').css('margin-left','-'+(NUM*$(o.carousel.main).width())+'px');
            }
            o.carousel.resized = function(e){
                GLOBAL.styles.sheet.rules[0].style.width = $(o.carousel.main).width()+'px';
            }
            //PUSH STYLES
            GLOBAL.styles.sheet.addRule('#carousel .container-main .view-main','width:'+$(o.carousel.main).width()+'px');
            if($(o.carousel.main).find('.list').length == 0){$(o.carousel.main).append('<div class="list"></div>');}

            $(o.carousel.selector).find('.item').each(function(i,e){
                this.c = this.parentNode.parentNode;
                this.index = i;

                this.main = document.createElement('div');
                this.main.className = 'view-main';
                $(this.c.carousel.main).find('.list').append(this.main);

                if($(this).hasClass('selected')){
                    this.c.carousel.pre.selector = this;
                    this.c.carousel.current = i;
                }
                $(this).on('click',function(){
                    if(this.index == this.c.carousel.current){return false;}
                    $(this.c.carousel.pre.selector).removeClass('selected');
                    $(this).addClass('selected');
                    this.c.carousel.pre.selector = this;
                    this.c.carousel.current = this.index;
                    //CHANGE MAIN
                    if(this.main){
                        if(!this._img){
                            this._img = new Image();
                            var _this = this;
                            this._img.onload = function(){
                                _this.c.carousel.goto(_this.index);
                            }
                            this._img.src = this.c.carousel.pre.selector.dataset.original;
                            this.main.append(this._img);
                        }else{
                            this.c.carousel.goto(this.index);
                        }
                    }
                });
                if(i == this.c.carousel.len-1){
                    if(!$(this.c.carousel.pre.selector).hasClass('selected')){
                        $(this.c.carousel.pre.selector).addClass('selected');
                    }
                    $(this.c.carousel.main).find('.list')[0].style.width = ($(this.c.carousel.main).width()*this.c.carousel.len)+'px';
                    //Init
                    this.c.carousel.pre.selector._img = new Image();
                    var _this = this.c.carousel.pre.selector;
                    this.c.carousel.pre.selector._img.onload = function(){
                        _this.c.carousel.goto(_this.index);
                    }
                    this.c.carousel.pre.selector._img.src = this.c.carousel.pre.selector.dataset.original;
                    this.c.carousel.pre.selector.main.append(this.c.carousel.pre.selector._img);
                }
            });
            $(window).on('resize',o.carousel.resized);
        }else{
            return false;
        }
    }
}