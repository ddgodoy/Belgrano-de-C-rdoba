var id_poll = null;

function add_answer_modal(id) {
    id_poll = id;
    $('#form-modal').modal('show');
}

function add_answer_submit() {
   //alert($('#answer').val());
    if ($('#answer').val() === '') {
        return false;
    }
    
    $.ajax({
        type: 'POST',
        async: true,
        cache: false,
        dataType: "json",
        url: base_url + '/admin/poll/'+id_poll+'/show',
        data: {answer: $('#answer').val(), 'id_poll': id_poll},
        success: function (response) {
           location.reload();
        }
    });
}

