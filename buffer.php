<?php
	session_start();
?>
<?php
    if(!strlen($_GET['t_no'])){
        header("location: index.php");
        $_SESSION['err'] = 'Enter the train number';
    }
    elseif(strlen($_GET['t_no'])!=5){
    	header("location: index.php");
    	$_SESSION['err'] = 'Wrong train number';
    }
    else{
?>
<html>
	<head>
	    
		<title> Buffer </title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	</head>
	<body>
		<header class="w3-container w3-teal w3-center">
          <h1>Xsonic Rail Route</h1>
        </header>
        <div class="w3-row">
        	<div class="w3-col l3 s12">
        		&nbsp;
        	</div>
        	<div class="w3-col l8 s12">
		<?php 
			$t_no= $_GET['t_no'];
			$url="https://api.railwayapi.com/v2/route/train/".$t_no."/apikey/zpa7xe2y3v/";
		    $data=file_get_contents($url);
		    $jssson = json_decode($data, true);
		    $response = array(200=>"Success", 210=>"Train doesn’t run on the date queried", 211=>"Train doesn’t have journey class queried", 220=>"Flushed PNR", 221=>"Invalid PNR", 230=> "Date chosen for the query is not valid for the chosen parameters", 404=>"Data couldn’t be loaded on our servers. No data available.", 405=> "Data couldn't be loaded on our servers. Request couldn't go through.", 500=> "Unauthorised API key", 501 =>"Contact mayankgbrc@gmail.com", 502=> "Invalid arguments passed, may be you entered in back date");
		    $res = $jssson["response_code"];
		    $link = mysqli_connect("server", "xsonicin", "Br02q6769Br02q6769", "xsonicin_rail");
		    date_default_timezone_set('Asia/Kolkata');
		    $d=strtotime("now");
            $time= date("Y-m-d h:i:sa", $d);
		    $sql="INSERT INTO train_stats(t_no,time_of_occurence,response_err) VALUES('$t_no','$time','$res')";
		    $query = mysqli_query($link, $sql);
		    $sql="SELECT * FROM trains WHERE train_number='$t_no'";
		    $result = mysqli_query($link, $sql);
		    if(mysqli_num_rows($result)){
		        while($row = mysqli_fetch_assoc($result)) {
                    $prior = $row["priority"];
                    $prior = $prior + 1;
                    $sql = "UPDATE trains SET priority='$prior' WHERE train_number='$t_no'";
                    $result2 = mysqli_query($link, $sql);
                }
		    }
		    if ($res==200){
		        
			    $i=0;
			    while(isset($jssson["route"][$i]["station"]["name"])){
			      $i++;
			    }
			    $max=$i-1;
			    echo "<h5 class=w3-container>You are searching for Train: ".$jssson["train"]["number"]." - ".$jssson["train"]["name"].".</h5>" ;
			    ?>
			    <div class="w3-row-padding">
			    	<div class="w3-col l6 s12">
			    	<table class="w3-table-all w3-hoverable w3-centered">
			    		<tr class="w3-blue"><td colspan="7"> Runs On </td></tr>
			    <?php
			    
			    for($i=0;$i<7;$i++){
			    	echo "<td class=w3-blue>".$jssson["train"]["days"][$i]["code"]."</td>";
			    } 
			    echo "</tr><tr>";
			    for($i=0;$i<7;$i++){
			    	echo "<td>".$jssson["train"]["days"][$i]["runs"]."</td>";
			    }
			    echo "</tr></table><br><br>";
			    ?>
			    	</div>
			    	<div class="w3-col l6 s12">
			    	<table class="w3-table-all w3-hoverable w3-centered">
			    		<tr class="w3-blue"><td colspan="8"> Classes </td></tr>
			    <?php
			    
			    for($i=0;$i<8;$i++){
			    	echo "<td class=w3-blue>".$jssson["train"]["classes"][$i]["code"]."</td>";
			    } 
			    echo "</tr><tr>";
			    for($i=0;$i<8;$i++){
			    	echo "<td>".$jssson["train"]["classes"][$i]["available"]."</td>";
			    }
			    echo "</tr></table><br><br>";

			    $i=0;
		?>
					</div>
				</div>
			<table class="w3-table-all w3-hoverable w3-hide-small">
				<tr class="w3-green w3-large">
					<td>S. No.</td>
					<td>Station-Name</td>
					<td>Scheduled Arrival</td>
					<td>Scheduled Departure</td>
					<td>Distance</td>
					<td> Halt </td>
					<td>Day</td>
				</tr>

		<?php
			    while(isset($jssson["route"][$i]["station"]["name"])){
			    	echo "<tr><td>";
			    	echo $i+1;
			    	echo "</td><td>";
			      	echo ($jssson["route"][$i]["station"]["name"]);
			      	echo "</td><td>";
			      	echo ($jssson["route"][$i]["scharr"]);
			      	echo "</td><td>";
					echo ($jssson["route"][$i]["schdep"]);
			      	echo "</td><td>";
			      	echo ($jssson["route"][$i]["distance"]." km");
			      	echo "</td><td>";
			      	if($jssson["route"][$i]["halt"]==-1){
			      		echo "--";
			      	}
			      	else{
			      		echo ($jssson["route"][$i]["halt"]." min");
			      	}
			      	echo "</td><td>";
			      	echo ($jssson["route"][$i]["day"]);
			      	echo "</td></tr>";
			      	if ($jssson["route"][$i]["scharr"]=='SOURCE'){
			      		$t_source=$jssson["route"][$i]["station"]["name"];
			      		$t_source_time=$jssson["route"][$i]["schdep"];
			      	}
			      	if($jssson["route"][$i]["schdep"]=="DEST"){
			      		$t_dest=$jssson["route"][$i]["station"]["name"];
			      		$t_dest_time=$jssson["route"][$i]["scharr"];
			      	}
			      	$i++;
			    }
			    echo "</table></div>";
			    $i =0;
			    ?>
			<table class="w3-table-all w3-hoverable w3-hide-large">
				<tr class="w3-green w3-large">
					<b><td>S.No.<br>Dist.</td>
					<td>Station-Name<br>Halt</td>
					<td>Scheduled Arrival / Depart</td>
					<td>Day</td></b>
				</tr>

		<?php
			    while(isset($jssson["route"][$i]["station"]["name"])){
			    	echo "<tr><td>";
			    	echo $i+1;
			    	echo (".<br>".$jssson["route"][$i]["distance"]." km");
			    	echo "</td><td>";
			      	echo ($jssson["route"][$i]["station"]["name"]."<br>");
			      	if($jssson["route"][$i]["halt"]==-1){
			      		echo "--";
			      	}
			      	else{
			      		echo ($jssson["route"][$i]["halt"]." min");
			      	}
			      	echo "</td><td>";
			      	echo ($jssson["route"][$i]["scharr"]." / ".$jssson["route"][$i]["schdep"]);
					echo "</td><td>";
					echo ($jssson["route"][$i]["day"]);
			      	echo "</td></tr>";
			      	if ($jssson["route"][$i]["scharr"]=='SOURCE'){
			      		$t_source=$jssson["route"][$i]["station"]["name"];
			      		$t_source_time=$jssson["route"][$i]["schdep"];
			      	}
			      	if($jssson["route"][$i]["schdep"]=="DEST"){
			      		$t_dest=$jssson["route"][$i]["station"]["name"];
			      		$t_dest_time=$jssson["route"][$i]["scharr"];
			      	}
			      	$i++;
			    }
			    $sql="SELECT * FROM trains WHERE train_number='$t_no'";
		        $t_name=$jssson["train"]["name"];
    		    $result = mysqli_query($link, $sql);
    		    if(!mysqli_num_rows($result)){
    		        $t_name = $jssson["train"]["name"];
    		        $t_name = str_replace("-"," ",$t_name);
    		        $sql = "INSERT INTO trains(train_number,train_name,train_source,source_dep_time,train_destination,dest_arr_time,priority) VALUES('$t_no','$t_name','$t_source','$t_source_time','$t_dest','$t_dest_time','1')";
                    $result2 = mysqli_query($link, $sql);
    		    }
			    
			    echo "</table>";
			    ?>
			</div></div>
			    <?php
		    }
		    else{
    			$_SESSION['err'] = $response[$res];
    			echo "<script type='text/javascript'>window.location.href = 'index.php';</script>";
		    }
		?>
		<br><br><br>
		<footer class="w3-container w3-teal w3-center" style="position:fixed;bottom:0;left:0;width:100%;">
          <h4>Developed with <span class="w3-text-red">&hearts;</span> by <a href="https://www.facebook.com/mayankgbrc" target="_blank">Mayank Gupta</a> </h4>
        </footer>
	</body>
	<script type="text/javascript"> var infolinks_pid = 3131024; var infolinks_wsid = 0; </script> <script type="text/javascript" src="//resources.infolinks.com/js/infolinks_main.js"></script>
</html>
<?php
    }
?>