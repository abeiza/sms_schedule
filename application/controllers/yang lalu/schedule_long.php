<?php if(!defined('BASEPATH'))exit('No direct script access allowed');

class Schedule extends CI_Controller{
	function __construct(){
		parent::__construct();
	}
	
	function index(){
		$this->new_inbox();
	}
	
	function new_inbox(){
		$n_inbox = $this->model_app->getWhereMy('inbox','Processed','');
		foreach($n_inbox->result() as $db){
			$data1['Processed'] = "proses";
			$update_flag = $this->model_app->getUpdateMy("inbox",$data1,"ID", $db->ID);
			
			$data2['object_id'] = $db->ID;
			$data2['ReceivingDateTime'] = $db->ReceivingDateTime;
			$data2['Text'] = $db->Text;
			$data2['SenderNumber'] = $db->SenderNumber;
			$data2['Coding'] = $db->Coding;
			$data2['UDH'] = $db->UDH;
			$data2['SMSCNumber'] = $db->SMSCNumber;
			$data2['Class'] = $db->Class;
			$data2['TextDecoded'] = $db->TextDecoded;
			$data2['RecipientID'] = $db->RecipientID;
			$data2['Processed'] = 'false';
			
			$insert_n_inbox = $this->model_app->getInsertSrv("new_inbox",$data2);
			
			$data3['Processed'] = "true";
			$update_flag_end = $this->model_app->getUpdateMy('inbox',$data3,'ID',$db->ID);
		}
		/*Note : tambahkan enum proses di table inbox field Processed */
		
	}
	
	function final_inbox(){
		$f_inbox = $this->model_app->getQueryMy('select * from new_inbox where Processed="false" order by UDH');
		if(!empty($f_inbox)){
			foreach($f_inbox->result() as $db){
				//echo $db->object_id."<br>";
				echo $no = substr($db->SenderNumber,0,3);
				echo "<br>";
				echo $text = substr($db->TextDecoded,0,6);
				echo "<br>";
				echo $udh = $db->UDH;
				echo "<br>";
				$cnt = substr($db->UDH,8,2);
				echo $num = intval(substr($db->UDH,10,2));
				echo "<br><br>";
				
				/*$date = $rs->Fields['ReceivingDateTime']->Value;
				$d = substr($date,0,2);
				$m = substr($date,3,2);
				$y = substr($date,6,4);
				
				$j = substr($date,11,2);
				$me = substr($date,14,2);
				$sec = substr($date,17,2);
				//echo $ID."<br>";
				$s = $y."-".$m."-".$d." ".$j.":".$me.":".$sec.".000";*/
				
				if($no === '+62'){
					if((is_numeric($text) and $num == 1) or $num >= 2){
						//$con2->Execute("UPDATE dbo.new_inbox SET Processed='t' where object_id=$ID");
						echo "aku sih yes";
						echo "<br>";
						echo "-------------------------------------------------------------------------------";
						echo "<br><br>";
						//pemecahan udh
						if(!$udh){
							//$insert_final = "insert into dbo.Final_Inbox (ReceiveDt, SenderNumber, FreqSMS, TextSMS, Processed, ProcessedDt) 
							//			   values 
							//			   ('".$ReceivingDateTime."','".$rs->Fields['SenderNumber']->Value."',1,'".$rs->Fields['TextDecoded']->Value."','true','".$ReceivingDateTime."')";
							//$con2->execute($insert_final);	
						}else{
							$data[] = $db->TextDecoded;
							$unique[] = substr($db->UDH,0,8);
							$count[] = $cnt;
							//$numb[] = substr($rs->Fields['UDH']->Value,10,2);
						}	
					}
				}
			}
		}else{
		
		}
	}
}

/*End of file schedule.php*/
/*Location:.application/controllers/schedule.php*/