<html>

	<head>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
		<link href="css/style.css" rel="stylesheet" type="text/css" />
		<script src="Chart.min.js"></script>
		<script src="jquery.js"></script>
	</head>

	<body>
		<div class="center">
			<div id="header">
				<h1>tagSENT &middot; tagalog sentiment analyzer</h1>
			</div>
			<div id="upload-div">
				
				<div id="message-upload"> 
					<h4>Upload the file containing the data to be predicted</h4>
					<input type="file" id="file-uploader" name="file"/>
				</div>
				<div id="upload-button-div">
					<button id="up">Upload & Predict</button>
				</div>
			</div>

			<div id="contents">

			</div>
		</div>
	</body>
</html>

<script>
 	$('#up').on('click', function() {
	    var file_data = $('#file-uploader').prop('files')[0];   
	    var form_data = new FormData();                  
	    form_data.append('file', file_data)
	                          
	    $.ajax({
	                url: 'uploader.php',
	                dataType: 'text',
	                cache: false,
	                contentType: false,
	                processData: false,
	                data: form_data,                         
	                type: 'post',
	                success: function(data){
	                     setData(data);
	                }

     	});
	 });
	 function setData(data){
	 	/*var find = '\n';
		var re = new RegExp(find, 'g');

		data = data.replace(re, '<br>');*/
		var data_arr = data.split('\n')	;
		for(var i=0;i<data_arr.length;i++){
			var builder = "";
			builder += "<div class='entry'>";
			builder += "<p class='sentence'>"+data_arr[i]+"</p>";
			
			var input = data_arr[i];
			var sent = "";
			$.ajax({
				url:"http://localhost:8080/api",
				data:"q="+input,
				method:"POST",
				async:false,
				success:function(e){
					var patt = new RegExp("<generalsentiment>(.+)</generalsentiment>");
					sent += patt.exec(e)[1];
				}
			});
			if(sent == "POSITIVE"){
				sent ="<font color='lime'>"+sent+"</font>";
			}
			else if(sent == "NEGATIVE"){
				sent ="<font color='red'>"+sent+"</font>";
			}
			builder += "<p class='polarity-text'>"+sent+"</p>";
			builder += "<p class='gotoMain'><a href='index.php?sentence="+data_arr[i]+"'><img src='goto.png' width='30'/></a></p>"
			builder += "</div>";
			$("#contents").append(builder);
		} 		
	 }   
</script>