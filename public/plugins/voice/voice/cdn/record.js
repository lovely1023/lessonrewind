function restore(){$("#record,#live").removeClass("disabled");$(".one").addClass("disabled");$.voice.stop();}
$(document).ready(function(){
	$(document).on("click", "#record:not(.disabled)", function(){
		elem = $(this);
		$.voice.record($("#live").is(":checked"), function(){
			elem.addClass("disabled");
			$("#live").addClass("disabled");
			$("#stop,#play,#download").removeClass("disabled");
		});
	});
	$(document).on("click", "#stop:not(.disabled)", function(){
		restore();
	});
	$(document).on("click", "#play:not(.disabled)", function(){
		$.voice.export(function(url){
			$("#audio").attr("src", url);
			$("#audio")[0].play();
		}, "URL");
		restore();
	});
	$(document).on("click", "#download:not(.disabled)", function(){
		$.voice.export(function(url){
			
			$('#voice_input').val(url);
			//console.log(url);
	/*	var data = new FormData();
      data.append('file', url);
	
			 $.ajax({
        url :  BLOB_URL,
        type: 'POST',
        data:data,
        contentType: false,
        processData: false,
        success: function(data) {
         $('#dataval').html(data);
        },    
        error: function() {
          alert("not so boa!");
        }
      });*/
		}, "URL");
		restore();
	});
});