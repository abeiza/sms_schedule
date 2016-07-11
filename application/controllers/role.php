<?php if(!defined('BASEPATH'))exit('No direct script access allowed');

	class Role extends CI_Controller{
		
		function index(){
			$exquery = $this->model_app->getQuerySrv("select * from Table_BA order by ID_BA");
			foreach($exquery->result() as $db){
				//$query2 = $this->db->query("select ObjectID from FinalHeader where ObjectID='".$db->ObjectID."'");
				//if($query2->num_rows() == 0){
				//	$insertHeader = $this->db->query("insert into FinalHeader VALUES ('".$db->ObjectID."','".$db->RefObjectID."','".$db->ReceiveDt."','".$db->ProcessedDt."','".$db->TransDt."','".$db->SenderNumber."','".$db->ID_OUTLET."','".$db->NAMA_OUTLET."','".$db->ID_BA."')");
					//$query3 = $this->db->query("select ParentObjectID from FinalDetail where ParentObjectID='".$db->ObjectID."' AND ID_PRODUCT='".$db->ID_PRODUCT."'");
					//if($query3->num_rows() == 0){
						//$query4 = $this->db->query("select * from View_Final_Detail where ObjectID='".$db->ObjectID."'");
						//foreach($query4->result() as $db2){
							$insertDetail = $this->model_app->getQueryMy("insert into table_ba values ('".$db->ID_BA."','".$db->NAMA_BA."','".$db->ID_TL."','".$db->ID_KBA."','".$db->ID_AREA."','".$db->ID_ASM."','".$db->ID_RSM."','".$db->STATUS."','".$db->CONTACT."')");
						//}
					//}
				//}
			}
		}
		
		function absen(){
			$query = $this->db->query("SELECT * FROM Final_Inbox WHERE [ReceiveDt] >= '2016-02-01' AND [ReceiveDt] <= '2016-03-24' order by [ReceiveDt] asc");
			foreach($query->result() as $data){
				$hari = substr($data->TextSMS,0,2);
				$bulan = substr($data->TextSMS,2,2);
				$tahun = substr($data->TextSMS,4,2);
				
				$absen['ReceiveDt'] = $data->ReceiveDt;
				$absen['tahun'] = $tahun;
				$absen['bulan'] = $bulan;
				$absen['hari'] = $hari;
				$absen['ID_BA'] = $data->ID_BA;
				$absen['Processed'] = $data->Processed;
				$dat = $data->ID_BA;
				if($dat != ''){
					$query2 = $this->db->query("select * from Ms_OUTLET where ID_BA='".$dat."'");
					if($query2->num_rows() > 0){
						foreach($query2->result() as $ot){
							$absen['ID_OUTLET'] = $ot->ID_OUTLET;
						}
					}else{
						$absen['ID_OUTLET'] = null;
					}
				}else{
					$absen['ID_OUTLET'] = null;
				}
				$absen['Freq_SMS'] = $data->FreqSMS;
				$absen['ReplySMS'] = $data->ReplySMS;
				$absen['TextSMS'] = $data->TextSMS;
				$this->model_app->getInsertSrv('BA_Freq_Absen',$absen);
			}
		}
		
		/*function data(){
			echo $data = "#50 2#6 2##5 ";
			echo "<br>";
			//echo $posisicrash = strpos($data,'#',1);
			$pieces = explode("#", $data);
			$jml = substr_count($data,"#");
			for($i=1;$i<=$jml;$i++){
				echo $pieces[$i];
				echo "<br>";
				if(substr_count($pieces[$i]," ") == 1){
					echo "success";
					echo "<br>";
					$da = explode(" ", $pieces[$i]);
					if($da[0] === "" or $da[0] === null){
						echo "gak ada";
					}else{
						echo "product : ".$product[] = $da[0]."<br>";
					}
					
					if($da[1] === "" or $da[1] === null){
						echo "gak ada";
					}else{
						echo "qty : ".$qty[] = $da[1]."<br>";
					}
					echo "<br>";
					
				}else{
					echo "fail";
					echo "<br>";
				}
			}
			
			if(count($product) === count($qty)){
				echo "seimbang";
				
			}else{
				echo "gak seimbang";
			}
		}
		
		function test_space(){
			$text = "100116 BGR00021#111 1#31 1#152 2#154 1#159 2#164 1#148 3#149 3#150 4#156 3#155 2#157 2#158 3#11 4#12 2#13 2#126 3#120 2#132 2#124 1#119 2#125 2#133 2#998";
			$t = substr($text,0,6);
			
			if(is_numeric($t)){
				if(!preg_match('/^[0-9]{1,}$/',$t)){
					echo $t;
					echo "<br/>";
					echo "gagal";
				}
				else{
					echo $t;
					echo "<br/>";
					
					echo "sukses";
				}
			}else{
				echo $t;
				echo "<br/>";
				echo "gagal";
			}
		}*/
	
		function copy(){
			$query1 = $this->model_app->getQuerySrv("select * from Ms_BA order by ID_BA");
			foreach($query1->result() as $ba){
				$query2 = $this->model_app->getQuerySrv("select ID_KBA from Ms_TL where ID_TL = '".$ba->ID_TL."'");
				foreach($query2->result() as $kba){
					$query3 = $this->model_app->getQuerySrv("select COVERAGE_KBA from Ms_KBA where ID_KBA = '".$kba->ID_KBA."'");
					foreach($query3->result() as $city){
						$update = $this->model_app->getQuerySrv("update Ms_Outlet set COVERAGE_OUTLET='".$city->COVERAGE_KBA."' where ID_BA='".$ba->ID_BA."'");
					}
				}
			}
		}
	}
