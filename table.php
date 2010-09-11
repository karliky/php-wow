<style type="text/css">
body {
	background-color: #000;
}
body,td,th {
	color: #FFF;
}
</style>
<table width="50%" height="100%" border="1" cellpadding="1" id="water"> <?


				echo ' <tr>';
				$i = 1;
				for($x = 1;$x<257;$x++){

				echo '<td><div align="center">'.$x.'</div>';
				
				echo ' <td> ';
				if($i%16 == 0) {
				echo ' </tr>';
				echo ' <tr>';
				}
				
				$i++;
				}
				echo " </tr>";

?></table>