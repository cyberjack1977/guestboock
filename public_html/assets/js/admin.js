$(function(){
    function access(){
        let pass = prompt('Введите parol:', 'parol');
        if(pass === 'parol'){
            alert('Доступ получен.');
            return pass;
        }
        else{
            alert('В доступе отказано.');
            access();
        }
    }
    function Templater(data, file, selector, method){
        $.post('/admin/source/template.php', {data: data, file: file}).done(function(html){
            if(method === 'append'){$(selector).append(html);}
            else if(method === 'prepend'){$(selector).prepend(html);}
            else{$(selector).html(html);}
        });
    }
    let PASS = access();
   
    $.post('./request/list.php', {pass: PASS}).done(function(data){
        ans = $.parseJSON(JSON.stringify(data));
        if(ans.error.status === 1){
            alert(ans.error.message);
        }
        else{
            Templater(ans.list, 'admin_list.php', 'body', 'html');
        }
    });
    
    $('body').on('click', 'button', function(){
        if(confirm('Выбранное сообщение будет удалено.') === true){
            let id = $(this);
            $.post('./request/delete.php', {pass: PASS, id: id.attr('data-message-id')}).done(function(data){
                ans = $.parseJSON(JSON.stringify(data));
                if(ans.error.status === 1){
                    alert(ans.error.message);
                }
                else{
                    id.parents('li').remove();
                }
            });
        }
    });
    
    
});