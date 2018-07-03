/**
 * Created by son_jp on 8/21/2017.
 * Untility class for javascript
 */

$(document).ready(function(){
    hideMessage('.alert-message');
});

//slide hide message
function hideMessage(selector){
    $(selector).delay(3000).slideUp(300);
}

//validate regular exception
function checkMail(email){
    var regex = /^(([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+([;.](([a-zA-Z0-9_\-\.]+)@{[a-zA-Z0-9_\-\.]+0\.([a-zA-Z]{2,5}){1,25})+)*$/;
    if(regex.test(email)){
        return true;
    }
    else{
        return false;
    }
    return false;
}

function checkBetween(value, from, to){
    return value.length < from || value.length > to;
}

function checkMatch(one, two){
    return parseInt(one) !== parseInt(two);
}

//remove add class
function reActiveClass(ele,className){
    $(ele).siblings().removeClass(className);
    return $(ele).addClass(className);
}

