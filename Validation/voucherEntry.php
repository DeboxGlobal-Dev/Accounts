
<?php 

$IsCorrect=TRUE;

$ReturnValue = CheckFixedCharacter($Type,"Type",1,1,$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;


$ReturnValue = CheckNumberOnly($AddedBy,"UserId",1,$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;


$ReturnValue = ValidateDate($dataToShare->VoucherDate,"Date","Y-m-d",$ValidationErrors);
$IsCorrect= !$ReturnValue?FALSE:$IsCorrect;  
$ValidationJsonString .= trim($ValidationErrors)==""? "" :$ValidationErrors."|" ;

?>