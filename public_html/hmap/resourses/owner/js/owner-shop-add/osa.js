function osa(){
    
}

//добавление нового магазина
osa.prototype.addOrg =  function(th){
    
    //console.log(new FormData( th ));
    var data=new FormData( th );
    data.append("about",$('#summernote').code());
    
    $.ajax({
        method:"post",
        url:"/owner/shop/addOrg",
        data: data,
        processData: false,
        contentType: false,
        dataType:"json",
        success:function(){
            //console.log(html);
            location.reload();
        }
    });
    return false;
}

osa.prototype.searchAdress = function(th){
    console.log();
    if($(th).val().length!==0){
        $.getJSON("http://nominatim.openstreetmap.org/search?q="
            +$("#city :selected").text()
            +" "+$(th).val()
            +"&format=json", 
            function (data) {
                //console.log(data[0]);
                if(data.length===0){
                    $("#alert-adress").text("Указанный адрес не найден!");
                    $("#alert-adress").show();
                }else{
                    //console.log(data[0]);
                    $("#alert-adress").hide();
                    $("#lat").val(data[0].lat);
                    $("#lon").val(data[0].lon);
                }
            }
        );
    }  
}