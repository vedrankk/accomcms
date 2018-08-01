/*
 * Get's the lang param from the url, if not exists set's it to 1
 * @return int
 */
function getLangFromUrl()
{
    let url = window.location.href;
    let lang = url.substring(url.indexOf('?lang=')).replace(/\D/g,'');
    if(lang == ''){lang = 1; }
    return lang;
}

function getSelectecAccomodation()
{
    return $('#w0').val();
}

/*
 * Gets the data of the selected accomodation and displays it
 */
function getData(id)
{
    let lang = getLangFromUrl();
     $.post("overview/accom-data", {id: id, lang: lang}, function(result){
         console.log(result);
         let data = result.data;
         let emailData = result.emailData;
         let langs = result.accomLangs;
         let services = result.services;
         
//         displayInfoData(id, data, lang);
//         displaySocialData(id, data);
         displayAccomodationData(data);
         displayEmails(emailData);
         displayAccomLangs(langs);
         displayAccomServices(services);
         
         $('#accomData').show();
         $('#emails').show();
         $('#newEmail').hide();
         $('#accomLangs').show();
         $('#accomServices').show();
         
    });
}

/*
 * 
 * @param {array} data - Array of values for the Accomodation
 * Displays the accomodation data on the page, and populates the edit fields with that data
 */
function displayAccomodationData(data)
{
    clearAccomodaionSocialData();
    
    displayAccomodationSocialData('Facebook', data.facebook);
    displayAccomodationSocialData('Youtube', data.youtube);
    displayAccomodationSocialData('Twitter', data.twitter);
    
    $('#accomDescription').html(data.description + returnAccomodationEditString('description'));
    $('#descriptionVal').html(data.description);
    
    $('#accomName').html(data.name + returnAccomodationEditString('name'));
    $('#nameVal').val(data.name);
    
    $('#accomAddress').html(data.address + returnAccomodationEditString('address'));
    $('#addressVal').val(data.address);
    showAccomodationData();
}

/*
 * Clears the text from the fields
 * @returns {undefined}
 */
function clearAccomodaionSocialData()
{
    $('#accomFacebook').html('');
    $('#accomYoutube').html('');
    $('#accomTwitter').html('');
}

function clearAccomodationSocialEdit()
{
    $('#facebookVal').val('');
    $('#youtubeVal').val('');
    $('#twitterVal').val('');
}

function displayAccomodationSocialData(name, data)
{
    if(data == '')
    {
        socialNetworkEmpty(name);
    }
    else{
        $('#accom'.concat(name)).html('<a href="' +data + '">' + data + '</a>' + returnAccomodationEditString(name)+ returnAccomodationDeleteString(name));
    }
}

function socialNetworkEmpty(name)
{
    $('#accom'.concat(name)).addClass(' text-danger');
    $('#accom'.concat(name)).html('Empty ' + returnAccomodationEditString(name));
}

/*
 * Created the edit string
 * @param {string} field
 * @returns {String}
 */
function returnAccomodationEditString(field)
{
    if(getLangFromUrl() != 1)
    {
        return '';
    }
    return '<a href="#!" onclick="editAccomodationData('+editString(field.toLowerCase())+')" ><span class="glyphicon glyphicon-pencil"></span></a>';
}

function returnAccomodationDeleteString(field)
{
    return '<a href="#!" onclick="deleteAccomodationSocialData('+editString(field.toLowerCase())+')" ><span class="glyphicon glyphicon-trash"></span></a>';
}

/*
 * Facebook, Description itd..Created the string that goes in the onclick function
 * Reason for this: if I wrote onclick="editAccomodationData('Facebook')" the HTML would not recognize the parametar inside
 * @param {string} field
 * @returns {String}
 */
function editString(field)
{
    return "'".concat(field).concat("'");
}

/*
 * Hides every edit field and shows the textual fields for the Accomodation data
 */
function showAccomodationData()
{
    $('#accomFacebook').show();
    $('#accomYoutube').show();
    $('#accomTwitter').show();
    $('#accomDescription').show();
    $('#accomName').show();
    $('#accomAddress').show();
    
    $('#editFacebook').hide();
    $('#editTwitter').hide();
    $('#editYoutube').hide();
    $('#editDescription').hide();
    $('#editName').hide();
    $('#editAddress').hide();
}

/*
 * Saves the Accomodation data
 * Gets the data from the input edit fields and sends it to PHP
 */
$('#saveData').click(function(){
    let facebook = $('#facebookVal').val();
    let twitter = $('#twitterVal').val();
    let youtube = $('#youtubeVal').val();
    let name = $('#nameVal').val();
    let description = $('#descriptionVal').val();
    let address = $('#addressVal').val();
    let values = {accom_id: getSelectecAccomodation(), lang_id : getLangFromUrl(), facebook : facebook, twitter : twitter, youtube : youtube, name : name, description : description, address : address,}
     $.post("overview/save-accomodation-data", values, function(result){
                if(result['code'] == 111)
                {
                    displayAccomodationData(result['modelData']);
                    clearAccomodationSocialEdit();
                }
                showModalMessage(result['msg']);
        });
});


/*
 * Detects which field we want to edit and calls the function for displaying the edit field
 * @param {string} field
 */
function editAccomodationData(field)
{
    switch(field)
    {
        case 'facebook':
           accomodationEditShow('Facebook');
        break;
        
        case 'twitter':
           accomodationEditShow('Twitter');
        break;
        
        case 'youtube':
            accomodationEditShow('Youtube');
        break;
        
        case 'description':
           accomodationEditShow('Description');
        break;
        
        case 'name':
            accomodationEditShow('Name');
        break;
        
        case 'address':
           accomodationEditShow('Address');
        break;
    }
}

/*
 * Displays the edit field for the chosen value
 * @param {string} field
 */
function accomodationEditShow(field)
{
    $('#accom'.concat(field)).hide();
    $('#edit'.concat(field)).show();
}

/*
 * If we choose to not edit the field after all, this hides the edit field and shows the text.
 * Also, changes back the value of the edit field to the original value from the text field.
 * @param {string} e
 */
function cancelEdit(e)
{
    console.log(e);
    $('#edit'.concat(e)).hide();
    $('#accom'.concat(e)).show();
    if(e != 'Facebook' && e!= 'Twitter' && e!= 'Youtube'){
        $('#'.concat(e.toLowerCase()).concat('Val')).html($('#accom'.concat(e)).text());
        $('#'.concat(e.toLowerCase()).concat('Val')).val($('#accom'.concat(e)).text());
    }
    else{
         $('#'.concat(e.toLowerCase()).concat('Val')).val('');
    }
}

function deleteAccomodationSocialData(field)
{
    let value = {accom_id : getSelectecAccomodation()};
    value['delete'] = field;
    $.post("overview/delete-accomodation-social-data", value, function(result){
            if(result['code'] == 111)
            {
                socialNetworkEmpty(capitalizeFirstLetter(value['delete']))
            }
            showModalMessage(result['msg']);
        });
}


/*
 * Loops through the email data and displays the email div
 * @param {object} data
 */
function displayEmails(data)
{
    $('#accomEmails').html('');
    data.forEach(function(e){
        displayEmailDiv(e.emails_id, e.email);
    });
}


/*
 * Displays the email div containt the data
 * @param {int} id
 * @param {string} email
 * @returns {undefined}
 */
function displayEmailDiv(id, email)
{
        let divId = 'div'.concat(id);
        $('#accomEmails').append('<div id="'+divId+'">'+createEmailHtml(id, email)+'</div>');
}

/*
 * Creates the HTML for the Email Div, edit and delete buttons for the current row
 * @param {{int} id
 * @param {string} email
 * @returns {String}
 */
function createEmailHtml(id, email)
{
    let divId = 'div'.concat(id);
    let pId = 'pid'.concat(id);
    let editEmailString = '"'.concat(divId).concat('",').concat(id);
    return '<p id = "'+pId+'"><a href="#!">' + email + '</a> ' +
                                "</a> <a href='#!' onclick='showEmailEdit("+id+")' ><span class='glyphicon glyphicon-pencil'></span></a>" +
                                "</a> <a href='#!' onclick='deleteEmail("+id+")' ><span class='glyphicon glyphicon-trash'></span></a></p>" +
                                "<div style='display: none;' id='edit"+divId+"'>" + editEmail(divId, id) + "<a href='#!' onclick='cancelEmailEdit("+id+")'><i class='glyphicon glyphicon-remove-sign'></i></a></div>";
}

/*
 * Removes the div for adding new email
 * @returns {undefined}
 */
function cancelNewEmail()
{
    $('#newEmail').hide();
}

/*
 * Shows the div for editing the email
 * @param {int} id
 * @returns {undefined}
 */
function showEmailEdit(id)
{
    $('#pid'.concat(id)).hide();
    $('#editdiv'.concat(id)).show();
}

/*
 * Hides the div for editing the email
 * @param {int} id
 * @returns {undefined}
 */
function cancelEmailEdit(id)
{
    $('#pid'.concat(id)).show();
    $('#editdiv'.concat(id)).hide();
}

/*
 * Displays the input for editing the Email value
 * @param {string} divId
 * @param {int} email_id
 * @returns {undefined}
 */
function editEmail(divId, email_id)
{
    return '<input type="email" id="inpute'+ email_id + '" placeholder="Edit email..."> <button class="btn btn-success" onclick="saveEmail('+email_id+')">Save</button>';
}

/*
 * Tries to save the edited email value. If succes displays the value.
 * @param {int} email_id
 * @returns {undefined}
 */
function saveEmail(email_id)
{
    let inputId = '#inpute'.concat(email_id);
    let emailVal = $(inputId).val();
    if(emailVal >= 255)
    {
        showModalMessage('Too long!');
    }
    
    if(emailVal != '')
    {
        $.post("overview/accom-email-edit", {id: email_id, email: emailVal}, function(result){
                if(result == 111){
                    showModalMessage('Success!');
                    $('#div'.concat(email_id)).html(createEmailHtml(email_id, emailVal));
                }
                else{
                    showModalMessage('Something went wrong!');
                }
            
        });
    }
    else
    {
        showModalMessage("You can't save an empty value!");
    }
}

/*
 * Tries to delete the email
 * @param {int} id
 * @returns {undefined}
 */
function deleteEmail(id)
{
    $.post("overview/accom-email-delete", {id: id}, function(result){
                  if(result['code'] == 111){
                    $('#div'.concat(id)).remove();
                    showModalMessage(result['msg']);
                  }
                  else{
                    showModalMessage(result);
                  }
        });
    
}

/*
 * Shows the form for the new email
 */
$("#showMailForm").click(function(){
    $('#newEmail').show();
});

/*
 * Tries to add a new email for the current accomodation.
 * Takes the title and email values from input.
 */
$("#addMail").click(function(){
    let accomodationId = $('#w0').val();
    let email = $('#emailVal').val();
    let title = $('#emailTitle').val();
    if(email != '' && title != '')
    {
        $.post("overview/accom-email-new", {email: email, title: title, accom_id: accomodationId}, function(result){
            showModalMessage(result['msg']['msg']);
            if(result['msg']['code'] == 111)
            {
                $('#newEmail').hide();
                displayEmailDiv(result.key, email);
            }
            
            $('#emailVal').val('');
            $('#emailTitle').val('');
            
        });
    }
});

/*
 * Loops the accomodation languages and displays them.
 * @param {object} data
 * @returns {undefined}
 */
function displayAccomLangs(data)
{
    $('#accomLangsList').html('');
    data.forEach(function(e){
        displayAccomLangsDiv(e.name, e.accom_languages_id, e.default_lang_id);
    });
}

/*
 * Displays the single language
 * If the language is the default language for acoomodation diplays the 'Default' in red color on the right side.
 * {lang_name} {delete_button} {default}
 * @param {string} lang_name
 * @param {int} id
 * @param {int} defaultLang
 * @returns {undefined}
 */
function displayAccomLangsDiv(lang_name, id, defaultLang)
{
    let defaultString = '';
    if(defaultLang != 0)
    {
        defaultString = '<span class="text-danger">Default</span>';
    }
    $('#accomLangsList').append('<p id="lang'+id+'">' + lang_name + '  <a href="#!" onclick="deleteLang('+id+')"><i class="glyphicon glyphicon-trash"></i></a> '+defaultString+'</p>');
}

/*
 * Tries to delete the language.
 * @param {int} id
 * @returns {undefined}
 */
function deleteLang(id)
{
    let accomodationId = $('#w0').val();
    $.post("overview/accom-langs-delete", {id: id}, function(result){
            showModalMessage(result);
            getData(accomodationId);
        });
}

/*
 * Loops through the services and displays them.
 * @param {object} data
 * @returns {undefined}
 */
function displayAccomServices(data)
{
    $('#accomServicesList').html('');
    data.forEach(function(e){
        displayAccomServicesDiv(e);
    });
}

/*
 * Displays the checkbox for the service
 * If the accomodation has the service, the checkbox is checked.
 * When the checkbox is checked or unchecked the service is added or deleted from the database
 * @returns {undefined}
 */
function displayAccomServicesDiv(data)
{
    let checked = '';
    if(typeof data.checked !== 'undefined')
    {
        checked = 'checked';
    }
    $('#accomServicesList').append('<p><input id="'+data.services_id+'" onchange="changeService(this)" type="checkbox" '+checked+' >' + data.name + '</p>');
    
}

function changeService(input)
{
    let id = input.id;
    let accomodationId = $('#w0').val();
    $.post("overview/accom-services-change", {id: id, accom_id: accomodationId}, function(result){
           showModalMessage(result);
        });
    
}

/*
 * Modal popup
 */
function showModalMessage(message) {
    // Get the snackbar DIV
    let x = document.getElementById("snackbar");

    // Add the "show" class to DIV
    x.textContent = message;
    x.className = "show";
    

    // After 3 seconds, remove the show class from DIV
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
}


function capitalizeFirstLetter(s)
{
    return s && s[0].toUpperCase() + s.slice(1);
}
