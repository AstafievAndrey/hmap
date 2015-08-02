function Land(){
   
}
Land.prototype.cottages;

//добавляем коттедж
Land.prototype.appendCottage = function(i){
    $("#search_result").append(
        "<div class='land' data-id='"+i+"' data-land='Land.prototype.cottages["+i+"]'>\n\
            <div class='like'></div>\n\
            <div data-id='"+i+"' data-land='Land.prototype.cottages["+i+"]'>\n\
                <span class='company' data-id='"+i+"'>"+Land.prototype.cottages[i].name_cottage+"</span>\n\
                <br>\n\
                <span class='adress'>цена за сотку от "+Land.prototype.cottages[i].price+"\n\
                    <!--<a href='"+Land.prototype.cottages[i].site+"' target='_blank'>cайт компании</a>-->\n\
                </span>\n\
                <span class='phone'></span>\n\
            </div>\n\
        </div>");
}

//сортировка
Land.prototype.sorting = function(){
    this.check = {
        city: function(i){
            if($("#city").val()==="all"){
                return true;
            }else{
                if($("#city").val()==Land.prototype.cottages[i].city_id){
                    return true;
                }else return false;
            }            
        },
        waterbody: function(i){
            if($("#waterbody").val()==="all"){
                return true;
            }else{
                if($("#waterbody").val()==Land.prototype.cottages[i].waterbody){
                    return true;
                }else return false;
            }  
        },
        forest: function(i){
            if($("#forest").val()==="all"){
                return true;
            }else{
                if($("#forest").val()==Land.prototype.cottages[i].forest){
                    return true;
                }else return false;
            } 
        },
        append: function(i){
            if(this.city(i)){
                    if(this.waterbody(i)){
                        if(this.forest(i)){
                            Land.prototype.appendCottage(i);
                        }
                    }
                }
        }
    }
    if($("#plus_minus").text()==="-"){
        $("#search_result div").remove();
        if($("#price").val()==="1"){
            for(i=Land.prototype.cottages.length-1;i>=0;i--){
                this.check.append(i);   
            }
        }else{
            for(i=0;i<Land.prototype.cottages.length;i++){
                this.check.append(i);
            }
        }
    }
}

//получим список доступных городов
Land.prototype.getCities = function(){
     $.ajax({
        method: "POST",
        url: "/land/ajax/getCities",
        dataType:"json",
        success: function(html){
            if(html.state==="ok"){
                for(i=0;i<html.cities.length;i++){
                    $("#city").append(
                            "<option value='"
                                +html.cities[i].city_id+"'>"
                                +html.cities[i].name_city+"\
                            </option>"
                            );
                }
            }
        }
    });
}

//позиционируемся на участке
Land.prototype.position  = function(th){
    map.setView(
            [
                parseFloat(Land.prototype.cottages[parseInt($(th).data('id'))].coordinates[0].lat),
                parseFloat(Land.prototype.cottages[parseInt($(th).data('id'))].coordinates[0].lon)
            ], 13
            );
    Land.prototype.cottages[parseInt($(th).data('id'))]["polygon"].openPopup();
    if(document.documentElement.clientWidth<=768){
        Land.prototype.turn_expand('#sidebar');
    }
}

//добавляем полигон
Land.prototype.drawpolygon = function(i){
    Land.prototype.cottages[i]["polygon"] = L.polygon(
                                array_coords,
                                {
                                    color: 'red',
                                    colorOpacity:0.4,
                                    fillColor: '#f03',
                                    fillOpacity: 0.3
                                }
                            )
                            .bindPopup("<h2>"+Land.prototype.cottages[i].name_cottage+"</h2>\n\
                                            <div style='height:200px; overflow:auto;'>"+Land.prototype.cottages[i].about+"</div>\n\
                                            <br>цена за сотку от "+Land.prototype.cottages[i].price+"\n\
                                        <a href='"+Land.prototype.cottages[i].site+"' target='_blank'>cайт компании</a>")
                            .addTo(map);
}

//добавляем круг
Land.prototype.drawcircle = function(i){
    Land.prototype.cottages[i]["circle"] = L.circle(
                                    [
                                        parseFloat(Land.prototype.cottages[0].lat),
                                        parseFloat(Land.prototype.cottages[0].lon)
                                    ],
                                            200,    {
                                                        color: 'red',
                                                        fillColor: '#f03',
                                                        fillOpacity: 0.5
                                                    }
                                )
                                .bindPopup("<h2>"+Land.prototype.cottages[i].name_cottage+"</h2>\n\
                                        <a href='"+Land.prototype.cottages[i].site+"' target='_blank'>Сайт</a>")
                            .addTo(map);
}

//получаем все коттеджи
Land.prototype.getCottages = function(map){
    $("#load").show();
     $.ajax({
        method: "POST",
        url: "/land/ajax/getCottages",
        dataType:"json",
        success: function(html){
            console.log(html);
            if(html.state==="ok"){          
                Land.prototype.cottages = html.cottages;
                //выводим список всех коттеджей
                for(i=0;i<Land.prototype.cottages.length;i++){
                    Land.prototype.appendCottage(i);
                    //проверяем список координат
                    if(Land.prototype.cottages[i].coordinates.length>1){
                        array_coords = [];
                        for(j=0;j<Land.prototype.cottages[i].coordinates.length;j++){
                            array_coords.push(
                                    [
                                        parseFloat(Land.prototype.cottages[i].coordinates[j].lat),
                                        parseFloat(Land.prototype.cottages[i].coordinates[j].lon)
                                    ]
                                );
                        }
                        Land.prototype.drawpolygon(i);            
                    }else{
                        Land.prototype.drawcircle(i);
                    }
                }
            }
            $("#load").hide();
        }
    });
}

//показываем и сворачиваем окно выбора параметров поиска и самог главное окно
Land.prototype.turn_expand = function(obj){
    
    this.option = {
        blur: function(bl){
            $("#sidebar_body").css({
                    '-webkit-filter': 'blur('+bl+'px)',
                    '-moz-filter': 'blur('+bl+'px)',
                    '-ms-filter': 'blur('+bl+'px)',
                    '-o-filter': 'blur('+bl+'px)',
                    'filter': 'blur('+bl+'px)',
                });
        },
        animate:function(h,m,head){
            $("#sidebar").animate({
                        "height":h+"px",
                        "margin-top":m+"px",
                    },500);
            $("#head").animate({
                        "height":head+"px",
                    },500);
            $("#head").toggleClass("border_head");       
        },
        text:function(txt){
            $("#plus_minus").text(txt);
        }
    }
    
    switch(obj){
        case "#select_param": 
            if($("#select_param").is(':visible')){
                $("#select_param").hide();
                this.option.text("+");
                this.option.blur(0);
            }else{
                $("#select_param").height($("#sidebar").height()-57);
                $("#select_param").show("500");  
                this.option.text("-");
                this.option.blur(2);
            }
            break;
            
        case "#sidebar": 
            if($("#sidebar").height()==50){
                this.option.animate(document.documentElement.clientHeight,0,28);
                $("#head_name").show();
                $("#head_block").hide();
            }else{
                $("#head_name").hide();
                $("#head_block").show();
                this.option.animate(50,document.documentElement.clientHeight-50,50);
            }
            
            break;
    }
    
}

//устанавливаем размеры всех элементов
Land.prototype.resize = function(){
    
    this.option = {
        setsize: function(){
            $("#footer").width(document.documentElement.clientWidth-350);
            $("#footer").css("margin-top",document.documentElement.clientHeight-50+"px");
            $("#sidebar_body").height($("#sidebar").height()-50);
            $("#search_result").height($("#sidebar").height()-50);
            $("#sidebar").css("width",350);
            $("#search_result").css("width",350);
        },
        setsizephone:function(){
            $("#search_result").height($("#sidebar").height()-50);
        }
    }
    
    if(document.documentElement.clientWidth<=768){
        this.option.setsizephone();
    }else{
        this.option.setsize();
    }
    
}

land = new Land();


