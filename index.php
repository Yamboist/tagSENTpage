
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
				<p><small>Positives: <font color="#009E60" ><span id="positives"></span></font></small></p>
				<p><small>Negatives: <font color="#C41E3A" ><span id="negatives"></span></font></small></p>
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
			var patt_item = /<item>([\s\S]+?)<\/item>/gm;
			$.ajax({
				url:"http://localhost:8080/api",
				data:"q="+input,
				method:"POST",
				success:function(e){
					var total = parseFloat(patt_neg.exec(e)[1]) + parseFloat(patt_pos.exec(e)[1]);
					var perc_pos = Math.round((parseFloat(patt_pos.exec(e)[1])/total)*100);
					
					var mat = e.match(patt_item);
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
					
					var pos_builder = "";
					var neg_builder = "";
					var word_builder = "";
					for(var i=0;i<mat.length;i++){
						
						var word_patt = new RegExp("<word>(.+)</word>");
						var tag_patt = new RegExp("<tag>(.+)</tag>");
						var pos_score = new RegExp("<positive_score>(.+)</positive_score>");
						var neg_score = new RegExp("<negative_score>(.+)</negative_score>");
						
						var tag_word = tag_patt.exec(mat[i])[1];
						var word = word_patt.exec(mat[i])[1];
						var pos_s = parseFloat(pos_score.exec(mat[i])[1]);
						var neg_s = parseFloat(neg_score.exec(mat[i])[1]);
						
						if(tag_word!= "pr" || tag_word!= "conj" || tag_word!="stopper" || tag_word!="dt"){
							word_builder += " " + word;
						}
						if(pos_s != 0 || neg_s != 0){
							
							if(pos_s > neg_s){
								pos_builder+="<br>"+word_builder;
							}
							else if(pos_s < neg_s){
								neg_builder+="<br>"+word_builder;
							}
							word_builder = "";
						}		
						
					}
					$("#positives").html(pos_builder);
					$("#negatives").html(neg_builder);

				}
			});
		});
	});
</script>