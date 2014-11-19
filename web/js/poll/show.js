var id_poll = null;
var id_question = null;
var question = '';

function add_question_modal(id) {
    id_poll = id;
    $('#form-modal').modal('show');
}

function add_question_submit() {

    if ($('#question').val() === '') {
        return false;
    }
    
    $.ajax({
        type: 'POST',
        async: true,
        cache: false,
        dataType: "json",
        url: base_url + '/admin/poll/'+id_poll+'/show',
        data: {question: $('#question').val(), 'id_poll': id_poll},
        success: function (response) {
           location.reload();
        }
    });
}

function edit_question_modal(id_q, id_p, q) {
    id_poll = id_p;
    id_question = id_q;
    question = q;
    $('#edit_question').val(question);
    $('#form-modal-edit').modal('show');
}

function edit_question_submit() {

    if ($('#edit_question').val() === '') {
        return false;
    }
    
    $.ajax({
        type: 'POST',
        async: true,
        cache: false,
        dataType: "json",
        url: base_url + '/admin/poll/'+id_poll+'/show',
        data: {'question': $('#edit_question').val(), 'id_poll': id_poll, 'id_question': id_question, 'edit': true},
        success: function (response) {
           location.reload();
        }
    });
}

function delete_question_modal(id_q, id_p, q) {
    id_poll = id_p;
    id_question = id_q;
    question = q;
    $('#question_text').text(q);
    $('#form-modal-delete').modal('show');
}

function delete_question_submit() {
    
    $.ajax({
        type: 'POST',
        async: true,
        cache: false,
        dataType: "json",
        url: base_url + '/admin/poll/'+id_poll+'/show',
        data: {'id_question': id_question, 'delete': true},
        success: function (response) {
           location.reload();
        }
    });
}


