/**
 * Created by son_jp on 9/1/2017.
 */
$(document).ready(function(){
    var password = $('#password'),
        confirm_pass = $('#password-confirm'),
        form = $('#frmResetPass'),
        flag = true;

    form.find('.help-block').hide();

    $('#btnSubmit').on('click', function(e){
        e.preventDefault();

        if(checkBetween(password.val(),8,16)){
            password.parents('.form-group').addClass('has-error').find('.help-block').show();
            return false;
        }else{
            password.parents('.form-group').removeClass('has-error').find('.help-block').hide();
        }

        if(checkMatch(password.val(), confirm_pass.val())){
            confirm_pass.parents('.form-group').addClass('has-error').find('.help-block').show();
            return false;
        }else{
            password.parents('.form-group').removeClass('has-error').find('.help-block').hide();
        }

        form.submit();
    });
});