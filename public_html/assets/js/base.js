$(function(){
   $('#formMessage').click(function(){
        $.get('/form.html', function(data){
            $('body').append(data);
        });
   });
   $('body').on('click', '#cancel', function(){
        $('.modal').remove();
   });
   $('body').on('click', '#refresh', function(){
        $('#captcha').attr('src', '/captcha.png?' + Math.random());
        return false;
   });
   $('body').on('click', '#send', function(){
        let error = false;
        let err_msg = '';
        let name = $('#name');
        let email = $('#email');
        let message = $('#message');
        let code = $('#code');
        $('.error').removeClass('error');
        if (!/^[a-zA-Z0-9]+$/.test(code.val())) {
            code.addClass('error').focus();
            err_msg += 'Код не задан или задан неверно.\n' + err_msg;
            error = true;
        }       
        if (!message.val()){
            message.addClass('error').focus();
            err_msg = 'Сообщение не задано.\n' + err_msg;
            error = true;
        }       
        if (!/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/.test(email.val())){
            email.addClass('error').focus();
            err_msg = 'Email не задан или задан неверно.\n' + err_msg;
            error = true;
        }
        if (!/^[a-zA-Z0-9]+$/.test(name.val())) {
            name.addClass('error').focus();
            err_msg = 'Имя должно состоять без пробелов, только из латинских букв и цифр.\n' + err_msg;
            error = true;
        }       
        
        if(error === true){
            alert('Обнаружены ошибки:\n' + err_msg);
        }
        else{
            $.ajax({ 
                type: 'POST',
                url: '/add.html',
                data: {name: name.val(), email: email.val(), message: message.val(), code: code.val()},
                dataType: 'json',
                success: function(data) {
                    if(data.err === 0){
                    $('.modal').remove();
                    $.get('/message.html', function(data) {
                        $('main ul, main nav').remove();
                        $('main').append(data);
                    });                    
                    }
                    else{
                        $('#captcha').attr('src', '/captcha.png?' + Math.random());
                        $('#code').val('');
                        alert('Обнаружены ошибки:\n' + data.err_msg);
                    }
                }
            });            
            
        }

   });
});