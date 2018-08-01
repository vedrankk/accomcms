/*
 * If there is a ID in the URL selectes that Accomodation from the first select, and filters the languages in the second select
 */
$( document ).ready(function() {
    let url = window.location.href;
    let id = 0;
    id = url.substring(url.indexOf('?id=')).replace(/\D/g,'');
    if(id != '')
    {
        $("#accomlanguages-accomodation_id").val(id).trigger("change");
        filterLangs(id);
    }
});

/*
 * Filters the languages in the second select depending what accomodation is selected
 * Displays the languages in the second select
 */
function filterLangs(id)
{
     $.post("accom-langs", {id: id}, function(result){
    	$('#accomlanguages-lang_id').empty();
        $('#selectedLangs').empty();
        $('#accomlanguages-lang_id').append('<option value="">' + 'Choose' + '</option>');
        let langs = result.langs;
        for(let i = 0; i < langs.length; i++)
        {
            $('#accomlanguages-lang_id').append('<option value="' + langs[i]['lang_id'] +'">' + langs[i]['name'] + '</option>');
        }
    });
}

/*
 * When a language in the second select is clicked, adds it to the #selectedLangs div
 * Also adds a checkbox to make it the default value, and a remove button to remove it
 * Removes that language from the select
 */
function addLang(id, text, index)
{
    let remove = "#rm".concat(id);
    let append = '<div id="rm'+id+'"><hr> <h3 id="name" class="text-success">'+text+'</h3>'
               +  '<span class="checkbox" ><label class="text-primary"><input type="checkbox" id="check" onclick="selectDefault(this)" name="default_lang_id" value="1">Default</label></p>'
               + ' <p class="text-danger">Remove <a href="#!" onclick="remove('+id+')" ><i style="font-size: 25px; color: red;" class="glyphicon glyphicon-remove"></i> </a></p>'
               + '</div><br>';
    $('#selectedLangs').append(append);
    $("#accomlanguages-lang_id option[value='"+id+"']").remove();
}

/*
 * Removes the language from the selectedLangs div
 * Adds the language to the select
 */
function remove(id)
{
    const point = '#rm'.concat(id);
    let text = $(point + ' #name').text();
    $(point).remove();
    $('#accomlanguages-lang_id').append('<option value="' + id +'">' + text + '</option>');
    let i = 0;
    $('#selectedLangs').find('input:checkbox:first').each(function(){
        if(i === 0){selectDefault(this); return false;}
        i++;
    });
}

/*
 * This function makes sure only one checkbox can be selected
 */
function selectDefault(check)
{
  jQuery('#selectedLangs #check').prop('checked', false);
  jQuery(check).prop('checked', true);
}

/*
 * Saves the selected languages
 * Gets the selected accomodation ID
 * Goes through the #selectedLangs div and gets the ids of the selected languages
 * Also Gets the value of the make default checkbox for each language
 * If there is no default language selected, the first language from the div will be made default
 * Sends the array of data to PHP
 * [{'default' => 0/1, 'lang'=> *LANG ID*}]
 */
function save()
{
    let accomId = $('#accomlanguages-accomodation_id').val();
    let langs = [];
    let defaultLang = 0;
    let defaultLangCheck = 0;
    $('#selectedLangs').find('div').each(function(){
        let langId = this.id.replace('rm', '');
        let defaultLangVal = $('#' + this.id + ' #check').is(':checked');
        if(defaultLangVal === true)
        {
            defaultLang = 1;
            defaultLangCheck = 1;
        }
        else{
            defaultLang = 0;
        }
        let values = {'default' : defaultLang, 'lang' : langId};
        langs.push(values);
    });
    
    if(defaultLangCheck == 0)
    {
        langs[0]['default'] = 1;
    }
        $.post("accom-langs-create", {accomId: accomId, langs: langs}, function(result){
            
        });
//    console.log(langIds);
//    window.location.href ='index';
}

