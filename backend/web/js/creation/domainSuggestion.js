$(document).ready()
{
    
    var typingTimer;                
    var doneTypingInterval = 2000;  

    //set the bg of the box
    $('.domain').on('input', function(){
        $('#domain-valid').hide();
        clearTimeout(typingTimer);
        if ($('.domain').val()) {
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        }
    });

    //Ajax call to get the suggestions
    function doneTyping () {


        $.post("get-domain-suggestions", {domainUrl: $('.domain').val()}, function(result){
                if(result.status === true)
                {
                    $('#domain-valid').html('<p style="color:green">'+result.msg+'</p>'+'<p><button type="button" id="saveData" class="waves-effect waves-light btn">Proceed</button>');
                    $('#domain-valid').show();
                }
                else{
                    $('#domain-valid').html('<p style="color:red">'+result.msg+'</p>');
                    $('#domain-valid').show();
                }

            });
    } 
    
    $(document).on('click', '#saveData', function() {
        var domain = $('#domain').val();
        $('#domain-valid').hide();
        $.post('save-domain', {domainUrl: domain}, function(result){
            if(result.status === true)
            {
                window.location = 'choose-domain';
            }
            else{
                $('#domain-valid').html('<p style="color:red">'+result.msg+'</p>');
                $('#domain-valid').show();
            }
        });
    });
    
    
    }
    