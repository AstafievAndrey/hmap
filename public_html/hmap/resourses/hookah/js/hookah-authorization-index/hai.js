function hai(){
    
}

//восстановление логина и пароля
hai.prototype.reestablish = function(th){
    $.ajax({
        method:"post",
        url:"/hookah/authorization/reestablish",
        data: new FormData( th ),
        processData: false,
        contentType: false,
        dataType:"json",
        success:function(html){
            //console.log(html);
            if(parseInt(html.state)===1){
                $("#alert-reestablish-info").text(html.message);
                $('input').val('');
                $("#alert-reestablish-info").show().fadeOut(3000);
                setTimeout($("#reestablish").modal("hide"),4000); 
            }else{
                $("#alert-reestablish-info").text(html.message);
                $("#alert-reestablish-info").show().fadeOut(3000);
            }
        }
    });
    return false;
}

//вход
hai.prototype.input = function(th){
    $.ajax({
        method:"post",
        url:"/hookah/authorization/input",
        data: new FormData( th ),
        processData: false,
        contentType: false,
        dataType:"json",
        success:function(html){
            //console.log(html);
            if(parseInt(html.state)===1){
                $("#alert-enter-info").text(html.message);
                location.reload();
            }else{
                $("#alert-enter-danger").text(html.message);
                $("#alert-enter-danger").show().fadeOut(3000);
            }
        }
    });
    return false;
}

//регистрация
hai.prototype.enter=function (th){
    $.ajax({
        method:"post",
        url:"/hookah/authorization/add",
        data: new FormData( th ),
        processData: false,
        contentType: false,
        dataType:"json",
        success:function(html){
            //console.log(html);
            if(parseInt(html.state)===1){
                $("#alert-reg-info").text(html.message);
                location.reload();
                grecaptcha.reset();
            }else{
                $("#alert-reg-danger").text(html.message);
                $("#alert-reg-danger").show().fadeOut(3000);
                grecaptcha.reset();
            }
        }
    });
    return false;
}