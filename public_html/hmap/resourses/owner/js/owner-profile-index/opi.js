function opi(){
        
}
opi.prototype.saveProfile = function(th){
    $.ajax({
        method:"post",
        url:"/owner/profile/saveProfile",
        data: new FormData( th ),
        processData: false,
        contentType: false,
        dataType:"json",
        success:function(html){
            console.log(html);
        }
    });
    return false;
}

//скрываем пказываем окно
opi.prototype.hide = function(name){
    $(name).hide("400");
}
opi.prototype.show = function(name){
    $(name).show("400");
}
opi.prototype.show_or_hide = function(name){
    if($(name).is(':visible')){
        this.hide(name);
    }else{
        this.show(name);
    }
}
