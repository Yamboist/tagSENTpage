
<?php
$general_sent="";
$pos_score=0;
$neg_score=0;
?>
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
			<div id="prediction-forms">
				<h5>Enter the text you wanted to be analyzed: </h5>
				<small>*Just a note: this is a prediction, so there is quite a chance that it is wrong. The percentages means that the analyzer is [x]% sure that the text is positive/ negative</small>
				<br>
				<textarea id="input"></textarea>
				<button id="analyze">Predict sentiment</button>
			</div>
		
			<div id="results">
				<p>Results:</p>
				
				<p><small>General Sentiment: 
					<br><br>
					The text is
					<span id="general"></span></small>
					<br><hr>
				<small>Prediction: <span id="perc_pos">0%</span> positive , <span id="perc_neg">0%</span> negative</small></p>
				<canvas id="myChart" width="150" height="150"></canvas>
				<p><small>Positives: <font color="#009E60" ><span id="positives"></span></font></small></p>
				<p><small>Negatives: <font color="#C41E3A" ><span id="negatives"></span></font></small></p>
			</div>
			<hr>
			
			<div id="process">
				<h2>Process</h2>
				<br>
				<h3>POS Tagging</h3>
				<small>
					<br>
					legend: 
						<ul>
							<li>dt: determiner</li>
							<li>n: noun</li>
							<li>v: verb</li>
							<li>vbl: linking verb</li>
							<li>pr: pronoun</li>
							<li>adj: adjective</li>
							<li>adv: adverb</li>
							<li>conj: conjunction</li>
							<li>stopper: stopper (periods, commas, semicolons, colons, etc ...)</li>

						</ul>
				</small>
				<div id="pos-tagging"></div>

				<h3>Translation</h3>
				<div id="translation"></div>
				<h3>Scoring</h3>
				<div id="sentiment_score"></div>
			</div>
			<hr>

			<div style="margin:20px;">
				<h2>XML Output</h2>
			<textarea id="testresults" style="width:100%;height:200px;">

			</textarea>
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
					$("#testresults").html(e);
					var total = parseFloat(patt_neg.exec(e)[1]) + parseFloat(patt_pos.exec(e)[1]);
					var perc_pos = Math.round((parseFloat(patt_pos.exec(e)[1])/total)*100);
					var org_pos = (parseFloat(patt_pos.exec(e)[1]));
					var org_neg = (parseFloat(patt_neg.exec(e)[1]));
					var mat = e.match(patt_item);
					var perc_neg =Math.round((parseFloat(patt_neg.exec(e)[1])/total)*100);
					var neutral = false;
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

					;

					

					if(perc_neg > perc_pos && Math.abs(org_pos-org_neg)>0.03){
						$("#general").html("<font color='#F7464A'>negative</font>");
					}
					else if(perc_pos > perc_neg && Math.abs(org_pos-org_neg)>0.03){
						$("#general").html("<font color='#46BFBD'>positive</font>");
					}
					else{
						$("#general").html("neutral");
						neutral = true;
					}

					if(!neutral){
						var ctx = document.getElementById("myChart").getContext("2d");
						window.myPie = new Chart(ctx).Pie(data);
						$("#perc_pos").html(perc_pos+"%");
					$("#perc_neg").html(perc_neg+"%");
					}
					

					var pos_builder = "";
					var neg_builder = "";
					var word_builder = "";
					var pos_tags_result= "";
					var trans_result = "";
					var scoring = "";

					for(var i=0;i<mat.length;i++){
						
						var word_patt = new RegExp("<word>(.+)</word>");
						var tag_patt = new RegExp("<tag>(.+)</tag>");
						var pos_score = new RegExp("<positive_score>(.+)</positive_score>");
						var neg_score = new RegExp("<negative_score>(.+)</negative_score>");
						var trans_patt = new RegExp("<translations>(.+)</translations>");


						var tag_word = tag_patt.exec(mat[i])[1];
						var word = word_patt.exec(mat[i])[1];
						var pos_s = parseFloat(pos_score.exec(mat[i])[1]);
						var neg_s = parseFloat(neg_score.exec(mat[i])[1]);
						var trans = trans_patt.exec(mat[i]);

						var tag_color= "";
						if(tag_word=="n"){
							tag_color="#33C6CE";
						}
						else if(tag_word=="adj"){
							tag_color="#FFC600";
						}
						else if(tag_word=="adv"){
							tag_color="#FF9922";
						}
						else if(tag_word=="dt"){
							tag_color="#C09336";
						}
						else if(tag_word=="conj"){
							tag_color="#F57B8A";
						}
						else if(tag_word=="stopper"){
							tag_color="#DCDCDC";
						}
						else if(tag_word=="v"){
							tag_color ="#D60000";
						}
						else if(tag_word=="vbl"){
							tag_color="#E02806";
						}
						else if(tag_word=="pr"){
							tag_color="#EEFFD2";
						}
						else if(tag_word=="AMB"){
							tag_color="#111;color:#F0BD12'";
						}
						else if(tag_word=="UNK"){
							tag_color="#111;color:#FFF'";
						}
						pos_tags_result+= "<div class='pos_entry'><div class='wordx'>"+word+"</div><div class='tagx' style='background-color:"+tag_color+"'>"+tag_word+"</div></div>"
						
						/*if(tag_word!= "pr" && tag_word!= "conj" && tag_word!="stopper" && tag_word!="dt" && tag_word!="prep" && tag_word!="vbl"){
							word_builder += " " + word;
						}*/
						try{
							trans_result+= "<div class='trans_entry'>"+word+" : "+trans[1]+"</div>";

						}
						catch(err){

						}
						
						if(pos_s != 0 || neg_s != 0){
							word_builder +=" " +word;
							scoring += "<div class='score_entry'>"+word_builder+" : (<span class='green'>"+pos_s+"</span> , <span class='red'>"+neg_s+"</span>)</div>"
							if(pos_s > neg_s && (Math.abs(pos_s - neg_s)>0.03)){
								pos_builder+="<br>"+word_builder;

							}
							else if(pos_s < neg_s && (Math.abs(pos_s - neg_s)>0.03) ){
								neg_builder+="<br>"+word_builder;
							}
							else{}
							word_builder = "";

						}		
						else{
							word_builder += " "+word;
						}
						
					}
					i
					$("#sentiment_score").html(scoring+ "<br><div class='score_entry'>Total : (<span class='green'>"+org_pos+"</span> , <span class='red'>"+org_neg+"</span>)</div>");
					$("#translation").html(trans_result);
					$("#pos-tagging").html(pos_tags_result);

					if(!neutral){
						$("#positives").html(pos_builder);
						$("#negatives").html(neg_builder);
					}
					else{
						$("#positives").html("");
						$("#negatives").html("");
						$("#mychart").html("");
						$("#perc_pos").html("");
						$("#perc_neg").html("");
					}
					

				}
			});
		});
	});
</script>