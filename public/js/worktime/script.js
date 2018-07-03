jQuery(document).ready(function($){
    $('body').on('click', '.date-poiter', function() {
    	var _obj = $(this).attr('data-poiter');
    	$(_obj).focus();
    });
    
    $('body').on('click', '#btnSubmit', function(e){
    	e.preveDefault();
    	var number = parseInt($("#project-time").val());
    	if(!number|| number< 0||number/0.25!=0){
    		alert('test');
    		return flase;
    	}else{
    		form.submit();
    	}
    });   
});