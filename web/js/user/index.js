var user_delete_path = null;

function user_delete_modal(path) {
    user_delete_path = path;
    $('#form-modal-delete').modal('show');
}

function user_delete_submit() {
   //alert($('#answer').val());
    if ($('#answer').val() === '') {
        return false;
    }
    
    $.ajax({
        type: 'POST',
        async: true,
        cache: false,
        dataType: "json",
        url: user_delete_path,
        
        success: function (response) {
           location.reload();
        }
    });
}