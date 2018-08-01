function deleteImage(image_id, gallery_name, accomodation_name)
{
    if(isNumber(image_id))
    {
         $.get("delete-image", {im_id: image_id, gal_name:gallery_name, ac_name:accomodation_name}, function(result){
               if(result)
               {
                   $('#img'.concat(image_id)).remove();
               }
        });
    }
}

function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
$('#type-delete').keyup(function(){
   let val = $('#type-delete').val();
   if(val == 'DELETE' || val == 'delete' || val == 'Delete')
   {
       $('#btnYes').prop('disabled', false);
   }
});

$('#myModal').on('show', function() {
    let id = $(this).data('id');
})

$('.confirm-delete').on('click', function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    $('#myModal').data('id', id).show();
});

$('#btnYes').click(function() {
  	let id = $('#myModal').data('id');
        $.post('delete-gallery', {id: id}, function(result){
              if(result['status'] == false)
              {
                  location.reload();
              }
        });
        
});


/*
 * Modal popup
 */
function showModalMessage(message) {
    // Get the snackbar DIV
    let x = document.getElementById("snackbar");
    console.log(x);
    // Add the "show" class to DIV
    x.textContent = message;
    x.className = "show";
    console.log(message);
    

    // After 3 seconds, remove the show class from DIV
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
}