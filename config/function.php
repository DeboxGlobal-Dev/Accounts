<?php 

ob_start();
ini_set('post_max_size', '10M');
ini_set('upload_max_filesize', '10M');
ini_set("display_errors",0);
ini_set("log_errors",0);

function MISuploadlogger($errorlog)
{
	$newfile = 	'errorlog/Debuglog_'.date('dmy').'.txt';

	//rename('errorlog/miserrorlog.txt',$newfile);
   
	if(!file_exists($newfile))
	{
	  file_put_contents($newfile,'');
	}
	$logfile=fopen($newfile,'a');

	
	$ip = $_SERVER['REMOTE_ADDR'];
	date_default_timezone_set('Asia/Kolkata');
	$time = date('d-m-Y h:i:s A',time());
	//$contents = file_get_contents('errorlog/errorlog.txt');
	$contents = "$ip\t$time\t$errorlog\r";
	fwrite($logfile,$contents);
	//file_put_contents('errorlog/errorlog.txt',$contents);
}

function GetSequenceNoOftheDay()
{
	$sequenceNo=1;
	$returnval=0;
	MISuploadlogger("Inside Function...");
	if(file_exists('SequenceNo.txt'))
	{
		$contents = file_get_contents('SequenceNo.txt');
		MISuploadlogger("Content of function at opening".$contents);
		$libData = explode("^",$contents);
		
		$sequenceNo=intval($libData[1]);
		
			$sequenceNo++;

			$returnval=$sequenceNo;
	
	}
	$contents=date("dmY")."^".strval($sequenceNo);
	MISuploadlogger("Content of function".$contents);
	file_put_contents ('SequenceNo.txt',$contents);
	
	return $returnval;

}

function getAccountName($accountId){

$DataEntryQuery = "SELECT \"ACCOUNT_NAME\" FROM accounts.\"accountMaster\" WHERE \"ACCOUNT_ID\"='_account'";

$DataEntryQuery =str_replace('_account',$accountId,$DataEntryQuery);

$getDatafromData = pg_query(OpenCon(), $DataEntryQuery);

if(pg_num_rows($getDatafromData) > 0) {

$resultData = pg_fetch_assoc($getDatafromData);

return $resultData['ACCOUNT_NAME'];	

}

return "-";

}

function getGroupId($accountId){

$DataEntryQuery = "SELECT \"GROUP_ID\" FROM accounts.\"accountMaster\" WHERE \"ACCOUNT_ID\"='_account'";

$DataEntryQuery =str_replace('_account',$accountId,$DataEntryQuery);

$getDatafromData = pg_query(OpenCon(), $DataEntryQuery);

if(pg_num_rows($getDatafromData) > 0) {

$resultData = pg_fetch_assoc($getDatafromData);

return $resultData['GROUP_ID'];	

}

return 0;

}

function getBalance($accountId){

$DataEntryQuery = "SELECT \"CURRENT_BALANCE\" FROM accounts.\"accountMaster\" WHERE \"ACCOUNT_ID\"='_account'";

$DataEntryQuery =str_replace('_account',$accountId,$DataEntryQuery);

$getDatafromData = pg_query(OpenCon(), $DataEntryQuery);

if(pg_num_rows($getDatafromData) > 0) {

$resultData = pg_fetch_assoc($getDatafromData);

return $resultData['CURRENT_BALANCE'];	

}
return 0;
}


 function updateBalance($accountId,$balance){

$obsUpdateQuery =" Update accounts.\"accountMaster\" set \"CURRENT_BALANCE\" ='_balance' where \"ACCOUNT_ID\" = '_account' ;";

$obsUpdateQuery = str_replace('_account',$accountId, $obsUpdateQuery);

$obsUpdateQuery = str_replace('_balance',$balance, $obsUpdateQuery);

$result = pg_query(OpenCon(),$obsUpdateQuery);

if(pg_affected_rows($result) > 0){

return true;

}
return false;
}

function getBalanceGroup($accountId){

$GroupId = getGroupId($accountId);

if($GroupId != 0){

$GroupEntryQuery = "SELECT \"BALANCE\" FROM accounts.\"accountGroupMaster\" WHERE \"GROUP_ID\"='_gid'";

$GroupEntryQuery =str_replace('_gid',$GroupId,$GroupEntryQuery);

$getDatafromGroup = pg_query(OpenCon(), $GroupEntryQuery);

if(pg_num_rows($getDatafromGroup) > 0) {

$GroupData = pg_fetch_assoc($getDatafromGroup);

return $GroupData['BALANCE'];

}

}

return 0;

}


function updateBalanceGroup($accountId,$balance){

$GroupId = getGroupId($accountId);

if($GroupId != 0) {

$obsUpdateQuery =" Update accounts.\"accountGroupMaster\" set \"BALANCE\" ='_balance' where \"GROUP_ID\" = '_gid' ;";

$obsUpdateQuery = str_replace('_gid',$GroupId, $obsUpdateQuery);

$obsUpdateQuery = str_replace('_balance',$balance, $obsUpdateQuery);

$result = pg_query(OpenCon(),$obsUpdateQuery);

if(pg_affected_rows($result) > 0){

return true;

}

}

return false;

}

function IsAccountActive($accountId){

$DataEntryQuery = "SELECT \"ACCOUNT_ID\" FROM accounts.\"accountMaster\" WHERE \"STATUS\"=0 AND \"ACCOUNT_ID\"='_account'";

$DataEntryQuery =str_replace('_account',$accountId,$DataEntryQuery);

$getDatafromData = pg_query(OpenCon(), $DataEntryQuery);

if(pg_num_rows($getDatafromData) > 0) {

return true;

}

return false;

}

function IsNonAsciExists($ValueofField)
{
    $tmp = "";
    $tmp = preg_replace('/[[:^print:]]/','',$ValueofField); // should be aA

    $Exists=FALSE;
    if (strlen($ValueofField) > strlen($tmp))
    {
        $Exists =TRUE;
    }
    return $Exists;
}

function ValidateDate($date,$NameofField,$format, &$ErrorMessage){
    
    $Isvalid = TRUE;
	$ErrorMessage="";

    $d = DateTime::createFromFormat($format, $date);
    if($d && $d->format($format) === $date){
    	
    return TRUE;

    }else{

    	$Mesage = $NameofField." is InValid.";
		$ErrorMessage = !$Isvalid ? $ErrorMessage.="^". $Mesage: $Mesage;
		$Isvalid = FALSE;

    }

    return $Isvalid;

} 

function CheckNumberOnly($ValueofField,$NameofField,$MandatoryFlag, &$ErrorMessage)
{

	$Isvalid = TRUE;
	$ErrorMessage="";

	if($MandatoryFlag == 1){

		if (!is_numeric($ValueofField))
		{
			$Mesage = $NameofField." must be numeric.";
			$ErrorMessage = !$Isvalid ? $ErrorMessage.="^". $Mesage: $Mesage;
			$Isvalid = FALSE;
		} 
	}

	if($MandatoryFlag == 0){

		if (!is_numeric($ValueofField) && $ValueofField != "")
		{
			$Mesage = $NameofField." must be numeric.";
			$ErrorMessage = !$Isvalid ? $ErrorMessage.="^". $Mesage: $Mesage;
			$Isvalid = FALSE;
		} 
	}
	return $Isvalid;
}

function CheckCharacterOnly($ValueofField,$NameofField,$MandatoryFlag, &$ErrorMessage)
{

	$Isvalid = TRUE;
	$ErrorMessage="";

	if($MandatoryFlag == 1){


		if(!preg_match("/^[A-Z a-z']+$/",$ValueofField))
		{
			$Mesage = "Only Characters allowed in ".$NameofField;
			$ErrorMessage = !$Isvalid ? $ErrorMessage.="^". $Mesage: $Mesage;
			$Isvalid = FALSE;
		}

	}

	if($MandatoryFlag == 0){


		if(!preg_match("/^[A-Z a-z']+$/",$ValueofField) && $ValueofField != "")
		{
			$Mesage = "Only Characters allowed in ".$NameofField;
			$ErrorMessage = !$Isvalid ? $ErrorMessage.="^". $Mesage: $Mesage;
			$Isvalid = FALSE;
		}

	}


	return $Isvalid;
}

function CheckFixedCharacter($ValueofField,$NameofField,$Count,$MandatoryFlag, &$ErrorMessage)
{

	$Isvalid = TRUE;
	$ErrorMessage="";
	if($MandatoryFlag == 1){

		if(!preg_match("/^[A-Z']+$/",$ValueofField))
		{
			$Mesage = "Uppercase Character/s allowed in ".$NameofField;
			$ErrorMessage = !$Isvalid ? $ErrorMessage.="^". $Mesage: $Mesage;
			$Isvalid = FALSE;
		}

		if(strlen($ValueofField) != $Count)
		{
			$Mesage = "Only ".$Count." allowed Character/s in ".$NameofField;
			$ErrorMessage = !$Isvalid ? $ErrorMessage.="^". $Mesage: $Mesage;
			$Isvalid = FALSE;
		} 
	
	}

	if($MandatoryFlag == 0){

		if(!preg_match("/^[A-Z']+$/",$ValueofField) && $ValueofField != "")
		{
			$Mesage = "Uppercase Character/s allowed in ".$NameofField;
			$ErrorMessage = !$Isvalid ? $ErrorMessage.="^". $Mesage: $Mesage;
			$Isvalid = FALSE;
		}

		if(strlen($ValueofField) <= $Count && $ValueofField != "")
		{
			$Mesage = "Only ".$Count." allowed Character/s in ".$NameofField;
			$ErrorMessage = !$Isvalid ? $ErrorMessage.="^". $Mesage: $Mesage;
			$Isvalid = FALSE;
		} 
	
	}

	return $Isvalid;
}

function CheckEmptyData($ValueofField,$NameofField, &$ErrorMessage)
{

    $Isvalid = TRUE;
    $ErrorMessage="";
        if($ValueofField == "")
        {
            $Mesage = $NameofField." is Mandatory. ";
            $ErrorMessage = !$Isvalid ? $ErrorMessage.="^". $Mesage: $Mesage;
            $Isvalid = FALSE;
        } 
    return $Isvalid;
}

function OnlyBoolean($ValueofField,$NameofField, &$ErrorMessage)
{

    $Isvalid = TRUE;
    $ErrorMessage="";
        if($ValueofField != "0" && $ValueofField != "1")
        {
            $Mesage = "Only 1 and 0 allowed in ".$NameofField;
            $ErrorMessage = !$Isvalid ? $ErrorMessage.="^". $Mesage: $Mesage;
            $Isvalid = FALSE;
        } 
    return $Isvalid;
}



?>