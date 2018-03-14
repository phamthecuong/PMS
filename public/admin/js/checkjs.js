function checkvalue(value, id) {
    var confirm;
    var name;
    var email;
    for (var i = 0; i < value.length; i++) {
        if (value[i] == 'confirm')
        {
            confirm = document.getElementsByName("confirm")[0].value;
        }
        if (value[i] == 'name')
        {
            name = document.getElementsByName("name")[0].value;
        }
        if (value[i] == 'email')
        {
            email = document.getElementsByName("email")[0].value;
        }
    }
    for (var i = 0; i < value.length; i++) {
        var varible = document.getElementsByName(value[i])[0].value;
        if (value[i] == 'name')
        {
            if (varible == null || varible.trim()=="") 
            { 
                bootbox.alert({
                    message: name_not_null,
                })
                return false;
            }
        }
        if (value[i] == 'password')
        {
            if (varible == null || varible.trim() == "") 
            {
                bootbox.alert({
                    message: password_not_null,
                })
                return false;
            }

            if (varible.length < 6)
            {
                bootbox.alert({
                    message: leng_password,
                })
                return false;
            }

            if (varible != confirm)
            {
                bootbox.alert({
                    message: password_not_confirm,
                })
                return false;
            }
        }
    }
    $.ajax({
        type: "GET",
        url: '/check_js',
        data: {user_name: name, email: email, id: id},
    }).done(function( msg ) {
        if(msg == 'user') {
            bootbox.alert({
                message: username_coincided,
            })
           return false;
        }
        else if(msg == 'email')
        {
            bootbox.alert({
                message: email_coincided,
            })
            return false;
        }
        else
        {
            document.getElementById("submit").submit();
        }
    });
}