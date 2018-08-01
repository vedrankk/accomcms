$('#save-email').on('click', function(){
    var email = $('#emails-email').val();
    var title = $('#emails-title').val();
    if(email !== '' && title !== '')
    {
        $.post('save-email', {email: email, title: title}, function(result){
            if(result['status'] === false)
            {
                Materialize.toast(result['msg'], 3000);
            }
            else{
                window.location = 'finalize';
            }
        });
    }
    else{
        Materialize.toast('Email and Title field cant be empty!', 3000);
    }
    
});

