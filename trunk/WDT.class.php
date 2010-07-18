<?
/*
    <WDT Class | An PHP class for editing world of warcraft WDT Files>
    Copyright (C) <2010>  <BugCraft> Also Karliky (karliky@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
ini_set("memory_limit","-1");
class WDT {
	//Variables
	var $WDT_Handle;
	var $debug;
	var $EndianMode;
	var $WDT_Name;
	//Arrays
	var $WDT_Info;
		function WDT(){
			//Constructor
			$this->EndianMode = true;
			$this->debug = true;
		}
		//////////////////////////////////////////////
		//////////////////////////////////////////////
		//Functions
		//////////////////////////////////////////////
		//////////////////////////////////////////////
		private function p_array($array){
			echo "<pre>";
			print_r($array);
			echo "</pre>";
		}
		
		private function hexToFloat($hex){
			//convert 32bit HEX values into IEEE 754 floating point 
				if($hex == "00000000")
				{
					return "0.000000";
				}
			$bin = str_pad(base_convert($hex, 16, 2), 32, "0", STR_PAD_LEFT);
			$sign = $bin[0];
			$exp = bindec(substr($bin, 1, 8)) - 127;
			$man = (2 << 22) + bindec(substr($bin, 9, 23));
			
			return $dec = $man * pow(2, $exp - 23) * ($sign ? -1 : 1); 
		}
		
		private function hexToStr($hex)
		{
			//Hexadecimal to string
			$string='';
			for ($i=0; $i < strlen($hex)-1; $i+=2)
			{
				$string .= chr(hexdec($hex[$i].$hex[$i+1]));
			}
			return $string;
		}
		private function EndianConverter($hex)
		{
			if($this->EndianMode){
				return substr($hex,6,2).substr($hex,4,2).substr($hex,2,2).substr($hex,0,2);
			}else{
				return $hex;
			}
		}
		//////////////////////////////////////////////
		//////////////////////////////////////////////
		//WDT initializacion functions
		//////////////////////////////////////////////
		//////////////////////////////////////////////		
		function WDT_Open($WDT_Handle){
			  if(($this->WDT_Handle = fopen($WDT_Handle, "r+b")) === FALSE)	//Read/Write binary mode
			  { die("Can't open the filez!!!!11 ._ ."); }
			  $this->WDT_Name = explode(".",$WDT_Handle);
		}
		//===================================
		//WDT SHOW INFO
		//===================================
		function WDT_Show(){
		
		fseek($this->WDT_Handle,0x3C,SEEK_SET);
		$this->WDT_Info = array(array());
		for($x = 0;$x < 4096;$x++){

				for($i = 0;$i < 64;$i++){	
				$WDT_DATAZ = bin2hex(fread($this->WDT_Handle,4));						
				$this->WDT_Info[$x][$i] = $this->EndianConverter($WDT_DATAZ);	
				bin2hex(fread($this->WDT_Handle,4));		
				}
				

		}

#
			$WDT_HTML = '<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.8.2.custom.css" type="text/css" media="screen" />';
			$WDT_HTML .= '<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>';
			$WDT_HTML .= '<script type="text/javascript" src="js/jquery-ui-1.8.2.custom.min.js"></script>';
			$WDT_HTML .= '<script type="text/javascript" src="js/jquery.simpletip-1.3.1.pack.js"></script>';

			$WDT_HTML .= '<script type="text/javascript">';
			$WDT_HTML .= '	$(function() {';
			$WDT_HTML .= '		$("#dialog").dialog();';
			$WDT_HTML .= '		$("#dialog").dialog( "option", "position", ["center","top"]  );';
			$WDT_HTML .= '		$("#dialog").dialog( "option", "title", "WDT Information" );';
			$WDT_HTML .= '		$("#dialog").dialog( "option", "width", 310 );';
			for($x = 1;$x < 65;$x++){
					for($i = 1;$i < 65;$i++){
						
						if($this->WDT_Info[$x][$i] == 00000000){
						}else{
						$WDT_HTML .= '$("#X'.$x."Y".$i.'").simpletip({ content: "'.$this->WDT_Name[0].'_'.$i."_".$x.'.adt", fixed: true });';

						}
					}
				}
			$WDT_HTML .= '	});';
			$WDT_HTML .= '$(document).ready(function(){';
			for($x = 1;$x < 65;$x++){
					for($i = 1;$i < 65;$i++){

							$WDT_HTML .= '$("#X'.$x."Y".$i.'").click(function(){';
							
							$WDT_HTML .= '$.get("WDT.class.php?i='.$i.'&x='.$x.'", function(data) {';
							$WDT_HTML .= '$("#prueba").append(data);';
							$WDT_HTML .= '});';

							$WDT_HTML .= 'if($("#X'.$x."Y".$i.'").hasClass("blue")) {';
							$WDT_HTML .= "$('#X".$x."Y".$i."').removeClass('blue').addClass('grey');";
							$WDT_HTML .= '$("#X'.$x."Y".$i.'").simpletip({ content: "This ADT Was deleted", fixed: true });';

							$WDT_HTML .= '}';
							$WDT_HTML .= 'else {';
							$WDT_HTML .= "$('#X".$x."Y".$i."').removeClass('grey').addClass('blue');";
							$WDT_HTML .= '$("#X'.$x."Y".$i.'").simpletip({ content: "'.$this->WDT_Name[0].'_'.$i."_".$x.'.adt", fixed: true });';
							$WDT_HTML .= '}';

							$WDT_HTML .= '});';

					}
				}
			$WDT_HTML .= '}); ';
			$WDT_HTML .= '	</script>';
			$WDT_HTML .= '				<style type="text/css">
					.tooltip{
					   position: absolute;
					   margin:0px;
					   padding:0px;
					   z-index: 2;
					   color: #303030;
					   background-color: #f5f5b5;
					   border: 2px solid #DECA7E;
					   font-family: sans-serif;
					   font-size: 12px;
					   line-height: 18px;
					   text-align: center;
					}
					#water {
					border: 1px solid #000;
					}
					a{
					color:#000;
					}
					.blue{
					background-color:#0000FF;
					}
					.grey{
					background-color:#000000;
					}
				 }
				</style>';
			$WDT_HTML .= '<style type="text/css"> body{ background-color:#000000;}</style>';		
			
			$WDT_HTML .= '<div id="dialog"><div id="prueba"></div></div>';		
			$WDT_HTML .= '<table width="100%" height="75%" border="0" cellspacing="0" cellpadding="1">';
			
				for($x = 1;$x < 65;$x++){
						$WDT_HTML .= '<tr>';
						for($i = 1;$i < 65;$i++){
						if($this->WDT_Info[$x][$i] == 00000000){
						$BACKGOUND = 'class="grey"';
						}else{
						$BACKGOUND = 'class="blue"';
						}
							$WDT_HTML .= '<td '.$BACKGOUND .' id="X'.$x."Y".$i.'">&nbsp;</td>';
						}
					$WDT_HTML .= '</tr>';
				}
				$WDT_HTML .= '</table>';

		echo $WDT_HTML;
		}

		//===================================
		//WDT update INFO
		//===================================
		function WDT_Update(){
								fseek($this->WDT_Handle,0x3C,SEEK_SET);
								for($x = 0;$x < 4096;$x++){
						
										for($i = 0;$i < 64;$i++){	

												if($_GET['i'] == $i && $_GET['x'] == $x)
												{
												
												$WDT_DATAZ = bin2hex(fread($this->WDT_Handle,4));						
												fseek($this->WDT_Handle,-4,SEEK_CUR);
												if($this->EndianConverter($WDT_DATAZ) == 00000000){
														//New ADT
														fwrite($this->WDT_Handle,pack("V",1),4);
														bin2hex(fread($this->WDT_Handle,4));
														echo "Kalimdor_".$i."_".$x.".adt<br>";

												}else{
														
														fwrite($this->WDT_Handle,pack("V",0),4);
														bin2hex(fread($this->WDT_Handle,4));
														//echo "Location deleted at Y: $i X: $x<br>";
												}
												}else{
														bin2hex(fread($this->WDT_Handle,4));
														bin2hex(fread($this->WDT_Handle,4));		
												}
												
											}	
										}
										
						
								

		}
}

$MyWDT = new WDT();
$MyWDT->WDT_Open("../../wdt/Kalimdor.wdt");
if(isset($_GET['i']) & isset($_GET['x'])){
$MyWDT->WDT_Update();
}else{
$MyWDT->WDT_Show();
}
?>
