<?php

include "inc.php";

MISuploadlogger("*********************   Inside page Voucher Entry API *******************************");

define("INF","{INFO} -");
define ("ERR","{ERROR} -");
define ("DBG","{DEBUG} -");

header("Content-Type: application/json");
$parameterdata = file_get_contents('php://input');
$inputData = json_decode($parameterdata);

MISuploadlogger("Parameter Json \n".$parameterdata);

// {
//   "VoucherDate": "2023-03-02",
//   "Note": "test",
//   "Type": "JV",
//   "AddedBy": "125",
//   "ListOfTransaction": [
//     {
//       "Credit": "0",
//       "Credit": "125.00",
//       "AccountCode":"SLB0025"
//       "Narration": "Being Commission for Tinfc online processed"
//     },
//     {
//       "Credit": "125.00",
//       "Credit": "0",
//       "AccountCode":"SLB0024"
//       "Narration": "Being Commission for Tinfc online processed"
//     }
//   ]
// }
$effectedRow = 0;

$VoucherDate = $inputData->VoucherDate." ".date('H:i:s',time());
$DateAdded = date('Y-m-d',time());
$Note = $inputData->Note;
$Type = $inputData->Type;
$AddedBy = $inputData->UserId;
$ListOfJson = json_encode($inputData->ListOfTransaction);
// $voucherNo = $Type."-".date('dmy')."-".GetVoucherSequenceOftheDay();
$VoucherNo = $Type."-".date('dmy');

$journal_name = '"VoucherNo","DateAdded","JSON","VoucherDate","AddedBy","Type","Note"';

$journal_val = "'".$VoucherNo."','".$DateAdded."','".$ListOfJson."','".$VoucherDate."','".$AddedBy."','".$Type."','".$Note."'";

$journal = ' Insert into accounts."voucherEntry" ('.$journal_name.') Values ('.$journal_val.') ;';

MISuploadlogger("===Journal Insert====".$journal);

$journalinsert = pg_query(OpenCon(),$journal);

if($effectedRow > 0){
 echo "Saved Successfull"; 
}else{
  echo "Failed to Save";
}
?>