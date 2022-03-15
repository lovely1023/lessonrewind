/* All Aditional Functions  */



$('.block_ui').click(function(e){$.blockUI();});

function showFlashMessage(msg,err){
	
	$('body').animate({ scrollTop: 0 }, 500);
	
 	$("#flash-message").show('slow');
	
 	if(err){
		$("#flash-message").addClass("alert-danger");
	}else{
		$("#flash-message").removeClass("alert-danger");
		$("#flash-message").addClass("alert-info");
	}
	
  	$("#flash-message").html(msg);
 	
	
	$("#flash-message").slideDown(function(){
		setTimeout(	'$("#flash-message").hide("slow")' ,3000);
	});
}

 

function formatPhone(obj) {
	
            var numbers = obj.value.replace(/\D/g, ''),
        char = { 0: '(', 3: ') ', 6: ' - ' };
            obj.value = '';
            for (var i = 0; i < numbers.length; i++) {
                obj.value += (char[i] || '') + numbers[i];
            }
        }

// override jquery validate plugin defaults
$.validator.setDefaults({
    highlight: function(element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
	
	submitHandler: function(form) {
     	 $.blockUI();
		 form.submit();
    },
	
    errorElement: 'span',
    errorClass: 'help-block',
    errorPlacement: function(error, element) {
        if(element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});
if(typeof(user_id)!='undefined' && $('.checkemail').length>0)
{
	jQuery.validator.addClassRules("checkemail",{remote: baseUrl+"/admin/user/checkemail/user_id/"+user_id});
}
else
{
	if($('.checkemail').length>0)
	{
			jQuery.validator.addClassRules("checkemail",{remote: baseUrl+"/admin/user/checkemail"});
	}
		
}

var handleChoosenSelect = function () {
	
        if(!jQuery().chosen)
		{
            return;
        }
        $(".chosen").chosen();

        $(".chosen-with-diselect").chosen({
            allow_single_deselect: true
        })
    }



<!-- For Datatables  -->
function re_init(){
		
		$('.checkboxes').uniform();
		
		$('.group-checkable').removeAttr('checked');
		
		$('.group-checkable').parent().removeClass('checked');
			
		$('.danger-toggle-button').toggleButtons({
			style: {
				// Accepted values ["primary", "danger", "info", "success", "warning"] or nothing
				enabled: "danger",
				disabled: "info"
				
			},
			width:126,
			height:28,
			label: {
				enabled: "Blocked",
				disabled: "Active"
			}
		});
		
		
		$('.danger-toggle-button-1').toggleButtons({
			style: {
				// Accepted values ["primary", "danger", "info", "success", "warning"] or nothing
				enabled: "danger",
				disabled: "info"
				
			},
			label: {
				enabled: "OFF",
				disabled: "ON"
			}
		});
	
		
	 $("img.lazy").lazyload({
         effect : "fadeIn"
     });
	 
	
	
}

$('.validate').validate();

$('.profile_form').validate({
	rules:{
		user_old_password:{minlength:5,maxlength:16,remote: baseUrl+"/admin/index/checkpassword"},
 		user_password:{minlength: 5, maxlength:16},
		user_rpassword:{equalTo:'#user_password' , minlength:5, maxlength:16},
 		user_email:{required: true,email: true,/*remote: baseUrl+"/user/checkemail"*/},
 	},
 });
	jQuery.validator.addClassRules("instrumentname",{remote: baseUrl+"/admin/instrument/instrumentname/instrument_id/"+$('#Instrument_id').val()});
$('.mix-link-delete').click(function(e) {
	
	var $confirm = confirm("Please Confim Your Delete Request !");
	if($confirm){
		return true; 
	}
	return false; 
});



function checkSelects(){
 
 	var checkedRecords=false;	
	
	$(".elem_ids").each(function(index, element) {
        if(this.checked==true){
			checkedRecords=true;
		}
    });
 	
	
	if(!checkedRecords){
		showFlashMessage("No Records Selected for Delete , Please Select Records to delete ",1);
		return false;	
	}else{
		if(!confirm("Are you sure you want to delete")){
			return false;
		}
		$.blockUI();
	}
 	 
}
	
$('#deletebcchk').click(function(e) {
	var current_checked_status = $.trim($('.group-checkable').attr('checked'));
	if(current_checked_status!='checked'){
		 $('.checkboxes').removeAttr('checked');
		 $('.checkboxes').parent().removeClass('checked');;
	}
	else{
		
		$('.checkboxes').attr('checked','checked');
		$('.checkboxes').parent().addClass('checked');;
	}
	
});



function globalStatus(chkAll){
	
 	$.blockUI();
	 
 	
	
	vars=	$(chkAll).attr("id").split("-");
	title=	$(chkAll).attr("title") ;
	
	if($(chkAll).hasClass("status-1")){
			var status=0;
	}else{
			var status=1;
	}
	
scriptUrl=baseUrl+"/admin/ajax/setstatus/type/"+vars[0]+"/id/"+vars[1]+"/status/"+status;
 
	  if(title!==undefined && title!=""){usermessage=title+" has been changed";}
	  else{
		  field_title = vars[0].replace(/_/g," ");
		  field_title = ucwords(field_title);
		  
		 }
		
		$.ajax({
			url: scriptUrl,
			dataType:"json",
			success: function(data) {
				
				if(data.success){
					
					if($(chkAll).hasClass("status-1")){
						$(chkAll).closest('div').css('left','0%');
							$(chkAll).addClass("status-0");
							$(chkAll).removeClass("status-1");
					}else{
						$(chkAll).closest('div').css('left','-50%');
							$(chkAll).addClass("status-1");
							$(chkAll).removeClass("status-0");
					}
					usermessage='<b>'+field_title +'</b>  status has been changed';
					$("#flash-message").slideUp();
		
					showFlashMessage(data.message);	
				}
				else{
					showFlashMessage(data.message,1);
				}
			$.unblockUI();
		},
		error : function(data) {
			
			showFlashMessage(" <b>Internal Server Error</b> " ,1);
			$.unblockUI();
			
			 
		}
	  
	});
		
}

/*function globalStatus(chkAll){
	
 	$.blockUI();
	 
 	if($(chkAll).hasClass("status-1")){
			var status=0
	}else{
		var status=1
	}
	
	vars=	$(chkAll).attr("id").split("-");
	title=	$(chkAll).attr("title") ;
	
	
	
	 
	scriptUrl=baseUrl+"/admin/ajax/setstatus/type/"+vars[0]+"/id/"+vars[1]+"/status/"+status;
 
	  if(title!==undefined && title!=""){usermessage=title+" has been changed";}
	  else{
		  field_title = vars[0].replace(/_/g," ");
		  field_title = ucwords(field_title);
		  
	}
	
		$.ajax({
			url: scriptUrl,
			dataType:"json",
			success: function(data) {
				if(data.success){
							if($(chkAll).hasClass("status-1"))
							{
									$(chkAll).addClass("status-0");
									$(chkAll).removeClass("status-1");
							}
							else
							{
									$(chkAll).addClass("status-1");
									$(chkAll).removeClass("status-0");
							}
								 usermessage='<b>'+field_title +'</b>  status has been changed';
								 	$("#flash-message").slideUp();
					showFlashMessage(data.message);	
				}else{
					showFlashMessage(data.message,1);
				}
			$.unblockUI();
		},
		error : function(data) {
			
			showFlashMessage(" <b>Internal Server Error</b> " ,1);
			$.unblockUI();
			
			 
		}
	  
	});
		
}


*/


function ucwords(str){
	
	return  str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
    	return letter.toUpperCase();
	});

	
}



function getSubCategories($obj){
	$.blockUI();
	$.ajax({
		url: baseUrl+"/admin/ajax/getsubcategories",
		dataType:'json',
		data : {id:$obj.value},
		success: function(data) {
			html = "<option value=''>-- Select SubCategory --</option>"
			for(var key in data) {
 				html += "<option value=" + data[key]['category_id']  + ">" +data[key]['category_name'] + "</option>"
			}
			$('#product_subcategory_id').html(html);
			$.unblockUI();
		},
		error : function(data) {
			showFlashMessage(" <b>Internal Server Error</b> " ,1);
			$.unblockUI();
		}

	});
	 
	
}




$(function() {
     $("img.lazy").lazyload({
         effect : "fadeIn"
     });

  });
  





$(document).ready(function(e) {
	handleChoosenSelect();
});










//group-checkable