<?
/*
    <ADT Class | An PHP class for editing world of warcraft maps>
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
set_time_limit(0);

class ADT {
	//Variables
	var $ADT_Handle;
	var $debug;
	var $EndianMode;
	var $ADT_FileName;
	//Arrays
	var $STRUCT_INFO;
	var $MTEX_Data;
	var $MDDX_Data;
	var $WMO_Data;
	var $MDDX_DATAZ;
	var $WMO_DATAZ;
	var $WATER_DATAZ;
	var $WATER_DATAZ_BLOCK;
	var $MCIN_DATA;
	var $GOATSE_DATA;
	var $NEW_OFFSETS_TYPE;
	var $NEW_OFFSETS;
	
		function ADT(){
			//Constructor
			$this->EndianMode = true;
			$this->debug = false;
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
		//ADT initializacion functions
		//////////////////////////////////////////////
		//////////////////////////////////////////////		
		function ADT_Open($ADT_FILE){
			  if(($this->ADT_Handle = fopen($ADT_FILE, "r+b")) === FALSE)	//Read/Write binary mode
			  { die("Can't open the filez!!!!11 ._ ."); }
			  $this->ADT_FileName = $ADT_FILE;
		}
		//===================================
		//ADT HEADER INFO
		//===================================		
		function ADT_HeaderInfo(){
				fseek($this->ADT_Handle,0x1c,SEEK_SET);
				$this->STRUCT_INFO = array("");
				unset($this->STRUCT_INFO[0]);
				$this->STRUCT_INFO["MTEX_Offset"] = bin2hex(fread($this->ADT_Handle,4));
				$this->STRUCT_INFO["MMDX_Offset"] = bin2hex(fread($this->ADT_Handle,4));
				$this->STRUCT_INFO["MMID_Offset"] = bin2hex(fread($this->ADT_Handle,4));
				$this->STRUCT_INFO["MWMO_Offset"] = bin2hex(fread($this->ADT_Handle,4));
				$this->STRUCT_INFO["MWID_Offset"] = bin2hex(fread($this->ADT_Handle,4));
				$this->STRUCT_INFO["MDDF_Offset"] = bin2hex(fread($this->ADT_Handle,4));
				$this->STRUCT_INFO["MODF_Offset"] = bin2hex(fread($this->ADT_Handle,4));
				$this->STRUCT_INFO["MFBO_Offset"] = bin2hex(fread($this->ADT_Handle,4));
				$this->STRUCT_INFO["MH2O_Offset"] = bin2hex(fread($this->ADT_Handle,4));
				$this->STRUCT_INFO["MTFX_Offset"] = bin2hex(fread($this->ADT_Handle,4));

				if ($this->debug):	$this->p_array($this->STRUCT_INFO);  endif;

		}
		
		//////////////////////////////////////////////
		//////////////////////////////////////////////
		//HTML Table that prints an array
		//////////////////////////////////////////////
		//////////////////////////////////////////////		
		function ADT_ShowTable($Arg){
		if(strtoupper($Arg) == "M2"){
		$Array = $this->ADT_M2_GetInfo();
		}elseif(strtoupper($Arg) == "WMO"){
		$Array = $this->ADT_WMO_GetInfo();
		}
		$HTML = '';
		$HTML .= '<style type="text/css">';
		$HTML .= '<!--';
		$HTML .= '#rounded-corner';
		$HTML .= '{';
		$HTML .= '	font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;';
		$HTML .= '	font-size: 12px;';
		$HTML .= '	text-align: left;';
		$HTML .= '	border-collapse: collapse;';
		$HTML .= '}';
		$HTML .= '#rounded-corner th';
		$HTML .= '{';
		$HTML .= '	padding: 8px;';
		$HTML .= '	font-weight: normal;';
		$HTML .= '	font-size: 13px;';
		$HTML .= '	color: #039;';
		$HTML .= '	background: #b9c9fe;';
		$HTML .= '}';
		$HTML .= '#rounded-corner td';
		$HTML .= '{';
		$HTML .= '	padding: 8px;';
		$HTML .= '	background: #e8edff;';
		$HTML .= '	border-top: 1px solid #fff;';
		$HTML .= '	color: #669;';
		$HTML .= '}';
		$HTML .= '#rounded-corner tbody tr:hover td';
		$HTML .= '{';
		$HTML .= '	background: #d0dafd;';
		$HTML .= '}';
		$HTML .= '-->';
		$HTML .= '</style>';
		$HTML .= '<div id="tabladecontenido"  align="center">';
		$HTML .= '<table id="rounded-corner" summary="2007 Major IT Companies\' Profit" style="width:100%;overflow:auto;">';
		$HTML .= '    <thead>';
 		$HTML .= '   	<tr>';
		//Header
		foreach(array_keys($Array[0]) as $Key => $Value){
 		$HTML .= '       	<th class="rounded-company" scope="col">'.$Value.'</th>';
		}
		$HTML .= '        </tr>';
		$HTML .= '    </thead>';
		$HTML .= '        <tfoot>';
 		$HTML .= '   	<tr>';
		//Footer
		foreach(array_keys($Array[0]) as $Key => $Value){
 		$HTML .= '       	<th class="rounded-company" scope="col">&nbsp;</th>';
		}
		$HTML .= '        </tr>';
		$HTML .= '    </tfoot>';
		$HTML .= '    <tbody>';
		for($x = 0;$x < count($Array);$x++){
		$HTML .= '    	<tr>';
			foreach($Array[$x] as $Key => $Value){
				$HTML .= '       	<td class="rounded-company" scope="col">'.$Value.'</td>';
			}
		$HTML .= ' 		</tr>';
		}
		$HTML .= '    </tbody>';
		$HTML .= '</table>';
		//$this->p_array($Array);
		return $HTML;
		}
		//===================================
		//ADT MTEX INFO
		//===================================
		function ADT_Load_Textures()
		{
	
		$OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["MTEX_Offset"]);
		fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE,SEEK_SET);
		$MTEX_Num = bin2hex(fread($this->ADT_Handle,4));
		$this->MTEX_Data = array("");
		unset($this->MTEX_Data["0"]);
		$Cadena_Actual = "";

			for($i=0;$i<hexdec($this->EndianConverter($MTEX_Num));$i++):
			
				$Byte_Actual = bin2hex(fread($this->ADT_Handle,1));

				if($Byte_Actual == "00"){						//  it's -0 terminated string and then we stop reading
					$this->MTEX_Data[] = $Cadena_Actual;
					$Cadena_Actual = "";
				}else{
					$Cadena_Actual .= $this->hexToStr($Byte_Actual);
				}
			endfor;
			if ($this->debug):	$this->p_array($this->MTEX_Data);  endif;
			return $this->MTEX_Data;
		}
		
		//===================================
		//ADT MDDX INFO
		//===================================
		function ADT_Load_M2_Patch()
		{
	
		$OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["MMDX_Offset"]);
		fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE,SEEK_SET);
		$MDDX_Num = bin2hex(fread($this->ADT_Handle,4));
		$this->MDDX_Data = array("");
		unset($this->MDDX_Data["0"]);
		$Cadena_Actual = "";

			for($i=0;$i<hexdec($this->EndianConverter($MDDX_Num));$i++):
			
				$Byte_Actual = bin2hex(fread($this->ADT_Handle,1));

				if($Byte_Actual == "00"){						//  it's -0 terminated string and then we stop reading
					$this->MDDX_Data[] = $Cadena_Actual;
					$Cadena_Actual = "";
				}else{
					$Cadena_Actual .= $this->hexToStr($Byte_Actual);
				}
			endfor;
			if ($this->debug):	$this->p_array($this->MDDX_Data);  endif;
			return $this->MDDX_Data;
		}
		
		//===================================
		//ADT MWMO INFO
		//===================================
		function ADT_Load_WMO_Patch()
		{
			
		$OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["MWMO_Offset"]);
		fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE,SEEK_SET);
		$WMO_Num = bin2hex(fread($this->ADT_Handle,4));
		$this->WMO_Data = array("");
		unset($this->WMO_Data["0"]);
		$Cadena_Actual = "";

			for($i=0;$i<hexdec($this->EndianConverter($WMO_Num));$i++):
			
				$Byte_Actual = bin2hex(fread($this->ADT_Handle,1));

				if($Byte_Actual == "00"){						//  it's -0 terminated string and then we stop reading
					$this->WMO_Data[] = $Cadena_Actual;
					$Cadena_Actual = "";
				}else{
					$Cadena_Actual .= $this->hexToStr($Byte_Actual);
				}
			endfor;
			if ($this->debug):	$this->p_array($this->WMO_Data);  endif;
			return $this->WMO_Data;
		}
		
		//===================================
		//ADT MDDF INFO ID UNIQUEID X Z Y A B C SCALE
		//===================================
		function ADT_M2_GetInfo()
		{
	
			$OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["MDDF_Offset"]);
			fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE,SEEK_SET);
			$NumDoodads = hexdec("0x".$this->EndianConverter(bin2hex(fread($this->ADT_Handle,4))));
			$NumDoodads = $NumDoodads/36;
			$this->MDDX_DATAZ = array(array());
			for($x = 0;$x < $NumDoodads;$x++):

			$MDX_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$MDDX_ID = $this->EndianConverter($MDX_DATA);								
			
			$MDX_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$MDDX_UNIQUEID = $this->EndianConverter($MDX_DATA);						
			
			$MDX_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$MDDX_X = $this->hexToFloat($this->EndianConverter($MDX_DATA));					
			
			$MDX_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$MDDX_Z = $this->hexToFloat($this->EndianConverter($MDX_DATA));					
			
			$MDX_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$MDDX_Y = $this->hexToFloat($this->EndianConverter($MDX_DATA));					
			
			$MDX_DATA = bin2hex(fread($this->ADT_Handle,4));								
			$MDDX_A = $this->hexToFloat($this->EndianConverter($MDX_DATA));			
			
			$MDX_DATA = bin2hex(fread($this->ADT_Handle,4));								
			$MDDX_B = $this->hexToFloat($this->EndianConverter($MDX_DATA));			
			
			$MDX_DATA = bin2hex(fread($this->ADT_Handle,4));								
			$MDDX_C = $this->hexToFloat($this->EndianConverter($MDX_DATA));			
			
			$MDX_DATA = bin2hex(fread($this->ADT_Handle,4));								
			$MDDX_SCALE = $this->EndianConverter($MDX_DATA);				
			
			$this->MDDX_DATAZ[$x][MDDX_ID] = hexdec($MDDX_ID);
			$this->MDDX_DATAZ[$x][MDDX_UNIQUEID] = hexdec($MDDX_UNIQUEID);
			
			$this->MDDX_DATAZ[$x][MDDX_X] = $MDDX_X;
			$this->MDDX_DATAZ[$x][MDDX_Y] = $MDDX_Y;
			$this->MDDX_DATAZ[$x][MDDX_Z] = $MDDX_Z;
			
			$this->MDDX_DATAZ[$x][MDDX_A] = $MDDX_A;
			$this->MDDX_DATAZ[$x][MDDX_B] = $MDDX_B;
			$this->MDDX_DATAZ[$x][MDDX_C] = $MDDX_C;
			
			$this->MDDX_DATAZ[$x][MDDX_SCALE] = hexdec($MDDX_SCALE);

			endfor;
			if ($this->debug):	$this->p_array($this->MDDX_DATAZ);  endif;
			return $this->MDDX_DATAZ;
		}
		
			//===================================
			//ADT WMO INFO
			//===================================	
			function ADT_WMO_GetInfo()
			{
			$OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["MODF_Offset"]);
			fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE,SEEK_SET);
			
			$NumWMO = hexdec("0x".$this->EndianConverter(bin2hex(fread($this->ADT_Handle,4))));
			$NumWMO = $NumWMO/64;
			$WMO_DATAZ = array(array());
			for($x = 0;$x < $NumWMO;$x++):
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_ID = $this->EndianConverter($WMO_DATA);								
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_UNIQUEID = $this->EndianConverter($WMO_DATA);	
				
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_X = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_Z = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_Y = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_A = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_B = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_C = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_UPPER_A = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_UPPER_B = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_UPPER_C = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_LOWER_A = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_LOWER_B = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_LOWER_C = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,2));						
			$WMO_FLAGS = $this->EndianConverter($WMO_DATA);	
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,2));						
			$WMO_DOODADINDEX = $this->EndianConverter($WMO_DATA);	
			
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_PADDING = $this->EndianConverter($WMO_DATA);	
			
			$this->WMO_DATAZ[$x][WMO_ID] = hexdec($WMO_ID);
			$this->WMO_DATAZ[$x][WMO_UNIQUEID] = hexdec($WMO_UNIQUEID);
			
			$this->WMO_DATAZ[$x][WMO_X] = $WMO_X;
			$this->WMO_DATAZ[$x][WMO_Y] = $WMO_Y;
			$this->WMO_DATAZ[$x][WMO_Z] = $WMO_Z;
			
			$this->WMO_DATAZ[$x][WMO_A] = $WMO_A;
			$this->WMO_DATAZ[$x][WMO_B] = $WMO_B;
			$this->WMO_DATAZ[$x][WMO_C] = $WMO_C;
			
			$this->WMO_DATAZ[$x][WMO_UPPER_A] = $WMO_UPPER_A;
			$this->WMO_DATAZ[$x][WMO_UPPER_B] = $WMO_UPPER_B;
			$this->WMO_DATAZ[$x][WMO_UPPER_C] = $WMO_UPPER_C;
			
			$this->WMO_DATAZ[$x][WMO_LOWER_A] = $WMO_LOWER_A;
			$this->WMO_DATAZ[$x][WMO_LOWER_B] = $WMO_LOWER_B;
			$this->WMO_DATAZ[$x][WMO_LOWER_C] = $WMO_LOWER_C;
			
			$this->WMO_DATAZ[$x][WMO_FLAGS] = hexdec($WMO_FLAGS);
			$this->WMO_DATAZ[$x][WMO_DOODADINDEX] = hexdec($WMO_DOODADINDEX);
			$this->WMO_DATAZ[$x][WMO_PADDING] = hexdec($WMO_PADDING);

			
			endfor;
			if ($this->debug):	$this->p_array($this->WMO_DATAZ);  endif;
			return $this->WMO_DATAZ;
			}
			//===================================
			//ADT All WMO Z UP/DOWN
			//===================================		
			function ADT_WMO_AllZ($Z)
			{
				$OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["MODF_Offset"]);
				fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE,SEEK_SET);
				
				$NumWMO = hexdec("0x".$this->EndianConverter(bin2hex(fread($this->ADT_Handle,4))));
				$NumWMO = $NumWMO/64;
				$WMO_DATAZ = array(array());
				
				for($x = 0;$x < $NumWMO;$x++):
					fseek($this->ADT_Handle,12,SEEK_CUR);	
					$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));
					$WMO_Z = $this->hexToFloat($this->EndianConverter($WMO_DATA));			
					fseek($this->ADT_Handle,-4,SEEK_CUR);
					fwrite($this->ADT_Handle,pack("f",$WMO_Z + $Z),4);
					fseek($this->ADT_Handle,48,SEEK_CUR);	
				endfor;
				return true;
			}
			//===================================
			//ADT All MD2 Z UP/DOWN
			//===================================		
			function ADT_M2_AllZ($Z)
			{
				$OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["MDDF_Offset"]);
				fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE,SEEK_SET);
				
				$NumMDDX = hexdec("0x".$this->EndianConverter(bin2hex(fread($this->ADT_Handle,4))));
				$NumMDDX = $NumMDDX/36;
				$this->MDDX_DATAZ = array(array());
				
				for($x = 0;$x < $NumMDDX;$x++):

					fseek($this->ADT_Handle,12,SEEK_CUR);	
					$MDX_DATA = bin2hex(fread($this->ADT_Handle,4));
					$MDDX_Z = $this->hexToFloat($this->EndianConverter($MDX_DATA));	
					fseek($this->ADT_Handle,-4,SEEK_CUR);
					fwrite($this->ADT_Handle,pack("f",$MDDX_Z + $Z),4);
					fseek($this->ADT_Handle,20,SEEK_CUR);	
					
				endfor;
				return true;
			}
			//===================================
			//ADT MH2O INFO
			//===================================		
			function ADT_MH2O_info()
			{
				$OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["MH2O_Offset"]);
				fseek($this->ADT_Handle,0x14+0x04+0x04+$OFFVALUE,SEEK_SET);
				
				$NumWater = 256;
				$this->WATER_DATAZ = array(array());
				
				for($x = 0;$x < $NumWater;$x++):
				
					$WATER_DATA = fread($this->ADT_Handle,4);						
					$this->WATER_DATAZ[$x][WATER_OFFSET] = unpack('V',$WATER_DATA);						//Use decimal int value
					$WATER_DATA = bin2hex(fread($this->ADT_Handle,4));						
					$this->WATER_DATAZ[$x][WATER_LAYERCOUNT] = $this->EndianConverter($WATER_DATA);	
					$WATER_DATA = bin2hex(fread($this->ADT_Handle,4));						
					$this->WATER_DATAZ[$x][WATER_OFSRENDERMASK] = $this->EndianConverter($WATER_DATA);	
				endfor;
				//$this->p_array($this->WATER_DATAZ);
				return $this->WATER_DATAZ;			
			}
            //===================================
			//ADT MH2O INFOBLOCK
			//===================================
			function ADT_MH2O_InfoBlock()
			{
				$OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["MH2O_Offset"]);
				$NumWater = 256;
				$this->WATER_DATAZ_BLOCK = array(array());// Get offsets

				for($x = 0;$x < $NumWater;$x++):
				if($this->WATER_DATAZ[$x][WATER_OFFSET][1] == 0){
				///There are not water!._ .
				}else{
				
				//MH2O_Information
                fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE+0x4+$this->WATER_DATAZ[$x][WATER_OFFSET][1],SEEK_SET);
				//HeightLevel
				fread($this->ADT_Handle,0x4);
				$Height_Level = bin2hex(fread($this->ADT_Handle,4));//UNUSED			
				$Height_Level = bin2hex(fread($this->ADT_Handle,4));//UNUSED				
				//HeightLevel
				$Byte_Data = bin2hex(fread($this->ADT_Handle,1));	
				$xOffset =  hexdec($this->EndianConverter($Byte_Data));
				$Byte_Data = bin2hex(fread($this->ADT_Handle,1));
				$iOffset =  hexdec($this->EndianConverter($Byte_Data));
				$Byte_Data = bin2hex(fread($this->ADT_Handle,1));
				$widht =  hexdec($this->EndianConverter($Byte_Data))+ 1;
				$Byte_Data = bin2hex(fread($this->ADT_Handle,1));
				$height =  hexdec($this->EndianConverter($Byte_Data))+ 1;
				//
				fread($this->ADT_Handle,4);
				$Height_Offset = unpack('V',fread($this->ADT_Handle,4));	//Unpack returns an array so be careful
				
				//MH2O_HeightmapData
					fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE+0x4+$Height_Offset[1],SEEK_SET);	//Seek at the water level
				//Parte wapah
				$Total_Floats = $widht*$height;
				
				for($i = 0;$i<$Total_Floats;$i++){
								
					$Height_Level = bin2hex(fread($this->ADT_Handle,4));
					$this->WATER_DATAZ_BLOCK[$x]["WaterLVL"] = $this->hexToFloat($this->EndianConverter($Height_Level));
				
				}
				
				}
				endfor;
				return $this->WATER_DATAZ_BLOCK;
			}
			//===================================
			//ADT MH2O Set Water Level
			//===================================
			function ADT_MH2O_Set_Water_level($Level)
			{
				$OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["MH2O_Offset"]);
				$NumWater = 256;

				for($x = 0;$x < $NumWater;$x++):
				if($this->WATER_DATAZ[$x][WATER_OFFSET][1] == 0){
				///There are not water!._ .
				}else{
				
				//MH2O_Information
                fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE+0x4+$this->WATER_DATAZ[$x][WATER_OFFSET][1],SEEK_SET);
				//HeightLevel
				fread($this->ADT_Handle,0x4);
				$Height_Level = bin2hex(fread($this->ADT_Handle,4));
				fseek($this->ADT_Handle,-4,SEEK_CUR);
				fwrite($this->ADT_Handle,pack("f",$this->hexToFloat($this->EndianConverter($Height_Level)) + $Level),4);			//Ocean	
				$Height_Level = bin2hex(fread($this->ADT_Handle,4));
				fseek($this->ADT_Handle,-4,SEEK_CUR);
				fwrite($this->ADT_Handle,pack("f",$this->hexToFloat($this->EndianConverter($Height_Level)) + $Level),4);			//Ocean
				//HeightLevel
				$Byte_Data = bin2hex(fread($this->ADT_Handle,1));	
				$xOffset =  hexdec($this->EndianConverter($Byte_Data));
				$Byte_Data = bin2hex(fread($this->ADT_Handle,1));
				$iOffset =  hexdec($this->EndianConverter($Byte_Data));
				$Byte_Data = bin2hex(fread($this->ADT_Handle,1));
				$widht =  hexdec($this->EndianConverter($Byte_Data))+ 1;
				$Byte_Data = bin2hex(fread($this->ADT_Handle,1));
				$height =  hexdec($this->EndianConverter($Byte_Data))+ 1;
				//
				fread($this->ADT_Handle,4);
				$Height_Offset = unpack('V',fread($this->ADT_Handle,4));	//Unpack returns an array so be careful
				
				//MH2O_HeightmapData
				fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE+0x4+$Height_Offset[1],SEEK_SET);	//Seek at the water level
				//Parte wapah
				$Total_Floats = $widht*$height;
				for($i = 0;$i<$Total_Floats;$i++){

				$Height_Level = bin2hex(fread($this->ADT_Handle,4));
				fseek($this->ADT_Handle,-4,SEEK_CUR);
				fwrite($this->ADT_Handle,pack("f",$this->hexToFloat($this->EndianConverter($Height_Level)) + $Level),4);

			
				}
				
				}
				endfor;

			}
			//===================================
			//ADT Draw MH2O 
			//===================================	
			function ADT_MH2O_Draw()
			{
				$WATER_DATAZ = $this->ADT_MH2O_info();
				$WATER_DATAZ_INFO = $this->ADT_MH2O_InfoBlock();
				echo'
				<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
				<script type="text/javascript" src="js/jquery.simpletip-1.3.1.pack.js"></script>
				<script type="text/javascript">
				$(document).ready(function (){';
				for($x = 0;$x<256;$x++){
				if($WATER_DATAZ[$x][WATER_OFFSET][1] == 0){
				$water = "No";
				$water_offset = "";
				$water_offset2 = "";
				}else{
				$water = "Yes";
				$water_offset = $this->WATER_DATAZ_BLOCK[$x]["WaterLVL"];
				}
				echo '$("#'.$x.'").simpletip({ content: "CHUNK '.$x.' Info<br><ul><li> -Water: '.$water.'</li>';
				if($water == "Yes"){
				echo '<li>-Water LVL: '.$water_offset.'</li>';
				}
				echo '</lu>", fixed: true });';
				}
				echo '
				});
				</script>
				<style type="text/css">
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
				 }
				</style>
				';
				echo '<table id="water" width="50%" height="50%" border="0" cellpadding="0" cellspacing="1" style="border-color:#000000;"> ';
				echo '<caption>'.$this->ADT_FileName.'</caption>';
				echo ' <tr>';
				$i = 1;
				for($x = 0;$x<256;$x++){
				if($WATER_DATAZ[$x][WATER_OFFSET][1] == 0){
				$water = false;
				}else{
				$water = true;
				}
				if($water){ $BACKGOUND = 'bgcolor="blue"'; } else { $BACKGOUND = ''; }
				echo '<td '.$BACKGOUND.' id="'.$x.'" ><div id="'.$x.'">&nbsp;</div>';
				
				echo ' <td> ';
				if($i%16 == 0) {
				echo ' </tr>';
				echo ' <tr>';
				}
				
				$i++;
				}
				echo " </tr>";
				echo "</table>";
			}
			//===================================
			//OffsetFix
			//===================================	
			function ADT_OffsetFix($New_y,$New_x)
			{

				//MCIN
				$this->MCIN_DATA = array(array());
				fseek($this->ADT_Handle,92,SEEK_SET);																	//MCIN HEADER
				for($x = 0;$x<256;$x++){
			
				$this->MCIN_DATA[$x][OFFSET] = $this->EndianConverter(bin2hex(fread($this->ADT_Handle,4)));				// absolute offset.
				$this->MCIN_DATA[$x][SIZE] = bin2hex(fread($this->ADT_Handle,4));										// the size of the MCNK chunk, this is refering to.
				$this->MCIN_DATA[$x][UNUSED] = bin2hex(fread($this->ADT_Handle,4));										// these two are always 0. only set in the client.
				$this->MCIN_DATA[$x][UNUSED1] = bin2hex(fread($this->ADT_Handle,4));	 		

				}

				//FIX MCNKs
				for($i = 0;$i<256;$i++){
					if($i == 0)
					{
					//Data that we will use later ._ .
					$File_Y = $New_x;
					$File_X = $New_y;
					
					$Offset = "0x".$this->MCIN_DATA[$i][OFFSET];
					fseek($this->ADT_Handle,0x0+$Offset+0x68+8,SEEK_SET);

					$oldX = $this->hexToFloat($this->EndianConverter(bin2hex(fread($this->ADT_Handle,4))));
					$oldY = $this->hexToFloat($this->EndianConverter(bin2hex(fread($this->ADT_Handle,4))));

					$Y=(1600.0*(32-$File_Y))/3.0-100.0*($i/16)/3.0;
					$X=(1600.0*(32-$File_X))/3.0-100.0*($i%16)/3.0;

					//Its Reversed Remember
					$X_Offset=$oldX-$X;
					$Z_Offset=$oldY-$Y;

					}
					$Offset = "0x".$this->MCIN_DATA[$i][OFFSET];
					fseek($this->ADT_Handle,0x0+$Offset+0x68+8,SEEK_SET);
					$Y=(1600.0*(32-32))/3.0-100.0*($i/16)/3.0;
					$X=(1600.0*(32-48))/3.0-100.0*($i%16)/3.0;
					fwrite($this->ADT_Handle,pack("f",$Y),4);
					fwrite($this->ADT_Handle,pack("f",$X),4);
				}
				
			//FIX M2
			$OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["MDDF_Offset"]);
			fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE,SEEK_SET);
			$NumDoodads = hexdec("0x".$this->EndianConverter(bin2hex(fread($this->ADT_Handle,4))));
			$NumDoodads = $NumDoodads/36;
			
			fread($this->ADT_Handle,8);
			for($x = 0;$x < $NumDoodads;$x++):
			
			$M2DATAZ = bin2hex(fread($this->ADT_Handle,4));
			$Off_X = $this->hexToFloat($this->EndianConverter($M2DATAZ));			
			fseek($this->ADT_Handle,-4,SEEK_CUR);
			fwrite($this->ADT_Handle,pack("f",$Off_X + $X_Offset),4);			//New X
			
			$M2DATAZ = bin2hex(fread($this->ADT_Handle,4));
			$Off_Z = $this->hexToFloat($this->EndianConverter($M2DATAZ));		//New Z	
			fseek($this->ADT_Handle,-4,SEEK_CUR);
			fwrite($this->ADT_Handle,pack("f",$Off_Z),4);
			
			$M2DATAZ = bin2hex(fread($this->ADT_Handle,4));
			$Off_Y = $this->hexToFloat($this->EndianConverter($M2DATAZ));		//New Y			
			fseek($this->ADT_Handle,-4,SEEK_CUR);
			fwrite($this->ADT_Handle,pack("f",$Off_Y + $Z_Offset),4);

			fseek($this->ADT_Handle,24,SEEK_CUR);
			endfor;
			//FIX WMO
			$OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["MODF_Offset"]);
			fseek($this->ADT_Handle,0x14+0x04+$OFFVALUE,SEEK_SET);
			
			$NumWMO = hexdec("0x".$this->EndianConverter(bin2hex(fread($this->ADT_Handle,4))));
			$NumWMO = $NumWMO/64;
			fseek($this->ADT_Handle,0x08,SEEK_CUR);
			for($x = 0;$x < $NumWMO;$x++):

			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_X = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			fseek($this->ADT_Handle,-4,SEEK_CUR);
			fwrite($this->ADT_Handle,pack("f",$WMO_X + $X_Offset),4);
			
			fseek($this->ADT_Handle,4,SEEK_CUR); //Z

			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_Y = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			fseek($this->ADT_Handle,-4,SEEK_CUR);
			fwrite($this->ADT_Handle,pack("f",$WMO_Y + $Z_Offset),4);
	
			fseek($this->ADT_Handle,12,SEEK_CUR); //Z
	
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_X = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			fseek($this->ADT_Handle,-4,SEEK_CUR);
			fwrite($this->ADT_Handle,pack("f",$WMO_X + $X_Offset),4);
			
			fseek($this->ADT_Handle,4,SEEK_CUR); //Z

			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_Y = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			fseek($this->ADT_Handle,-4,SEEK_CUR);
			fwrite($this->ADT_Handle,pack("f",$WMO_Y + $Z_Offset),4);
	
			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_X = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			fseek($this->ADT_Handle,-4,SEEK_CUR);
			fwrite($this->ADT_Handle,pack("f",$WMO_X + $X_Offset),4);
			
			fseek($this->ADT_Handle,4,SEEK_CUR); //Z

			$WMO_DATA = bin2hex(fread($this->ADT_Handle,4));						
			$WMO_Y = $this->hexToFloat($this->EndianConverter($WMO_DATA));
			fseek($this->ADT_Handle,-4,SEEK_CUR);
			fwrite($this->ADT_Handle,pack("f",$WMO_Y + $Z_Offset),4);
	
			fseek($this->ADT_Handle,16,SEEK_CUR);
			endfor;
			return true;
			}
			//===================================
			//Make Holes
			//===================================	
			function ADT_Make_Goatse($CHUNK_TO_GOATSE)
			{
			//http://en.wikipedia.org/wiki/Goatse.cx OMG!
			//MCIN
				$this->MCIN_DATA = array(array());				
				fseek($this->ADT_Handle,92,SEEK_SET);						//MCIN HEADER
				for($x = 0;$x<256;$x++){
				
					$this->MCIN_DATA[$x][OFFSET] = $this->EndianConverter(bin2hex(fread($this->ADT_Handle,4)));				// absolute offset.
					$this->MCIN_DATA[$x][SIZE] = bin2hex(fread($this->ADT_Handle,4));				// the size of the MCNK chunk, this is refering to.
					$this->MCIN_DATA[$x][UNUSED] = bin2hex(fread($this->ADT_Handle,4));				// these two are always 0. only set in the client.
					$this->MCIN_DATA[$x][UNUSED1] = bin2hex(fread($this->ADT_Handle,4));	 		
	
				}
					//Seek at Chunk
					for($x = 0;$x<256;$x++){
						if($x == $CHUNK_TO_GOATSE){
							$Offset = "0x".$this->MCIN_DATA[$x][OFFSET];
							fseek($this->ADT_Handle,0x0+$Offset+8+0x03C,SEEK_SET);
							$HaChunk = hexdec($this->EndianConverter(bin2hex(fread($this->ADT_Handle,4))));
							if($HaChunk == 0){
								fseek($this->ADT_Handle,-4,SEEK_CUR);
								fwrite($this->ADT_Handle,pack("V",65535),4); //65535 biggest number from unsigned short will make an entire hole
								echo "You Goatse this chunk!<br>";
							}else{
								fseek($this->ADT_Handle,-4,SEEK_CUR);
								fwrite($this->ADT_Handle,pack("V",0),4);
								echo "You fixed this chunk!<br>";
							}
						}
					}
			}
			//===================================
			//Read Holes
			//===================================	
			function ADT_Get_Goatse(){
			//http://en.wikipedia.org/wiki/Goatse.cx OMG!
			//MCIN
				$this->MCIN_DATA = array(array());				
				fseek($this->ADT_Handle,92,SEEK_SET);						//MCIN HEADER
				for($x = 0;$x<256;$x++){
				
					$this->MCIN_DATA[$x][OFFSET] = $this->EndianConverter(bin2hex(fread($this->ADT_Handle,4)));				// absolute offset.
					$this->MCIN_DATA[$x][SIZE] = bin2hex(fread($this->ADT_Handle,4));				// the size of the MCNK chunk, this is refering to.
					$this->MCIN_DATA[$x][UNUSED] = bin2hex(fread($this->ADT_Handle,4));				// these two are always 0. only set in the client.
					$this->MCIN_DATA[$x][UNUSED1] = bin2hex(fread($this->ADT_Handle,4));	 		
	
				}
				//Seek at Chunk and read
				$this->GOATSE_DATA = array();	
					for($x = 0;$x<256;$x++){
						
					$Offset = "0x".$this->MCIN_DATA[$x][OFFSET];
					fseek($this->ADT_Handle,0x0+$Offset+8+0x03C,SEEK_SET);
					
							$Goatse_Value = bin2hex(fread($this->ADT_Handle,4));
							$Goatse_Value = hexdec($this->EndianConverter($Goatse_Value));
							$this->GOATSE_DATA[$x] = $Goatse_Value ;
				}
				return $this->GOATSE_DATA;
			}
			//===================================
			//Make Holes
			//===================================	
			function ADT_Show_Goatse()
			{
				$Array= $this->ADT_Get_Goatse();	//Get Hole Information
				//Include
				$Goatse_HTML = '<head><link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.8.2.custom.css" type="text/css" media="screen" />';
				$Goatse_HTML .= '<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>';
				$Goatse_HTML .= '<script type="text/javascript" src="js/jquery-ui-1.8.2.custom.min.js"></script>';
				$Goatse_HTML .= '<script type="text/javascript" src="js/jquery.simpletip-1.3.1.pack.js"></script>';
				//Javascript jQuery
				$Goatse_HTML .= '<script type="text/javascript">';
				$Goatse_HTML .= '	$(function() {';
				$Goatse_HTML .= '		$("#GoatseDialog").dialog();';
				$Goatse_HTML .= '		$("#GoatseDialog").dialog( "option", "position", [700,0]  );';
				$Goatse_HTML .= '		$("#GoatseDialog").dialog( "option", "title", "ADT Information" );';
				$Goatse_HTML .= '});';
				$Goatse_HTML .= '</script>';
				
							$Goatse_HTML .= '<script type="text/javascript">';
							$Goatse_HTML .= '$(document).ready(function(){';
							for($x = 0;$x<256;$x++){
							$Goatse_HTML .= '$("#Goat'.$x.'").click(function(){';
							
							$Goatse_HTML .= '$.get("'.$_SERVER['PHP_SELF'].'?chunk='.$x.'", function(data) {';
							$Goatse_HTML .= '$("#prueba").append(data);';
							$Goatse_HTML .= '});';

							$Goatse_HTML .= 'if($("#Goat'.$x.'").hasClass("black")) {';
							$Goatse_HTML .= "$('#Goat".$x."').removeClass('black').addClass('white');";

							$Goatse_HTML .= '}else {';
							
							$Goatse_HTML .= "$('#Goat".$x."').removeClass('white').addClass('black');";
							$Goatse_HTML .= '}';

							$Goatse_HTML .= '});';
							}

							$Goatse_HTML .= '}); ';
							$Goatse_HTML .= '	</script>';
				//Styles
				$Goatse_HTML .= '
				<style type="text/css">
					#Goatse table
					{
						border-color: #000;
						border-width: 0px;
						border-style: solid;
					}
					#Goatse td
					{
						border-width: 1px;
						border-style: solid;
						margin: 0px;

					}
					.black{
					background-color:#000;
					}
					.white{
					background-color:#fff;
					}
				</style>
				</head>';
				//HTML and draw table
				$Goatse_HTML .= '<div id="GoatseDialog"><div id="prueba"></div></div>';
				$Goatse_HTML .= '<table id="Goatse" width="50%" height="50%" border="0" cellpadding="0" cellspacing="1" style="border-color:#000000;"> ';
				$Goatse_HTML .= '<caption>'.$this->ADT_FileName.'</caption>';
				$Goatse_HTML .= ' <tr>';
				$i = 1;
				for($x = 0;$x<256;$x++){
				if($Array[$x] == 0){
				$Hole = false;
				}else{
				$Hole = true;
				}
				if($Hole){ $BACKGOUND = 'class="black"'; } else { $BACKGOUND = 'class="white"'; }
				$Goatse_HTML .= '<td '.$BACKGOUND.' id="Goat'.$x.'" >&nbsp;</td>';

				if($i%16 == 0) {
				$Goatse_HTML .= ' </tr>';
				$Goatse_HTML .= ' <tr>';
				}
				
				$i++;
				}
				$Goatse_HTML .= " </tr>";
				$Goatse_HTML .= "</table>";
				return $Goatse_HTML;
			}
			
			//===================================
			//Update Offsets CURRENTLY BEING DEVELOPED!
			//===================================	
			function ADT_Get_Offsets(){
			rewind($this->ADT_Handle);
                        
			$ADT_FILE_DATA = bin2hex(fread($this->ADT_Handle,filesize($this->ADT_FileName)));
			$ADT_NEW_FILE_DATA = "";

			rewind($this->ADT_Handle);

			for($i="0"; $i<strlen($ADT_FILE_DATA); $i=$i+4){
				
                            $ADT_NEW_FILE_DATA .=  strtoupper($this->EndianConverter(bin2hex(fread($this->ADT_Handle,4))));

			}

                                $this->NEW_OFFSETS = array();
                                unset($this->NEW_OFFSETS[0]);
                                
				$this->NEW_OFFSETS["MTEX_Offset"] = stripos($ADT_NEW_FILE_DATA,$this->EndianConverter("5845544D"));
				$this->NEW_OFFSETS["MMDX_Offset"] = $this->EndianConverter("58444D4D")." - 58444D4D";
				$this->NEW_OFFSETS["MMID_Offset"] = stripos($ADT_NEW_FILE_DATA,$this->EndianConverter("44494D4"));
				$this->NEW_OFFSETS["MWMO_Offset"] = stripos($ADT_NEW_FILE_DATA,$this->EndianConverter("4F4D574"));
				$this->NEW_OFFSETS["MWID_Offset"] = stripos($ADT_NEW_FILE_DATA,$this->EndianConverter("4449574"));
				$this->NEW_OFFSETS["MDDF_Offset"] = stripos($ADT_NEW_FILE_DATA,$this->EndianConverter("4644444"));
				$this->NEW_OFFSETS["MODF_Offset"] = stripos($ADT_NEW_FILE_DATA,$this->EndianConverter("46444F4"));
				$this->NEW_OFFSETS["MFBO_Offset"] = stripos($ADT_NEW_FILE_DATA,$this->EndianConverter("99999999"));//TO DO
				$this->NEW_OFFSETS["MH2O_Offset"] = stripos($ADT_NEW_FILE_DATA,$this->EndianConverter("4F32484D"));
				$this->NEW_OFFSETS["MTFX_Offset"] = stripos($ADT_NEW_FILE_DATA,$this->EndianConverter("99999999"));//TO DO
				
                                $this->p_array($this->NEW_OFFSETS);
                               // echo $ADT_NEW_FILE_DATA;

			}
			
}

$MyADT = new ADT();
copy("../../maps/Kalimdor_1_1.adt","../../maps/Azeroth_32_48 - copia.adt");
$MyADT->ADT_Open("../../maps/Azeroth_32_48 - copia.adt");
$MyADT->ADT_HeaderInfo();

$MyADT->ADT_OffsetFix(32,48);

?>