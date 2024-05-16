"use strict";

function textFormat(symbols, data, replaceWith) {

    symbols     = symbols || null;
    replaceWith = replaceWith || ' ';

    var convertedString = data.replace(new RegExp(symbols.join('|'), 'g'), replaceWith).toLowerCase().replace(/(?:^|\s)\S/g, function(a) {

        return a.toUpperCase();
    });

    return convertedString;
}


$('.whatsapp-submit').on('click', function() { 

    const infoNoteBtn = document.querySelector(".info-note-btn");

        infoNoteBtn.addEventListener("click", ()=>{
        const noteContainer = document.querySelector(".note-container");
        noteContainer.classList.toggle("d-none");
    })

})


$('.whatsapp-submit').on('click', function() {
				 
    if ($('input[type=datetime-local][name=schedule_date]').val()) {

        const html = `
        <input hidden type="number" value ="2" name="schedule" id="schedule" class="form-control">`;
        $('.schedule').append(html);

    } else {
        
        const html = `
        <input hidden type="number" value ="1" name="schedule" id="schedule" class="form-control">`;
        $('.schedule').append(html);
    }
});

$("#file").change(function() {
    
    var contact_file = this.files[0];
    var file_name = "Selected: <p class='badge badge--primary'>" +contact_file.name +"</p>";
    $("#contact_file_name").html(file_name);
})

$('select[name=template]').on('change', function() {

    var character = $(this).val();
    $('textarea[name=message]').val(character);
    $('#templatedata').modal('toggle');
});



var selectType = $('#selectTypeChange');
var fileInput  = $('#uploadfile');

selectType.on('change', () => {
    var selectedValue = selectType.val();
    switch (selectedValue) {
    case 'file':
        fileInput.html('<input class="form-control" type="file" name="document" id="document" accept=".doc,.docx,.pdf">');
        break;
    case 'image':
        fileInput.html('<input class="form-control" type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.gif">');
        break;
    case 'audio':
        fileInput.html('<input class="form-control" type="file" name="audio" id="audio" accept=".mp3,.wav">');
        break;
    case 'video':
        fileInput.html('<input class="form-control" type="file" name="video" id="video" accept=".mp4,.mov,.avi">');
        break;
    default:
        fileInput.html('<input class="form-control" type="file" name="" id="file">');
        break;
    }
});

$(document).on('click','.whatsappBusinessApiEdit' ,function(e) {

    $("#edit_cred").empty();
    var credentials = $(this).data('credentials');
    const modal = $('#whatsappBusinessApiEdit');
    modal.find('input[name=id]').val($(this).attr('data-id'));
    modal.find('input[name=name]').val($(this).attr('data-name'));
    var html = ``;
    $.each(credentials, function(key, value) {

        html += `
            <div class="col-lg-12 mb-2">
                <label for="${key}">${textFormat(['_'],key)}<span class="text-danger">*</span></label>
                <input type="text" class="mt-2 form-control" name="credentials[${key}]" value="${value}" placeholder="Enter the ${key}">
            </div>
        `;
        
    });
    $("#edit_cred").append(html);
    modal.modal('show');
});

$(document).on('click','.generate-token' ,function(e) {

    e.preventDefault();
    var randomString = generateRandomString(32);
    $('#verify_token').val(randomString);
});

function generateRandomString(length) {

    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var result = '';
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    notify("info", "Code successfully generated.");
    return result;
}

$(document).on('click', '.copy-text', function(e) {
    var $inputField = $(this).closest('.input-group').find('.form-control');
    var inputField = $inputField[0]; 
    inputField.select();
    document.execCommand('copy');
    notify("success", "Callback URL copied successfully.");
    window.getSelection().removeAllRanges();
    e.preventDefault();
});


        
$(document).on('click', '.whatsappEdit', function(e){
    const modal = $('#whatsappEdit');
    modal.find('input[name=id]').val($(this).data('id'));
    modal.find('input[name=name]').val($(this).data('name'));
    modal.find('input[name=min_delay]').val($(this).data('min_delay'));
    modal.find('input[name=max_delay]').val($(this).data('max_delay'));
    modal.find('select[name=status]').val($(this).data('status'));
    modal.modal('show');
});

$(document).on('click', '.whatsappDelete', function(e){
    e.preventDefault()
    var id = $(this).attr('value')
    var modal = $('#whatsappDelete');
    modal.find('input[name=id]').val(id);
    modal.modal('show');
})

