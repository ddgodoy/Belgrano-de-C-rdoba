var poll_delete_path = null;

function poll_delete_modal(path) {
    poll_delete_path = path;
    $('#form-modal-delete').modal('show');
}

function poll_delete_submit() {
   //alert($('#answer').val());
    if ($('#answer').val() === '') {
        return false;
    }
    
    $.ajax({
        type: 'POST',
        async: true,
        cache: false,
        dataType: "json",
        url: poll_delete_path,
        
        success: function (response) {
           location.reload();
        }
    });
}