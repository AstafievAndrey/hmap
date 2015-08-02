function hii(){
    $("#hookah_container").height(screen.height-294);
    $("#parametrs_sorting").height($("#hookah_container").height());

    $(window).resize(function(){
        $("#hookah_container").height(screen.height-294);
        $("#parametrs_sorting").height($("#hookah_container").height());
    });
    
    //добавление маркеров на карту
    $.ajax({
        method:"post",
        url:"/hookah/index/indexAjax",
        dataType:"json",
        success:function(html){
            hii.prototype.zav=html.zav;
            hii.prototype.appendMarkerHookah();
            //console.log(hii.prototype.zav);
        }
    });
}

//делаем сам ajax запрос по названию кальянной
hii.prototype.ajaxSearchByName = function(str,c_id){
    hii.prototype.removeAllMarker();//очистим карту от маркеров
    $.ajax({
        method:"post",
        url:"/hookah/index/searchByName",
        data:"str="+str+"&city_id="+c_id,
        dataType:"json",
        success:function(html){
            hii.prototype.zav=html;
            hii.prototype.appendHookah();
            hii.prototype.appendMarkerHookah();//добавим новые маркеры на карту
            $(".spin").hide();
        }
    });
}

//поиск по названию
hii.prototype.searchByName = function(th){
    str=$(th).val();
    if(document.documentElement.clientWidth<=768){
        if(str[str.length-1]===" "){
            hii.prototype.ajaxSearchByName(str,$("#city").val());
        }
    }else{
        hii.prototype.ajaxSearchByName(str,$("#city").val());
    }
}

//добавим маркеры кальянных на карту
hii.prototype.appendMarkerHookah = function(){
    if(hii.prototype.zav!==null){
        for(i=0;i<hii.prototype.zav.length;i++){
            hii.prototype.zav[i]["marker"] = L.marker(
                    [
                        parseFloat(hii.prototype.zav[i].lat),
                        parseFloat(hii.prototype.zav[i].lon)
                    ])
                    .bindPopup("<h4>"+hii.prototype.zav[i].name+"</h4>"+hii.prototype.zav[i].adress)
                    .addTo(map);
        }
    }
}

//удалим все маркеры с карты
hii.prototype.removeAllMarker = function(){
    $(".caliano").remove();
    $(".spin").show();
    if(hii.prototype.zav!==null){
        for(i=0;i<hii.prototype.zav.length;i++){
            map.removeLayer(hii.prototype.zav[i]["marker"]);
        } 
    }
    hii.prototype.zav=[];
}

//добавим кальянные в main_user_interface
hii.prototype.appendHookah = function(){
    if(hii.prototype.zav!==null){
        for(i=0;i<hii.prototype.zav.length;i++){
            $("#hookah_container").append("<div class='row caliano' onclick='hookah.position(this)' data-ind='"+i+"' data-id='"+hii.prototype.zav[i]["id"]+"'>"
                            + "<div class='col-xs-12'>"
                                +"<span class='company'>"+hii.prototype.zav[i]["name"]+"</span>"
                                +"<span class='adress'>"+hii.prototype.zav[i]["adress"]+""
                                    +"<a href='#'>Подробнее</a>"
                                +"</span>"
                            +"</div>"
                            +"</div>");
        }
    }
}

hii.prototype.selectCity = function(){
    hii.prototype.removeAllMarker();//очистим карту от маркеров
    $.ajax({
        method:"post",
        url:"/hookah/index/getCityOrg",
        data:"id="+$("#city :selected").val()
                +"&categ="+$("#zaved :selected").val()
                +"&price="+$("#price :selected").val()
                +"&alcohol="+$("#alcohol :selected").val(),
        dataType:"json",
        success:function(html){
            hii.prototype.zav=html;
            hii.prototype.appendHookah();
            hii.prototype.appendMarkerHookah();//добавим новые маркеры на карту
            $(".spin").hide();
        }
    });
 }

//массив с заведениями
hii.prototype.zav=[];

//позиционируемся на заведении
hii.prototype.position  = function(th){
    map.setView(
            [
                parseFloat(hii.prototype.zav[parseInt($(th).data('ind'))].lat),
                parseFloat(hii.prototype.zav[parseInt($(th).data('ind'))].lon)
            ], 12
            );
    hii.prototype.zav[parseInt($(th).data('ind'))]["marker"].openPopup();
    if(document.documentElement.clientWidth<=768){
        hii.prototype.show_or_hide('.main_user_interface');
    }
}

//скрываем пказываем окно
hii.prototype.hide = function(name){
    $(name).hide("400");
}
hii.prototype.show = function(name){
    $(name).show("400");
}
hii.prototype.show_or_hide = function(name){
    if($(name).is(':visible')){
        this.hide(name);
    }else{
        this.show(name);
    }
}

//показываем и скрываем параметры для сортировки
hii.prototype.show_hide_parametrs = function(){
    if($("#parametrs_sorting").is(':visible')){
        $("#parametrs_sorting").hide(400);
        $(".sortShHd").text("Показать");
        hii.prototype.selectCity();
    }else{
        $("#parametrs_sorting").show(400);
        $(".sortShHd").text("Скрыть");
    }
}

//для main_user_interface
hii.prototype.indent = function(){
    function mnHeight(){
        if($("#mn").height()===0){
            $(".main_user_interface").animate({
                top: "50px"
            },400);
        }else{
            $(".main_user_interface").animate({
                'top': $("#mn").height()+50+"px"
            },400);
        }
    }
    setTimeout(mnHeight,400);
}