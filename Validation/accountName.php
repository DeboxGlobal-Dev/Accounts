
<?php 

$IsCorrect=TRUE;

$ReturnValue = CheckNumberOnly($GroupId,"Group Id",1,$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;

$ReturnValue = CheckNumberOnly($OpeningBal,"Opening Balance",1,$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;

$ReturnValue = CheckNumberOnly($AddedBy,"Added By",1,$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;

$ReturnValue = CheckNumberOnly($Id,"Id",0,$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;

$ReturnValue = OnlyBoolean($Status,"Status",$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;

$ReturnValue = CheckEmptyData($AccountName,"Account Name",$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;

?>