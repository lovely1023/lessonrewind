// JavaScript Document


// override jquery validate plugin defaults
$.validator.setDefaults({
	
    highlight: function(element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
	
	/*submitHandler: function(form) {
     	 //$.blockUI();
		 form.submit();
    },*/
	
    errorElement: 'span',
    errorClass: 'help-block',
    errorPlacement: function(error, element) {
		console.log(error);
        if(element.parent('.form-group').length) {
			
            error.insertAfter(element.parent());
        } else {
			
			if($(element).attr('name')=='announcement_type[]')
			{
				error.insertAfter(element.parent().parent().parent().parent());	
			}
			else if($(element).attr('name')=='student_class[]')
			{
				error.insertAfter(element.parent().parent());	
			}
			else if($(element).attr('name')=='stu_roster')
			{
				error.insertAfter(element.parent());	
			}
			else
			{
            	error.insertAfter(element.parent().parent());
			}
        }
    }
});


function formatPhone(obj) {
	
        var numbers = obj.value.replace(/\D/g, ''),
        char = { 0: '(', 3: ') ', 6: ' - ' };
            obj.value = '';
            for (var i = 0; i < numbers.length; i++) {
                obj.value += (char[i] || '') + numbers[i];
            }
        }
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

function ucwords(str){
	
	return  str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
    	return letter.toUpperCase();
	});

	
}


$('.profile_form').validate({
	rules:{
		user_old_password:{minlength:5,maxlength:16,remote: baseUrl+"/user/checkpassword"},
 		user_password:{minlength:5 , maxlength:16},
		user_cvv:{maxlength:4},
		user_rpassword:{equalTo:'#user_password' , minlength:5, maxlength:16},
 		/*user_email:{email: true,},*/
		bulkimport_file:{extension:"xls|xlsx|csv|XLS|XLSX|CSV"},
		
 	},
	messages:{
		user_rpassword:{equalTo:"Password Mismatch ,please enter correct password"}
	}
 });
 
 
 
  $.validator.addClassRules("mygroup", {
        require_from_group: [1, ".mygroup"]
    });
 $('#familyform').validate({
	rules:{
		
		
 	},
	messages:{
		user_rpassword:{equalTo:"Password Mismatch ,please enter correct password"}
	}
 });
 
 

 
 $('.studentForm').validate({
	rules:{
		
 	},
	messages:{
		
	}
 });

jQuery.validator.addClassRules("checkemail",{remote: baseUrl+"/user/checkemail"});
//jQuery.validator.addClassRules("checkemail_student",{remote: baseUrl+"/user/checkemailstudent"});

jQuery.validator.addClassRules("instrumentname",{remote: baseUrl+"/class/instrumentname/instrument_id/"+$('#Instrument_id').val()});
jQuery.validator.addClassRules("emailexists",{remote: baseUrl+"/user/checkemail/rev/1"});
jQuery.validator.addClassRules("emailexists",{remote: baseUrl+"/user/checkemail/rev/1"});
jQuery.validator.addClassRules("checkemail_newfamily",{remote: baseUrl+"/user/checkemailfamily"});
jQuery.validator.addClassRules("checkusername",{remote: baseUrl+"/user/checkusername"});
jQuery.validator.addClassRules("checkuserandmail",{remote: baseUrl+"/user/checkuserandmail"});


if($('.checkemail_exclude_mail').length>0)
{
	
	jQuery.validator.addClassRules("checkemail_exclude_mail",{remote: baseUrl+"/user/checkemailexclude/user_id/"+user_id});
	
}
if($('.checkemail_exclude_username').length>0)
{
	jQuery.validator.addClassRules("checkemail_exclude_username",{remote: baseUrl+"/user/checkemailexcludeusername/user_id/"+user_id});
	
}

jQuery.validator.addMethod('checkexcludeusername', function(user_username, element) {
	var response;
					$.ajax({
					url: baseUrl+"/user/checkemailexcludeusername/user_id/"+user_id ,
					data:{user_username:user_username},
					async:false, 
					success: function(data){
					response= data;
					//$("#results").append(html);
					}
					});					
					if(response==0)
					{
						return false;	
					}
					else
					{
						return true;		
					}
	
	}, 'Please enter another user name, this  user name is already exists.');


jQuery.validator.addMethod('checkemail_student', function(user_email, element) {
	$('#familyinfo').css('display','none');
	var response;
	
		$.ajax({
			
		url: baseUrl+"/user/checkstudentexists/user_email/"+user_email+"/user_id/"+user_id,
		//url: baseUrl+"/user/checkemailstudent/user_email/"+user_email+"/user_id/"+user_id,
		async:false, 
		success: function(html){
			
			var data=JSON.parse(html);
		response= html;
		//alert(html);
		}
		
		
		});
		$('#familycontact').css('display','none');
		$('#').css('display','none');
		
	if(response == 0)
    {
		  return false;
    }
    else if(response == 1)
    {
		   return true;
    }
	else if(response == 2)
	{
		$('#user_email_hidfamilycontactstudentden').val(user_email);
		//$('#familycontact').css('display','block');
		$('input[name="family_contact"]').addClass('required');
		  return true;

	}
	
	else if(response == 3)
	{
		$('#user_email_hidden').val(user_email);
	//	$('#familycontactstudent').css('display','block');
		$('#innerlabel').html('The User Name "'+user_email+'" already exists for Student <student name>.  Do you want to convert this User Name to a Family Contact so it can be used for multiple students?');
		
		var checkval=$('input[name="family_contact_student"]:checked').val();
		if(checkval)
		{
		
		checkradiostudent1(checkval);
		}
		  return true;

	}
	
		
	}, 'Email Addresss already exists');
	
	jQuery.validator.addMethod('checkemail_family', function(user_email, element) {
	
	var response;
	var family_type=$('input[name="family_type"]').val();
	var email_options=$('input[name="family_contact_student"]').val();
		$.ajax({
			
		url: baseUrl+"/user/checkfamilyexists/user_email/"+user_email+"/user_id/"+user_id+"/family_type/"+family_type+"/email_options/"+email_options,
		async:false, 
		success: function(html)
		{
		
		var data=JSON.parse(html);
		response= html;
		
		}
		
		
		});
		$('#familycontact').css('display','none');
		$('#').css('display','none');
		
	if(response == 0)
    {
		  return false;
    }
    else if(response == 1)
    {
		   return true;
    }
	else if(response == 2)
	{
		
		  return true;

	}
	
	else if(response == 3)
	{
	
		  return true;

	}
	
		
	}, 'Email Addresss already exists');
	

jQuery.validator.addMethod('checknameuser', function(user_email, element) {
	
	
	var firstname=$('#user_first_name').val();
	var lastname=$('#user_last_name').val();
	var family_type=$('input[name="family_type"').val();
	var response;
	
	 if($("#family_type-0").is(":checked")) 
	{
			
			var url_val=baseUrl+"/user/checknameuser/firstname/"+firstname+"/lastname/"+lastname+"/user_id/"+user_id+"/family_type/family";
	}
	else
	{
		
			var url_val=baseUrl+"/user/checknameuser/firstname/"+firstname+"/lastname/"+lastname+"/user_id/"+user_id;	
	}
					$.ajax({
					url:url_val ,
					data:"",
					async:false, 
					success: function(data){
					response= data;
					//$("#results").append(html);
					}
					});					
					if(response==0)
					{
						return false;	
					}
					else
					{
						return true;		
					}
	
	}, 'Please enter another first name or last name, this name is already exists.');



$('.mail_verification_pulsate').pulsate({color: "#fcb322"});

$(function() {
     $("img.lazy").lazyload({
         effect : "fadeIn"
     });


	

  });
  
   if($('.lessontemplate').length>0)
  {
		$('.lessontemplate').change(function(e) {
			
			$('#lesson_title').val('');
			var thisval=$(this).val();
			if(thisval==0)
			{
						$('#template').css('display','none');
						//$('#notsentlesson').css('display','none');
						$('#oldlesson').css('display','none');
			}
			else if(thisval==1)
			{
					$('#oldlesson').css('display','none');
					$('#template').css('display','block');	
				//	$('#notsentlesson').css('display','block');
					 $('#lesson_template_name').prop('selectedIndex',0);
					// $('#lesson_notsaved_name').prop('selectedIndex',0);
			}
			else if(thisval==2)
			{
				
					$('#template').css('display','none');
					//$('#notsentlesson').css('display','none');
					$('#oldlesson').css('display','block');
					 $('#lesson_old_name').prop('selectedIndex',0);
							
			}
			CKEDITOR.instances['lesson_desc'].setData('');
        });
  }
  
  if($('.classtype').length>0)
  {
		$('.classtype').change(function(e) {
            
			var thisval=$(this).val();
			if(thisval==0)
			{
						$('#template').css('display','none');
			}
			else
			{
					$('#template').css('display','block');	
			}
        });
  }
  
    $(document).ready(function(e) {
		
		
        	if($('.datetime').length>0)
		{
	 		 $('.datetime').datetimepicker({
				formatTime: 'g:i A',
				formatDate:'Y-m-d',
				format:'Y-m-d h:i A',
				step:5

	});
		}
		if($('.student_class').length>0)
		{
			 $('.student_class').niceScroll({  
					cursorcolor: "#ff8400",
					cursoropacitymin: 0.3,
					background: "#bbb",
					cursorborder: "0",
					autohidemode: false,
					cursorminheight: 30
			  });	
		}
		
		if($('.daterangetime').length>0)
		{
				/*$('.daterangetime').daterangepicker({
				"startDate": "10/01/2017",
				"endDate": "10/07/2017",
				 timePicker: true,
				  format: 'MM/DD/YYYY h:mm A',
				}, function(start, end, label) {
				console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
				});*/
	 		  $('.daterangetime').daterangepicker({
                    timePicker: true,
                    timePickerIncrement: 5,
                    format: 'MM/DD/YYYY h:mm A',
                  });
	}
		if($('.daterangeonly').length>0)
		{
	 		  $('.daterangeonly').daterangepicker({
                    timePicker: false,
                   /*minDate:getFormattedDate(new Date()),*/
                    format: 'MM/DD/YYYY'
                });
             }
    });

 function getFormattedDate(date) {
  var year = date.getFullYear();
  var month = (1 + date.getMonth()).toString();
  month = month.length > 1 ? month : '0' + month;
  var day = date.getDate().toString();
  day = day.length > 1 ? day : '0' + day;
  return month + '/' + day + '/' + year;
}
	
function getsavetemplate(idval,ajaxurl)
{
	var templateid=$(idval).val();
	$.ajax({
            type: "POST", //rest Type
             url: ajaxurl+templateid ,
			 data:"",
            success: function (data) {
				data=JSON.parse(data);
				$('#class_name').val(data['class_name']);
				$('#class_days').val(data['class_days']);
				$('input[type="checkbox"]').removeAttr('checked');
				
				if(data['class_date_type']==0)
				{$('#class_date').val('');
				}
				else if(data['class_date_type']==1)
				{$('#class_date').val(data['class_date']);$('#date_timepicker_start').val(data['date_timepicker_start']);$('#date_timepicker_end').val(data['date_timepicker_end']);}
				else if(data['class_date_type']==2){
				$('#class_date').val(data['class_date']);	$('#class_date_only').val(data['class_date_only']);
				}
				else{
					$('#class_date').val(data['class_date']); 
					 timeexplode1=data['timeexplode1'];
					 timeexplode2=data['timeexplode2'];
					 timeexplode3=data['timeexplode3'];
					 timeexplode4=data['timeexplode4'];
					 timeexplode5=data['timeexplode5'];
					 timeexplode6=data['timeexplode6'];
					  timeexplode7=data['timeexplode7'];
					   timeexplode8=data['timeexplode8'];
						console.log(timeexplode1+'------------'+timeexplode2+'------------'+timeexplode3+'------------'+timeexplode4+'------------'+timeexplode5+'------------'+timeexplode6);
					$('input[name=daterangepicker_start]').val(data['daterangepicker_start']);
          		  $('input[name=daterangepicker_end]').val(data['daterangepicker_end']);	
						$('.second').find('.hourselect option[value='+data['timeexplode1']+']').attr('selected','selected');
						$('.second').find('.minuteselect option[value='+data['timeexplode2']+']').attr('selected','selected');
						$('.second').find('.ampmselect option[value='+data['timeexplode3']+']').attr('selected','selected');
						$('.first').find('.hourselect option[value='+data['timeexplode4']+']').attr('selected','selected');
						$('.first').find('.minuteselect option[value='+data['timeexplode5']+']').attr('selected','selected');
						$('.first').find('.ampmselect option[value='+data['timeexplode6']+']').attr('selected','selected');
						 var startdate=data['daterangepicker_start'].split('/');
						    var endtdate=data['daterangepicker_end'].split('/');
						for (var row = 0; row < 6; row++) {
              				  for (var col = 0; col < 7; col++) {
								  var datevals= $('.second').find('td[data-title="r'+row+'c'+col+'"]').html();
								    var datevale= $('.first').find('td[data-title="r'+row+'c'+col+'"]').html();
								  if(startdate[1]==datevals)
								  {	
								  $('.second').find('td').removeClass('active').removeClass('start-date').removeClass('end-date');
								  	$('.second').find('td[data-title="r'+row+'c'+col+'"]').addClass('active').addClass('start-date').addClass('end-date')
								  }
								  if(endtdate[1]==datevale)
								  {	$('.first').find('td').removeClass('active').removeClass('start-date').removeClass('end-date');
								  	$('.first').find('td[data-title="r'+row+'c'+col+'"]').addClass('active').addClass('start-date').addClass('end-date')
								  }
								  }
              				 
           								 }
						 }
				$('input:radio[name="class_date_type"]').filter('[value="'+data['class_date_type']+'"]').attr('checked', true);
				$('.class_date_type').trigger('change');
				$('#class_date').val(data['class_date']);
					$(data['class_days']).each(function(index, element) {
						$('#class_days-'+element).prop('checked', true);;
				});
				 $('.minuteselect').trigger('change');
            }
 });
	
}
	function deletetemplate(idval)
	{ 
		var test=confirm("Are you sure you want to remove this file");
		if(test)
		{
		
				$('#tempalte_'+idval).remove();
				$('#attach_'+idval).remove();
			
		}
		
    }
function getsavetemplatelesson(idval,ajaxurl)
{
	var templateid=$(idval).val();
	
	// $('#lesson_template_name').prop('selectedIndex',0);
	 $('#lesson_notsaved_name').prop('selectedIndex',0);
	$.ajax({
            type: "POST", //rest Type
             url: ajaxurl+templateid ,
			 data:"",
            success: function (data) {
		
				$('#template_attach').html(data);
				$('#lesson_title').val($('#lesson_name').val());
					CKEDITOR.instances['lesson_desc'].setData($('#lesson_descr').val());
            }
 });
	
}

function getnotsavedlesson(idval,ajaxurl)
{
	 $('#lesson_template_name').prop('selectedIndex',0);
				//	 $('#lesson_notsaved_name').prop('selectedIndex',0);
	var templateid=$(idval).val();
	
	$.ajax({
            type: "POST", //rest Type
             url: ajaxurl+templateid ,
			 data:"",
            success: function (data) {
				
				$('#template_attach').html(data);
				$('#lesson_title').val($('#lesson_name').val());
					CKEDITOR.instances['lesson_desc'].setData($('#lesson_descr').val());
            }
 });
	
}
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
				enabled: "Inactive",
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


function globalStatus(chkAll){
	
 	$.blockUI();
	 
 	if($(chkAll).hasClass("status-1")){
			var status=0
			$(chkAll).addClass("status-0");
			$(chkAll).removeClass("status-1");
	}else{
		var status=1
			$(chkAll).addClass("status-1");
			$(chkAll).removeClass("status-0");
	}
	
	vars=	$(chkAll).attr("id").split("-");
	title=	$(chkAll).attr("title") ;
	
	
		fieldstatus = $(chkAll).attr("name");
	
	 
	scriptUrl=baseUrl+"/ajax/setstatus/type/"+vars[0]+"/id/"+vars[1]+"/status/"+status+"/fieldstatus/"+fieldstatus;
// console.log(scriptUrl);
	  if(title!==undefined && title!=""){usermessage=title+" has been changed";}
	  else{
		  field_title = vars[0].replace(/_/g," ");
		  field_title = ucwords(field_title);
		  
		 usermessage='<b>'+field_title +'</b>  status has been changed';}
		$("#flash-message").slideUp();
		$.ajax({
			url: scriptUrl,
			dataType:"json",
			success: function(data) {
				if(data.success){
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

function searchFamily1(parent_id,child_id)
{
		
	var oemListLength = $("#"+parent_id+" .form-group .checkbox").length;
	
	if($("#"+child_id).val()!=undefined && $("#"+child_id).val()!='Find your brand' && $("#"+child_id).val()!='') {
		var val = $("#"+child_id).val().toLowerCase();
		
		for (var brandCount=1;brandCount<=oemListLength;brandCount++) {
			//var brandName = $("brandName"+brandCount).html(); 
			var student_child='#'+parent_id+'  .checkbox:nth-child('+brandCount+') span';
			var forid=$('#'+parent_id).children('.form-group').children('.checkbox:nth-child('+brandCount+')').children('input').attr('id');
			var brandName = $(student_child).html();
			if(brandName.toLowerCase().indexOf(val)==-1) {
				$("label[for='"+forid+"']").addClass('disnone');
			} else { 
				$("label[for='"+forid+"']").removeClass('disnone');
			}
		}
	} else {
		for (var brandCount=1;brandCount<=oemListLength;brandCount++) {
			
				var student_child='#'+parent_id+' .checkbox span';
			
			var forid=$('#'+parent_id).children('.form-group').children('.checkbox:nth-child('+brandCount+')').children('input').attr('id');
			$("label[for='"+forid+"']").removeClass('disnone');
		}
	}

}


function searchTeacher()
{
		
	var oemListLength = $(".viewall .student_view").length;
	
	if($("#teacher_name").val()!=undefined && $("#teacher_name").val()!='Find your brand' && $("#teacher_name").val()!='') {
		var val = $("#teacher_name").val().toLowerCase();
		//alert(val);
		for (var brandCount=1;brandCount<=oemListLength;brandCount++) {
			//var brandName = $("brandName"+brandCount).html(); 
			var student_child='#teacher_name_'+brandCount+' .student_view';
			var brandName = $(student_child).html();
			
			if(brandName.toLowerCase().indexOf(val)==-1) {
				$("#teacher_name_"+brandCount).addClass('disnone');
			} else { 
				$("#teacher_name_"+brandCount).removeClass('disnone');
			}
		}
	} else {
		for (var brandCount=1;brandCount<=oemListLength;brandCount++) {
			$("#teacher_name_"+brandCount).removeClass('disnone');
		}
	}

}

function searchStudent() 
{
	var oemListLength = $(".viewall .student_view").length;
	if($("#student_name").val()!=undefined && $("#student_name").val()!='Find your brand' && $("#student_name").val()!='') {
		var val = $("#student_name").val().toLowerCase();
		for (var brandCount=1;brandCount<=oemListLength;brandCount++) {
			//var brandName = $("brandName"+brandCount).html(); 
			var student_child='#stu_name_'+brandCount+' .student_view';
			var brandName = $(student_child).html();
			
			if(brandName.toLowerCase().indexOf(val)==-1) {
				$("#stu_name_"+brandCount).addClass('disnone');
			} else { 
				$("#stu_name_"+brandCount).removeClass('disnone');
			}
		}
	} else {
		for (var brandCount=1;brandCount<=oemListLength;brandCount++) {
			$("#stu_name_"+brandCount).removeClass('disnone');
		}
	}

}
if($('.chzn-select').length>0)
{
	$(document).ready(function(e) {
			$(".chzn-select").chosen();        
    });

}
function searchClass() {
	var oemListLength = $(".viewall .student_view").length;
	
	if($("#class_name").val()!=undefined && $("#class_name").val()!='Find your brand' && $("#class_name").val()!='') {
		var val = $("#class_name").val().toLowerCase();
		//alert(val);
		for (var brandCount=1;brandCount<=oemListLength;brandCount++) {
			//var brandName = $("brandName"+brandCount).html(); 
			var student_child='#class_name_'+brandCount+' .student_view';
			var brandName = $(student_child).html();
			
			if(brandName.toLowerCase().indexOf(val)==-1) {
				$("#class_name_"+brandCount).addClass('disnone');
			} else { 
				$("#class_name_"+brandCount).removeClass('disnone');
			}
		}
	} else {
		for (var brandCount=1;brandCount<=oemListLength;brandCount++) {
			$("#class_name_"+brandCount).removeClass('disnone');
		}
	}

}


 $(function(){
	 
	 if($('#date_timepicker_start').length>0)
	 {
		 $('#date_timepicker_start').datetimepicker({
			 
		   	 format:"h:i A",
			 formatTime:  "h:i A",
			 step:05,
		 	 onShow:function( ct ){
		 	 this.setOptions({
				maxTime:$('#date_timepicker_end').val()?Add2Days1():false
		 	  })
			  
		  },
		  timepicker:true,
			datepicker:false,
		 });
	 }
	 if($('#date_timepicker_end').length>0)
	 {
 $('#date_timepicker_end').datetimepicker({
	 
			format:"h:i A",
			step:05,	
			formatTime:  "h:i A",
			onShow:function( ct ){
			this.setOptions({
			minTime:$('#date_timepicker_start').val()?Add2Days():false
			})
			},
			timepicker:true,
			datepicker:false,
			});
	 }
});

function Add2Days()
{
	var test=($('#date_timepicker_start').val()).split(" ");
	var timeElements = test[0].split(":");    
    var theHour = parseInt(timeElements[0]);
	
    var theMintute = parseInt(timeElements[1])+5;
    var newHour = theHour;
    var newval=newHour + ":" + theMintute+" "+test[1];
	console.log(newval);
	return newval;
//return todaydate;	
}// 
function Add2Days1()
{
	var test=($('#date_timepicker_end').val()).split(" ");
	var timeElements = test[0].split(":");    
    var theHour = parseInt(timeElements[0]);
    var theMintute = parseInt(timeElements[1]);
    var newHour = theHour;
    var newval=newHour + ":" + theMintute+" "+test[1];
	
	return newval;
//return todaydate;	
}//


function changelogintype(idval)
{
		
		if($(idval).is(":checked"))
		{
			$('#user_email').removeClass('required');
			$('#user_email_parent').hide();
		}
		else
		{
			$('#user_email').addClass('required');
			$('#user_email_parent').show('slow');
		}
}

