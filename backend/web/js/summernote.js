$('#summernote').summernote({
  callbacks: {
    onImageUpload: function(files, editor, welEditable) {
        for(let i = 0; i < files.length; i++)
        {
            sendFile(files[i], editor, welEditable);
        }
        
    },
    onMediaDelete : function($target, editor, $editable) {
        console.log(typeof $target.context.dataset.filename);
        deleteImage($target.context.dataset.filename);
    }
  }
});
$('.dropdown-toggle').dropdown()

function deleteImage(url)
{
    $.ajax({
        data: {img_url: url},
        type: "POST",
        url: 'image-temp-delete',
        success: function (response) {
            console.log(response);
        }
    });
}

function sendFile(file, editor, welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
        data: data,
        type: "POST",
        url: 'image-temp',
        cache: false,
        contentType: false,
        processData: false,
        success: function (response) {
            if(response['status'] != null)
            {
                showModalMessage(response['msg']);
            }
            else
            {
                $('.note-editable').append('<img data-filename="' + response + '" src="' + response + '">');
            }
        }
    });
}

$('#w0').submit(function() {
    let summernoteContent = $('#summernote').summernote('code');
    let summernoteData = '';
    if(summernoteContent == '<p></p>')
    {
        return false;
    }
    else
    {
        summernoteData = '<p>'.concat($('#summernote').summernote('code')).concat('</p>');
        summernoteData = summernoteData.replace(/<p><p><\/p><\/p>/, '');
        summernoteData = summernoteData.replace(/<p><\/p>/, '');
    }
    $('#accomodationnews-news_text').val(summernoteData);
    
    return true;
});

/*
 * Modal popup
 */
function showModalMessage(message) {
    console.log(message);
    // Get the snackbar DIV
    let x = document.getElementById("snackbar");
    // Add the "show" class to DIV
    x.textContent = message;
    x.className = "show";
    

    // After 3 seconds, remove the show class from DIV
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
}
