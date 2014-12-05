
function go_to_page(page, id_form) {
    $('#page').val(page);
    $('#' + id_form).submit();
}

$('#search').keypress(function (event) {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if (keycode === 13) {
        $('#page').val(1);
        $('#paginate-form').submit();
    }


});