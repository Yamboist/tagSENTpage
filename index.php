
<?php
$general_sent="";
$pos_score=0;
$neg_score=0;
?>
<html>

	<head>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link href="css/style.css" rel="stylesheet" type="text/css" />
		<script src="Chart.min.js"></script>
		<script src="jquery.js"></script>
	</head>

	<body>
		<div class="center">
			<div id="header">
				<h1>tagSENT &middot; tagalog sentiment analyzer</h1>
			</div>
			<div id="prediction-forms">
				<h5>Enter the text you wanted to be analyzed: </h5>
				<small>*Just a note: please add spaces besides every punctuations ...</small>
				<br>
				<textarea id="input"></textarea>
				<button id="analyze">Analyze</button>
			</div>
		
			<div id="results">
				<p>Results:</p>
				
				<p><small>General Sentiment: <p>POSITIVE</p> </small>
				<small>Total: <span id="perc_pos">0%</span> positive , <span id="perc_neg">0%</span> negative</small></p>
				<canvas id="myChart" width="150" height="150"></canvas>
				<p><small>Positives: </small></p>
				<p><small>Negatives: </small></p>
			</div>
		</div>
	</body>

<textarea style="display:none" id="resultdata"></textarea>
</html>


<script>
	/*
	var data = [
	    {
	        value: 0.55,
	        color:"#F7464A",
	        highlight: "#FF5A5E",
	        label: "Red"
	    },
	    {
	        value: 0,
	        color: "#46BFBD",
	        highlight: "#5AD3D1",
	        label: "Green"
	    },
	  
	]
	var ctx = document.getElementById("myChart").getContext("2d");
	window.myPie = new Chart(ctx).Pie(data);*/

	var result = ""
	$(document).ready(function(){
		$("#analyze").click(function(){
			var input = $("#input").val();
			var patt_pos = new RegExp("<positive>(.+)</positive>");
			var patt_neg = new RegExp("<negative>(.+)</negative>");
			$.ajax({
				url:"http://localhost:8080/api",
				data:"q="+input,
				method:"POST",
				success:function(e){
					var total = parseFloat(patt_neg.exec(e)[1]) + parseFloat(patt_pos.exec(e)[1]);
					var perc_pos = Math.round((parseFloat(patt_pos.exec(e)[1])/total)*100);
					

					var perc_neg =Math.round((parseFloat(patt_neg.exec(e)[1])/total)*100);
					
					var data = [
				    {
				        value: perc_neg,
				        color:"#F7464A",
				        highlight: "#FF5A5E",
				        label: "NEGATIVE"
				    },
				    {
				        value: perc_pos,
				        color: "#46BFBD",
				        highlight: "#5AD3D1",
				        label: "POSITIVE"
				    },
				  
				]

				var ctx = document.getElementById("myChart").getContext("2d");
				window.myPie = new Chart(ctx).Pie(data);

				$("#perc_pos").html(perc_pos+"%");
				$("#perc_neg").html(perc_neg+"%");



				}
			});
		});
	});
</script>