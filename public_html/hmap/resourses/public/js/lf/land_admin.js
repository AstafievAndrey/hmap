function Land_admin(){

}
Land_admin.prototype.cottages;
///////////////////////////////////////
    /*
     * рисуем маршрут 
     * map.on('click', function(e){})
     */
Land_admin.prototype.point = [];
Land_admin.prototype.polyline = [];
Land_admin.prototype.count = 0;
Land_admin.prototype.pt = [];

//
Land_admin.prototype.ctrl_down = false;
Land_admin.prototype.ctrl_key = 17;
Land_admin.prototype.z_key = 90;

Land_admin.prototype.drawroutes = function(e){
    if($('#addCottage').is(':visible')||$('#editCottage').is(':visible')){
            Land_admin.prototype.pt.push(L.marker(e.latlng).addTo(map));
            Land_admin.prototype.point.push(e.latlng.lat,e.latlng.lng);
            if(Land_admin.prototype.point.length===4) {
                Land_admin.prototype.polyline[Land_admin.prototype.count] = 
                        L.polyline([
                            [Land_admin.prototype.point[0],Land_admin.prototype.point[1]],
                            [Land_admin.prototype.point[2],Land_admin.prototype.point[3]]
                        ], {color: 'red'}).addTo(map);
            }
            if(Land_admin.prototype.point.length>4) {
                Land_admin.prototype.count=Land_admin.prototype.count+1;
                Land_admin.prototype.polyline[Land_admin.prototype.count]=L.polyline([
                    [Land_admin.prototype.point[Land_admin.prototype.point.length-4],Land_admin.prototype.point[Land_admin.prototype.point.length-3]],
                    [Land_admin.prototype.point[Land_admin.prototype.point.length-2],Land_admin.prototype.point[Land_admin.prototype.point.length-1]]
                ], {color: 'red'}).addTo(map);
            }  
        }
}

Land_admin.prototype.ctrl_z=function(){
        
        $(document).keydown(function(e) {
                if (e.keyCode == Land_admin.prototype.ctrl_key) Land_admin.prototype.ctrl_down = true;
            }).keyup(function(e) {
                if (e.keyCode == Land_admin.prototype.ctrl_key) Land_admin.prototype.ctrl_down = false;
            });
            
        $(document).keydown(function(e) {
            if (Land_admin.prototype.ctrl_down && (e.keyCode === Land_admin.prototype.z_key)) {
                console.log(Land_admin.prototype.count);
                console.log(Land_admin.prototype.map);
                if($('#addCottage').is(':visible')||$('#editCottage').is(':visible')){
                    map.removeLayer(Land_admin.prototype.pt[Land_admin.prototype.pt.length-1]);
                    map.removeLayer(Land_admin.prototype.polyline[Land_admin.prototype.count]);
                    if(Land_admin.prototype.count===0){
                        Land_admin.prototype.point=[];
                        map.removeLayer(Land_admin.prototype.pt[0]);
                        Land_admin.prototype.pt=[];
                    }
                    else {
                        Land_admin.prototype.point.splice(Land_admin.prototype.point.length-2);
                        Land_admin.prototype.pt.splice(Land_admin.prototype.pt.length-1);
                        Land_admin.prototype.count--;
                    }
                }
            }
        });
    }

Land_admin.prototype.clearMap = function(){
    for(i=0;i<Land_admin.prototype.pt.length;i++){
        map.removeLayer(Land_admin.prototype.pt[i]);
    }
    for(i=0;i<Land_admin.prototype.polyline.length;i++){
        map.removeLayer(Land_admin.prototype.polyline[i]);
    }
    Land_admin.prototype.point=[];
    Land_admin.prototype.pt=[];
}

//выведем список всех коттежей
Land_admin.prototype.show_list_cottage = function(){
    $.ajax({
            type:"post",
            url:"/land/ajax/getCottages",
            dataType:"json",
            success:function(html){
                if(html.state==="ok"){
                    $("#addCottage").hide();
                    $("#editCottage").hide();
                    Land_admin.prototype.cottages = html.cottages;
                    $("#size").height(document.documentElement.clientHeight-150);
                    $("#list_cottage tbody tr").remove();
                    for(i=0;i<Land_admin.prototype.cottages.length;i++){
                       $("#list_cottage tbody").append("\n\
                            <tr>\n\
                                <td>"+Land_admin.prototype.cottages[i].name_cottage+"</td>\n\
                                <td>\n\
                                    <div onclick='land_admin.show_edit_cottage(this)' \n\
                                        data-index='"+i+"' \n\
                                        data-id='"+Land_admin.prototype.cottages[i].cottage_id+"' class='btn btn-primary'>\n\
                                        редактировать\n\
                                    </div></td>\n\
                                <td>\n\
                                    <div onclick='land_admin.delete_cottage(this)' data-id='"+Land_admin.prototype.cottages[i].cottage_id+"' class='btn btn-danger'>\n\
                                        удалить\n\
                                    </div></td>\n\
                                </td>\n\
                            </tr>\n\
                        "); 
                    }
                    $("#table").show();
                }
            }
        });
}

//добавим землю в бд
Land_admin.prototype.add_cottage = function(){
    if(Land_admin.prototype.point.length>0){
        $.ajax({
            type:"post",
            url:"/admin/ajaxMap/addCottage",
            data:"points="+Land_admin.prototype.point
                    +"&name="+$("#name_cottage").val()
                    +"&city="+$("#city_cottage").val()
                    +"&waterboby="+$("#waterbody_cottage").val()
                    +"&forest="+$("#forest_cottage").val()
                    +"&owner="+$("#owner_cottage").val()
                    +"&price="+$("#price_cottage").val()
                    +"&email="+$("#email_cottage").val()
                    +"&site="+$("#site_cottage").val()
                    +"&phone="+$("#phone_cottage").val()
                    +"&about="+$("#about_cottage").val(),
            success:function(html){
                if(html==="1"){
                    $(".input").val("");
                    for(i=0;i<Land_admin.prototype.pt.length;i++){
                        map.removeLayer(Land_admin.prototype.pt[i]);
                    }
                    for(i=0;i<Land_admin.prototype.polyline.length;i++){
                        map.removeLayer(Land_admin.prototype.polyline[i]);
                    }
                    Land_admin.prototype.point=[];
                    Land_admin.prototype.pt=[];
                }else{
                    alert("не удалось добавить попробуйте перезагрузить страницу или зовите программистов");
                }
            }
        });
    }else{
        alert("Выделите область");
    }
     
}

//покажем форму редактирования маршрута
Land_admin.prototype.show_edit_cottage = function(th){
    Land_admin.prototype.show_modal("#editCottage");
    //console.log(Land_admin.prototype.cottages[$(th).data("index")]);
    $("#id").val(Land_admin.prototype.cottages[$(th).data("index")].cottage_id);
    $("#index").val($(th).data("index"));
    $("#edit_name_cottage")
            .val(Land_admin.prototype.cottages[$(th).data("index")].name_cottage);
    $("#edit_city_cottage [value='"
            +Land_admin.prototype.cottages[$(th).data("index")].city_id+"']")
            .attr("selected", "selected");
    $("#edit_waterbody_cottage [value='"
            +Land_admin.prototype.cottages[$(th).data("index")].waterbody+"']")
            .attr("selected", "selected");
    $("#edit_forest_cottage [value='"
            +Land_admin.prototype.cottages[$(th).data("index")].forest+"']")
            .attr("selected", "selected");
    $("#edit_price_cottage")
            .val(Land_admin.prototype.cottages[$(th).data("index")].price);
    $("#edit_email_cottage")
            .val(Land_admin.prototype.cottages[$(th).data("index")].email);
    $("#edit_site_cottage")
            .val(Land_admin.prototype.cottages[$(th).data("index")].site);
    $("#edit_phone_cottage")
            .val(Land_admin.prototype.cottages[$(th).data("index")].phone);
    $("#edit_about_cottage")
            .val(Land_admin.prototype.cottages[$(th).data("index")].about);
    //рисуем маршрут
    for(i=0;i<Land_admin.prototype.cottages[$(th).data("index")].coordinates.length;i++){
        Land_admin.prototype.pt.push(L.marker(
                [
                    parseFloat(Land_admin.prototype.cottages[$(th).data("index")].coordinates[i].lat),
                    parseFloat(Land_admin.prototype.cottages[$(th).data("index")].coordinates[i].lon)
                ]
                ).addTo(map));
        Land_admin.prototype.point.push(
                parseFloat(Land_admin.prototype.cottages[$(th).data("index")].coordinates[i].lat),
                parseFloat(Land_admin.prototype.cottages[$(th).data("index")].coordinates[i].lon)
                );
        if(Land_admin.prototype.point.length===4) {
            Land_admin.prototype.polyline[Land_admin.prototype.count] = 
                    L.polyline([
                        [Land_admin.prototype.point[0],Land_admin.prototype.point[1]],
                        [Land_admin.prototype.point[2],Land_admin.prototype.point[3]]
                    ], {color: 'red'}).addTo(map);
        }
        if(Land_admin.prototype.point.length>4) {
            Land_admin.prototype.count=Land_admin.prototype.count+1;
            Land_admin.prototype.polyline[Land_admin.prototype.count]=L.polyline([
                [Land_admin.prototype.point[Land_admin.prototype.point.length-4],Land_admin.prototype.point[Land_admin.prototype.point.length-3]],
                [Land_admin.prototype.point[Land_admin.prototype.point.length-2],Land_admin.prototype.point[Land_admin.prototype.point.length-1]]
            ], {color: 'red'}).addTo(map);
        }  
    }
}

//отредактируем маршрут
Land_admin.prototype.edit_cottage = function(){
    ind=$("#index").val();
    Land_admin.prototype.cottages[ind].name_cottage=$("#edit_name_cottage").val();
    Land_admin.prototype.cottages[ind].city_id=$("#edit_city_cottage :selected").val();
    Land_admin.prototype.cottages[ind].waterbody=$("#edit_waterbody_cottage :selected").val();
    Land_admin.prototype.cottages[ind].forest=$("#edit_forest_cottage :selected").val();
    Land_admin.prototype.cottages[ind].price=$("#edit_price_cottage").val();
    Land_admin.prototype.cottages[ind].email=$("#edit_email_cottage").val();
    Land_admin.prototype.cottages[ind].site=$("#edit_site_cottage").val();
    Land_admin.prototype.cottages[ind].phone=$("#edit_phone_cottage").val();
    Land_admin.prototype.cottages[ind].about=$("#edit_about_cottage").val();
    Land_admin.prototype.cottages[ind].coordinates= [];
    for(i=0;i<Land_admin.prototype.point.length;i+=2){
        Land_admin.prototype.cottages[ind].coordinates.push({
            "lat":Land_admin.prototype.point[i],
            "lon":Land_admin.prototype.point[i+1]
        });
    }
    $.ajax({
            method: "POST",
            url: "/admin/ajaxMap/updateCottage",
            data:"cottage_id="+Land_admin.prototype.cottages[ind].cottage_id
                +"&name_cottage="+Land_admin.prototype.cottages[ind].name_cottage
                +"&city_id="+Land_admin.prototype.cottages[ind].city_id
                +"&waterbody="+Land_admin.prototype.cottages[ind].waterbody
                +"&forest="+Land_admin.prototype.cottages[ind].forest
                +"&price="+Land_admin.prototype.cottages[ind].price
                +"&email="+Land_admin.prototype.cottages[ind].email
                +"&site="+Land_admin.prototype.cottages[ind].site
                +"&phone="+Land_admin.prototype.cottages[ind].phone
                +"&about="+Land_admin.prototype.cottages[ind].about
                +"&coordinates="+Land_admin.prototype.point,
            success: function(html){
                if(html==="1"){
                    $("#editCottage").hide();
                    Land_admin.prototype.clearMap();
                }
            }
        });
    
}

//удалим коттедж
Land_admin.prototype.delete_cottage = function(th){
    var result = confirm("Вы уверены что хотите удалить запись?");
    if(result){
        $.ajax({
            method: "POST",
            url: "/admin/ajaxMap/deleteCottage",
            data:"id="+$(th).data("id"),
            success: function(html){
                if(html==="1"){
                    $(th).parent().parent().remove();
                }else{
                    alert("Something wrong! Try later again or call support!");
                }
                $("#load").hide();
            }
        });
    }
}

Land_admin.prototype.click_close = function(th){
    $(th).parent().parent().hide();
}

Land_admin.prototype.show_modal = function(name){
    $(".input").val("");
    $("#table").hide();
    $.ajax({
            type:"post",
            url:"/admin/ajaxMap/getCity",
            dataType:"json",
            success:function(html){
                console.log(html);
                if(html.state==="ok"){
                    $("#city_cottage option").remove();
                    $("#edit_city_cottage option").remove();
                    for(i=0;i<html.city.length;i++){
                        $("#city_cottage").append("<option value='"+html.city[i].city_id+"'>"+html.city[i].name_city+"</option>");
                        $("#edit_city_cottage").append("<option value='"+html.city[i].city_id+"'>"+html.city[i].name_city+"</option>");
                    }
                    $(name).show();
                }
                
            }
        });
    
}

land_admin = new Land_admin();