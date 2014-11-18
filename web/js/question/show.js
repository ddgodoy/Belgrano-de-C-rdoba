var id_question = null;
var id_poll = null;
var id_answer = null;
var question = '';

function add_answer_modal(id, id_p) {
    id_question = id;
    id_poll = id_p;
    $('#form-modal').modal('show');
}

function add_answer_submit() {

    if ($('#answer').val() === '') {
        return false;
    }
    
    id_question = $('#id_question').val();
    id_poll = $('#id_poll').val();
    
    $.ajax({
        type: 'POST',
        async: true,
        cache: false,
        dataType: "json",
        url: base_url + '/admin/question/'+id_question+'/show',
        data: {answer: $('#answer').val(), 'id_question': id_question, 'id_poll': id_poll},
        success: function (response) {
           location.reload();
        }
    });
}

function edit_answer_modal(id_a, id_q, id_p, a) {
    id_answer = id_a;
    id_poll = id_p;
    id_question = id_q;
    
    answer = a;
    $('#edit_answer').val(answer);
    $('#form-modal-edit').modal('show');
}

function edit_answer_submit() {

    if ($('#edit_answer').val() === '') {
        return false;
    }
    
    id_question = $('#id_question_edit').val();
    id_poll = $('#id_poll_edit').val();
    
    $.ajax({
        type: 'POST',
        async: true,
        cache: false,
        dataType: "json",
        url: base_url + '/admin/question/'+id_question+'/show',
        data: {'answer': $('#edit_answer').val(), 'id_answer': id_answer, 'id_poll': id_poll, 'id_question': id_question, 'edit': true},
        success: function (response) {
           location.reload();
        }
    });
}

function delete_answer_modal(id_a, a) {
    
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
        url: base_url + '/admin/question/'+id_question+'/show',
        data: {'id_answer': id_answer, 'delete': true},
        success: function (response) {
           location.reload();
        }
    });
}
