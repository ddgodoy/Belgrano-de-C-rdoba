
$(document).ready(function () {
    $('#bdc_pollbundle_poll').validate({
        errorElement: 'span', //default input error message container
        errorClass: 'help-block help-block-error', // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        ignore: "", // validate all fields including form hidden input

        rules: {
            "bdc_pollbundle_poll[name]": {
                minlength: 6,
                required: true
            },
            "bdc_pollbundle_poll[status]": {
                required: true
                
            }
            
        },
        
        highlight: function (element) { // hightlight error inputs
            $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
        },
        unhighlight: function (element) { // revert the change done by hightlight
            $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
        },
        success: function (label) {
            label
                    .closest('.form-group').removeClass('has-error'); // set success class to the control group
        }
    }); 
});



