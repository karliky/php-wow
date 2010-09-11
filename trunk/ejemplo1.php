<?
include("adt.class.php");

$MiMapa = new ADT();
copy("../../maps/Azeroth_32_48.adt","../../maps/Azeroth_32_48 - copia.adt");

$MiMapa->ADT_Open("../../maps/Azeroth_32_48 - copia.adt");
$MiMapa->ADT_HeaderInfo();


?>