
<?php 

$IsCorrect=TRUE;

$ReturnValue = CheckFixedCharacter($Side,"Side",1,1,$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;

$ReturnValue = CheckFixedCharacter($ReportId,"Report Id",11,$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;

$ReturnValue = CheckNumberOnly($AddedBy,"Added By",1,$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;

$ReturnValue = CheckNumberOnly($Id,"Id",0,$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;

$ReturnValue = CheckEmptyData($GroupName,"Group Name",$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;

?>