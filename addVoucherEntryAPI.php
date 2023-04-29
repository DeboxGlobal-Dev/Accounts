<?php
include "inc.php";

header("Content-Type: application/json");
$parameterdata = file_get_contents('php://input');
$parameterdata = str_replace("null","\"\"",$parameterdata);
//MISuploadlogger($parameterdata);

// {
//  "VoucherNo": "",
//   "VoucherDate": "2023-03-02",
//   "Narration": "test",
//   "Type": "J",
//   "TotalAmount":"250.00",
//   "AddedBy": "125",
//   "ListOfTransaction": [
//     {
//       "Amount": "125.00",
//       "Type": "D",
//       "AccountCode":"SLB0025"
//     },
//     {
//       "Amount": "125.00",
//       "Type": "C",
//       "AccountCode":"SLB0024"
//     }
//   ]
// }

$dataToShare = json_decode($parameterdata);

$VoucherNo = $dataToShare->VoucherNo == ""?$dataToShare->VoucherNo:$Type."/".date('dmy')."/".GetSequenceNoOftheDay();
$VoucherDate = $dataToShare->VoucherDate." ".date('H:i:s',time());
$DateAddedWithTime = date('Y-m-d H:i:s');
$DateAdded = date('Y-m-d');
$Narration = $dataToShare->Narration;
$Type = $dataToShare->Type;
$AddedBy = $dataToShare->UserId;
$ListOfJson = json_encode($dataToShare->ListOfTransaction);

include "Validation/voucherEntry.php";


class clsListData
{
  public $Status;
  public $Message;
  public $Remark;

}

$listArray=array();
$Message = "";
$Status = 1;

try
{
 if($IsCorrect){

  $lastIdQuery = "SELECT \"Id\" FROM accounts.\"ledgerMaster\" ORDER BY \"Id\" DESC";
   $DataQuery = pg_query(OpenCon(), $lastIdQuery);

   $LastId = pg_fetch_assoc($DataQuery);
   $TablelastId = $LastId['Id']+1;

  foreach ($dataToShare->ListOfTransaction as $value) {

   if($value->Type == 'C'){
    $Credit = $value->Amount;
    $Debit = 0;
  }
  if($value->Type == 'D'){
    $Debit = $value->Amount;
    $Credit = 0;
  }
  
  $TotalCredit +=$Credit;
  $TotalDebit +=$Debit; 
  
}
if($TotalCredit == $TotalDebit){

  foreach ($dataToShare->ListOfTransaction as $value) {

    $Status *= 1;

   if($value->Type == 'C'){
    $Credit = $value->Amount;
    $Debit = 0;
    $CurrentBalance = getBalance($value->AccountCode);
    $UpdatedBalance = $CurrentBalance + $Credit;
  }
  if($value->Type == 'D'){
    $Debit = $value->Amount;
    $Credit = 0;
    $CurrentBalance = getBalance($value->AccountCode);
    $UpdatedBalance = $CurrentBalance - $Debit;

  }

  if(IsAccountActive($value->AccountCode)){

    $sql_name = '"Id","AccountId","VoucherNo","Narration","DateAdded","AddedBy","Debit","Credit","Type","Balance"';

    $sql_val = "'".$TablelastId."','".$value->AccountCode."','".$voucherNo."','".$Narration."','".$DateAddedWithTime."','".$AddedBy."','".$Debit."','".$Credit."','".$Type."','".$UpdatedBalance."'";

    $query .= 'Insert into accounts."ledgerMaster" ('.$sql_name.') Values ('.$sql_val.') ;';

    MISuploadlogger($query);

  }else{
    $Status *=0;
    $Message = "Account Not Available";
  }

  $TablelastId++;

}


if($VoucherNo!=''){

  $listSql = "SELECT \"VoucherNo\" FROM accounts.\"voucherEntry\" where true _vno";
  $listSql = str_replace("_vno", $VoucherNo!=''?" and \"VoucherNo\"='".$VoucherNo."' ":"",$listSql );

    // $listSql = str_replace("_gname",$GroupName!=''?" and UPPER(\"GROUP_NAME\") LIKE '%".$GroupName."%' ":"",$listSql );

  MISuploadlogger($listSql);	
  $DataQuery = pg_query(OpenCon(), $listSql);

  $Count = pg_num_rows($DataQuery);

  if($Count > 0){

    $updateDetailQuery =" Update accounts.\"voucherEntry\" set \"Note\" = '_note',\"JsonData\"='_jdata',\"Type\"='_type' where true _vno;";

    $updateDetailQuery = str_replace("_vno"," and \"VoucherNo\" = '".$VoucherNo."' ",$updateDetailQuery);
    $updateDetailQuery = str_replace("_type",$Type,$updateDetailQuery);
    $updateDetailQuery = str_replace("_jdata",$ListOfJson,$updateDetailQuery);
    $updateDetailQuery = str_replace("_note",$Narration,$updateDetailQuery);

    $result = pg_query(OpenCon(),$updateDetailQuery);
    $effectiveRow = pg_affected_rows($result);

    if($effectiveRow > 0){
      $Message = "Updated Successfully";
    }else{
      $Message = "Not Updated";
    }

  }

}else{

  if($Status == 1){

    $misinsert = pg_query(OpenCon(),$query);   

    $lastIdQuery = "SELECT \"VID\" FROM accounts.\"voucherEntry\" ORDER BY \"VID\" DESC";
    MISuploadlogger($lastIdQuery);
    $DataQuery = pg_query(OpenCon(), $lastIdQuery);

    $LastId = pg_fetch_assoc($DataQuery);
    $TablelastId = $LastId['VID']+1;

    $sql_name = '"VID","VoucherNo","DateAdded","JsonData","VoucherDate","AddedBy","Type","Note"';

    $sql_val = "'".$TablelastId."','".$voucherNo."','".$DateAdded."','".$ListOfJson."','".$VoucherDate."','".$AddedBy."','".$Type."','".$Note."'";

    $InsertQuery ='Insert into accounts."voucherEntry" ('.$sql_name.') Values ('.$sql_val.');';

    MISuploadlogger($InsertQuery);

    $result = pg_query(OpenCon(),$InsertQuery);
    $effectiveRow = pg_affected_rows($result);

    if($effectiveRow > 0){
     $Message = "Added Successfully";
   }else{
     $Message = "Not Added";
   }
 }
}

if($Status == 1){

  foreach ($dataToShare->ListOfTransaction as $value) {

     if($value->Type == 'C'){
    $Credit = $value->Amount;
    $CurrentBalance = getBalance($value->AccountCode);
    $GroupBalance = getBalanceGroup($value->AccountCode);
    $UpdatedBalance = $CurrentBalance + $Credit;
    $UpdatedGroupBalance = $GroupBalance + $Credit;
  }
  if($value->Type == 'D'){
    $Debit = $value->Amount;
    $CurrentBalance = getBalance($value->AccountCode);
    $GroupBalance = getBalanceGroup($value->AccountCode);
    $UpdatedBalance = $CurrentBalance - $Debit;
    $UpdatedGroupBalance = $GroupBalance - $Debit;

  }

  updateBalance($value->AccountCode,$UpdatedBalance);

  updateBalanceGroup($value->AccountCode,$UpdatedGroupBalance);

  }

}

}else{

  $Message = "Invalid Entry";
  
}

}

$objListData = new clsListData();
$objListData->Status = "0";
$objListData->Message= $Message;
$objListData->Remark= $ValidationJsonString;


echo json_encode($objListData,JSON_PRETTY_PRINT);

}

catch(Exception $e)
{
	echo json_encode(['Status'=>'-1','Message'=>'Failed','Remark'=>''],JSON_PRETTY_PRINT);
}

?>