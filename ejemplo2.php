<?
include("adt.class.php");


$MyADT = new ADT();
$MyADT->ADT_Open("../../maps/Azeroth_32_50.adt");
$MyADT->ADT_HeaderInfo();
if(isset($_GET['chunk'])){
$MyADT->ADT_Make_Goatse($_GET['chunk']);
}else{
echo $MyADT->ADT_Show_Goatse();
}
?>