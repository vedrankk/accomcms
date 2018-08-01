$('.modal').modal({
      dismissible: true, // Modal can be dismissed by clicking outside of the modal
      opacity: .5, // Opacity of modal background
      inDuration: 300, // Transition in duration
      outDuration: 200, // Transition out duration
      startingTop: '4%', // Starting top style attribute
      endingTop: '10%' // Ending top style attribute
    }
  );
  
function showMore(id)
{
    $.post('template-details', {template_id: id}, function(result){
        if(result['status'] !== null)
        {
            $('.slides').html('');
            $('.single-image').hide();
            $('.slider').hide();
            $('#template-name').html(result['name'] + ' v' + result['version']);
            $('#template-description').html(result['description']);
            $('#live-preview').html('Live preview : <a target="_blank" href="template-preview?id='+result['template_id']+'">Preview</a>');
            //$('#template-preview').attr('src', result['preview']);
            if(typeof result['image_data'] !== 'undefined')
            {
               result['image_data'].forEach(function(item, index){$('.slides').append('<li><img src="'+item+'"></li>');});
               $('.slider').show();
            }
            else{
                $('.slider').hide();
                $('#template-preview').attr('src', result['preview']);
                $('.single-image').show();
            }
            $('.slider').slider();
            $('#modal1').modal('open');
        }
    });
}

 $(document).ready(function(){
      $('.slider').slider();
    });