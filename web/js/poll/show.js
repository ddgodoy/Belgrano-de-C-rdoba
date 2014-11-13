var id_poll = null;
var id_answer = null;
var answer = '';

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

function edit_answer_modal(id_a, id_p, a) {
    id_poll = id_p;
    id_answer = id_a;
    answer = a;
    $('#edit_answer').val(answer);
    $('#form-modal-edit').modal('show');
}

function edit_answer_submit() {
   //alert($('#answer').val());
    if ($('#edit_answer').val() === '') {
        return false;
    }
    
    $.ajax({
        type: 'POST',
        async: true,
        cache: false,
        dataType: "json",
        url: base_url + '/admin/poll/'+id_poll+'/show',
        data: {'answer': $('#edit_answer').val(), 'id_poll': id_poll, 'id_answer': id_answer, 'edit': true},
        success: function (response) {
           location.reload();
        }
    });
}

function delete_answer_modal(id_a, id_p, a) {
    id_poll = id_p;
    id_answer = id_a;
    answer = a;
    $('#answer_text').text(a);
    $('#form-modal-delete').modal('show');
}

function delete_answer_submit() {
    
    $.ajax({
        type: 'POST',
        async: true,
        cache: false,
        dataType: "json",
        url: base_url + '/admin/poll/'+id_poll+'/show',
        data: {'id_answer': id_answer, 'delete': true},
        success: function (response) {
           location.reload();
        }
    });
}


