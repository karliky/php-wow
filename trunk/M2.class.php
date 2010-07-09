<?
/*
    <M2 Class | An PHP class for editing world of warcraft models>
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
class M2 {
	//Variables
	var $M2_Handle;
	var $debug;
	var $EndianMode;
	//Arrays
	var $M2_Info;
		function M2(){
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
		//M2 initializacion functions
		//////////////////////////////////////////////
		//////////////////////////////////////////////		
		function M2_Open($M2_Handle){
			  if(($this->M2_Handle = fopen($M2_Handle, "r+b")) === FALSE)	//Read/Write binary mode
			  { die("Can't open the filez!!!!11 ._ ."); }
		}
		//===================================
		//M2 HEADER INFO
		//===================================		
		function M2_HeaderInfo(){
                    $this->STRUCT_INFO = array("");
                    unset($this->STRUCT_INFO[0]);
                    //M2 Name
                    fseek($this->M2_Handle,0x8,SEEK_SET);
                    $this->STRUCT_INFO["M2_Name"] = bin2hex(fread($this->M2_Handle,4));
                    $this->STRUCT_INFO["M2_ofsName"] = bin2hex(fread($this->M2_Handle,4));
                    //Animations
                    fseek($this->M2_Handle,0x1C,SEEK_SET);
                    $this->STRUCT_INFO["M2_nAnimations"] = hexdec($this->EndianConverter(bin2hex(fread($this->M2_Handle,4))));
                    $this->STRUCT_INFO["M2_ofsAnimations"] = bin2hex(fread($this->M2_Handle,4));
                    //Bones
                    fseek($this->M2_Handle,0x2C,SEEK_SET);
                    $this->STRUCT_INFO["M2_nBones"] = hexdec($this->EndianConverter(bin2hex(fread($this->M2_Handle,4))));
                    $this->STRUCT_INFO["M2_ofsBones"] = bin2hex(fread($this->M2_Handle,4));

                    if ($this->debug):	$this->p_array($this->STRUCT_INFO);  endif;
		}
		
		//===================================
		//M2 INFO
		//===================================	
		function M2_GetInfo(){
                    $this->M2_Info = array();
                    unset($this->M2_Info[0]);

                    $this->M2_Info["M2_LengthName"] = $this->EndianConverter($this->STRUCT_INFO[M2_Name]);
                    $this->M2_Info["M2_ofsName"] = "0x".$this->EndianConverter($this->STRUCT_INFO[M2_ofsName]);

                    if ($this->debug):	$this->p_array($this->M2_Info);  endif;
		}

		//===================================
		//Big Head mode
		//===================================
		function M2_BigHeadMode(){

                    $OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["M2_ofsBones"]);

                    fseek($this->M2_Handle,0x0+$OFFVALUE,SEEK_SET); // Modo patata de hacer las cosas, Bug en PHP?

                    for($x = 0; $x <  $this->STRUCT_INFO["M2_nBones"];$x++):
                    echo ftell($this->M2_Handle)."<br>";
            $M2_DATA_KEY = bin2hex(fread($this->M2_Handle,4));

			$M2_DATA = bin2hex(fread($this->M2_Handle,4));

			$M2_DATA = bin2hex(fread($this->M2_Handle,1));
			$M2_ParentBone = $this->EndianConverter($M2_DATA);

                        $M2_DATA = bin2hex(fread($this->M2_Handle,1));
			$M2_UnkNown = $this->EndianConverter($M2_DATA);

                        $M2_DATA = bin2hex(fread($this->M2_Handle,1));
			$M2_UnkNown1 = $this->EndianConverter($M2_DATA);
                        
                        $M2_DATA = bin2hex(fread($this->M2_Handle,1));
			$M2_UnkNown2 = $this->EndianConverter($M2_DATA);

                        $M2_DATA = bin2hex(fread($this->M2_Handle,4));
			$M2_Translation = $this->hexToFloat($this->EndianConverter($M2_DATA));
                        
                        $M2_DATA = bin2hex(fread($this->M2_Handle,4));
			$M2_Translation1 = $this->hexToFloat($this->EndianConverter($M2_DATA));

                        $M2_DATA = bin2hex(fread($this->M2_Handle,4));
			$M2_Translation2 = $this->hexToFloat($this->EndianConverter($M2_DATA));

                        $M2_DATA = bin2hex(fread($this->M2_Handle,1));
			$M2_Rotation = $this->hexToFloat($this->EndianConverter($M2_DATA));

                        $M2_DATA = bin2hex(fread($this->M2_Handle,1));
			$M2_Rotation1 = $this->hexToFloat($this->EndianConverter($M2_DATA));

                        $M2_DATA = bin2hex(fread($this->M2_Handle,1));
			$M2_Rotation2 = $this->hexToFloat($this->EndianConverter($M2_DATA));
                        
                        $M2_DATA = bin2hex(fread($this->M2_Handle,1));
			$M2_Rotation3 = $this->hexToFloat($this->EndianConverter($M2_DATA));
			if($M2_DATA_KEY == "ffffffff"){
				bin2hex(fread($this->M2_Handle,4));
				bin2hex(fread($this->M2_Handle,4));
				bin2hex(fread($this->M2_Handle,4));
			}else{
				                        $M2_DATA = bin2hex(fread($this->M2_Handle,4));
			$M2_Scaling = $this->hexToFloat($this->EndianConverter($M2_DATA));
                        echo $M2_Scaling."<br>";
                        $M2_DATA = bin2hex(fread($this->M2_Handle,4));
			$M2_Scaling1 = $this->hexToFloat($this->EndianConverter($M2_DATA));
                        echo $M2_Scaling1."<br>";
                        $M2_DATA = bin2hex(fread($this->M2_Handle,4));
			$M2_Scaling2 = $this->hexToFloat($this->EndianConverter($M2_DATA));
                        echo $M2_Scaling2."<br><br>";

			}
                        $M2_DATA = bin2hex(fread($this->M2_Handle,4));
			$M2_PivotPoint = $this->hexToFloat($this->EndianConverter($M2_DATA));

                        $M2_DATA = bin2hex(fread($this->M2_Handle,4));
			$M2_PivotPoint1 = $this->hexToFloat($this->EndianConverter($M2_DATA));

                        $M2_DATA = bin2hex(fread($this->M2_Handle,4));
			$M2_PivotPoint2 = $this->hexToFloat($this->EndianConverter($M2_DATA));
                    endfor;

                    //if ($this->debug):	$this->p_array($this->M2_Info);  endif;
		}

		//===================================
		//M2 Speed Animation
		//===================================	
		function M2_Animation_Speed_Change(){

                    $OFFVALUE = "0x".$this->EndianConverter($this->STRUCT_INFO["M2_ofsAnimations"]);

                    fseek($this->M2_Handle,0x0+$OFFVALUE,SEEK_SET); // Modo patata de hacer las cosas, Bug en PHP?
                    
                    for($x = 0; $x <  $this->STRUCT_INFO["M2_nAnimations"];$x++):
                    
                        $M2_DATA = bin2hex(fread($this->M2_Handle,2));
			$M2_AnimationID = $this->EndianConverter($M2_DATA);

                        $M2_DATA = bin2hex(fread($this->M2_Handle,2));
			$M2_SubAnimationID = $this->EndianConverter($M2_DATA);

                        $M2_DATA = bin2hex(fread($this->M2_Handle,4));
			$M2_Length = $this->EndianConverter($M2_DATA);
                        echo hexdec($M2_Length)."<br>";
                        fseek($this->M2_Handle,-4,SEEK_CUR);
                        fwrite($this->M2_Handle,pack("V",111111),4);
                        fseek($this->M2_Handle,-4,SEEK_CUR);
                        $M2_DATA = bin2hex(fread($this->M2_Handle,4));
			$M2_Length = $this->EndianConverter($M2_DATA);
                        echo hexdec($M2_Length)."<br>";


                        fwrite($this->M2_Handle,pack("f",0),4);
                        fseek($this->M2_Handle,-4,SEEK_CUR);
                        $M2_DATA = bin2hex(fread($this->M2_Handle,4));
			$M2_SpeedAnimation = $this->hexToFloat($this->EndianConverter($M2_DATA));
                        echo $M2_SpeedAnimation."<br>";
                        fseek($this->M2_Handle,0x24,SEEK_CUR);
                    endfor;

            //if ($this->debug):	$this->p_array($this->M2_Info);  endif;
		}
}

$MyM2 = new M2();
$MyM2->M2_Open("HumanMaleTry.M2");
$MyM2->M2_HeaderInfo();
$MyM2->M2_GetInfo();
//$MyM2->M2_Animation_Speed_Change();
$MyM2->M2_BigHeadMode();

?>