$(document).ready(function () {
    $('#bdc_pollbundle_user').validate({
        errorElement: 'span', //default input error message container
        errorClass: 'help-block help-block-error', // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        ignore: "", // validate all fields including form hidden input

        rules: {
            "bdc_pollbundle_user[dni]": {
                minlength: 6,
                required: true
            },
            "bdc_pollbundle_user[name]": {
                required: true,
                
            },
            "bdc_pollbundle_user[last_name]": {
                required: true,
            },
            "bdc_pollbundle_user[email]": {
                required: true,
                email: true
            },
            "bdc_pollbundle_user[associate_id]": {
                required: true,
            },
            /*"pass": {
                required: true,
            },
            "pass2": {
                required: true,
            },*/
            
        },
        invalidHandler: function (event, validator) { //display error alert on form submit              
            

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
    $('#bdc_pollbundle_user_role').trigger('change');
    
    
    
});



