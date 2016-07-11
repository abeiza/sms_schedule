<?php
	if(!defined('BASEPATH'))exit('No Direct Script Access Allowed');
	
	class Sync extends CI_Controller{
		function __construct(){
			parent::__construct();
		}
		
		function sync_new_inbox(){
			$status_insert = 0;
			$status_update = 0;
			$status_fail_insert = 0;
			$status_fail_update = 0;
			$query5 = $this->model_app->getQueryMy("select * from new_inbox where status='unsync' ORDER BY object_id ASC");
			foreach($query5->result() as $db){
				$query_insert = $this->model_app->getQuerySrv("insert into new_inbox (object_id,ReceivingDateTime,Text,SenderNumber,Coding,UDH,SMSCNumber,Class,TextDecoded,RecipientID,Processed,ReplySMS) VALUES 
				('".$db->object_id."','".$db->ReceivingDateTime."','".$db->Text."','".$db->SenderNumber."','".$db->Coding."','".$db->UDH."','".$db->SMSCNumber."','".$db->Class."','".strtoupper($db->TextDecoded)."','".$db->RecipientID."','".$db->Processed."','".$db->ReplySMS."')");
				$query_update = $this->model_app->getQueryMy("update new_inbox set status='sync' where object_id='".$db->object_id."'");
				
				if($query_insert){
					$status_insert++;
				}else{
					$status_fail_insert++;
				}
				
				if($query_update){
					$status_update++;
				}else{
					$status_fail_update++;
				}
			}
			$data['notif_insert_success'] = $status_insert;
			$data['notif_insert_fail_success'] = $status_fail_insert;
			$data['notif_update_success'] = $status_update;
			$data['notif_update_fail_success'] = $status_fail_update;
			$data['total'] = $query5->num_rows();
			$this->load->view('sync_new_inbox_view',$data);
		}
		
		function sync_final_inbox(){
			$status_insert = 0;
			$status_update = 0;
			$status_fail_insert = 0;
			$status_fail_update = 0;
			$query6 = $this->model_app->getQueryMy("select * from final_inbox where status='unsync' ORDER BY ObjectID ASC LIMIT 3000 ");
			foreach($query6->result() as $db){
				$query_insert = $this->model_app->getQuerySrv("insert into Final_Inbox (ObjectID, ReceiveDt,SenderNumber,ID_BA,FreqSMS,TextSMS,Processed,ProcessedDt,ReplySMS) VALUES 
				('".$db->ObjectID."','".$db->ReceiveDt."','".$db->SenderNumber."','".strtoupper($db->ID_BA)."','".$db->FreqSMS."','".strtoupper($db->TextSMS)."','".$db->Processed."','".$db->ProcessedDt."','".$db->ReplySMS."')");
				$query_update = $this->model_app->getQueryMy("update final_inbox set status='sync' where ObjectID='".$db->ObjectID."'");
			
				if($query_insert){
					$status_insert++;
				}else{
					$status_fail_insert++;
				}
				
				if($query_update){
					$status_update++;
				}else{
					$status_fail_update++;
				}
			}
			$data['notif_insert_success'] = $status_insert;
			$data['notif_insert_fail_success'] = $status_fail_insert;
			$data['notif_update_success'] = $status_update;
			$data['notif_update_fail_success'] = $status_fail_update;
			$data['total'] = $query6->num_rows();
			$this->load->view('sync_final_inbox_view',$data);
		}
		
		function graphics_count(){
			$this->load->view('graphics_view');
		}
		
		function sync_hd(){
			$this->load->view('sync_new_header_detail');
			$this->new_header_detail();
		}
		
		function new_header_detail(){
			$query_new_header = $this->model_app->getQuerySrv("SELECT TOP 200 * FROM Final_Inbox WHERE Processed='valid'");
			foreach($query_new_header->result() as $db){
				$crash_position = strpos($db->TextSMS, '#', 1);
				$first_data = substr($db->TextSMS,0,$crash_position);
				$space_format1 = strpos($first_data, ' ', 1)+1;
				$second_data = substr($db->TextSMS,7,14);
				$space_format2 = strpos($second_data, ' ', 1)+1;
				
				$cek = substr_count($first_data,' ');
				
				if($cek === 1){
					echo $data_header['RefObjectID'] = $db->ObjectID;
					echo "<br>";
					echo $data_header['ReceiveDt'] = $db->ReceiveDt;
					echo "<br>";
					echo $data_header['ProcessedDt'] = date('Y-m-d H:i:s');
					echo "<br>";
					echo $TransDt = substr($db->TextSMS,0,6);
					echo "<br>";
					$y = '20'.substr($TransDt,4,2);
					$m = substr($TransDt,2,2);
					$d = substr($TransDt,0,2);
					$date = strtotime($y.$m.$d);
					$from = date('Y-m-d H:i:s',$date);

					echo $data_header['TransDt'] = $from;
					echo "<br>";
					echo $data_header['SenderNumber'] = $db->SenderNumber;
					echo "<br>";
					//echo $data_header['ID_OUTLET'] = substr($db->TextSMS,7,8);
					if(strpos(substr($db->TextSMS,7,8),'#',1) == 0){
						echo $data_header['ID_OUTLET'] = substr($db->TextSMS,7,8);
					}else{
						echo $data_header['ID_OUTLET'] = substr($db->TextSMS,7,6);
					}
					echo "<br>";
					$query_outlet = $this->model_app->getQuerySrv("SELECT * FROM Ms_OUTLET WHERE ID_OUTLET='".$data_header['ID_OUTLET']."'");
					foreach($query_outlet->result() as $dbs){
						echo $data_header['NAMA_OUTLET'] = $dbs->NAMA_OUTLET; 
						echo "<br>";
						echo $data_header['ID_BA'] = $dbs->ID_BA;
					}
					echo "<br>----------------------------Batas Header----------------------------<br>";
					//insert data ke dalam NewHeader
					$insert_new_header = $this->model_app->getInsertSrv("NewHeader",$data_header);
					
					//tarik data paling atas
					$query_header_id = $this->model_app->getQuerySrv("SELECT TOP 1 ObjectID FROM NewHeader ORDER BY ObjectID DESC");
					foreach($query_header_id->result() as $detail_header){
						echo $data_detail['ParentObjectID'] = $detail_header->ObjectID;
					}
					echo $db->TextSMS;
					echo '<br>';
					$data = substr($db->TextSMS,$crash_position);
					//$data = substr($db->TextSMS,15);
					//$list = preg_split("/(?<=\w)\b\s*/",$data);
					//foreach($list as $key => $value){
					//	if(substr($value,0,1) === '#'){
					//		echo $product[] = substr($value,1);
					//		echo '=id product<br>';
					//	}else if($value === ''){
					//		echo 'bukan apa apa<br>';
					//	}else if(is_numeric($value)){
					//		echo $qty[] = $value;
					//		echo '=qty product<br>';
					//	}
					//}
					
					echo $pieces = explode("#", $data);
					echo $jml = substr_count($data,"#");
					for($i=1;$i<=$jml;$i++){
						if(substr_count($pieces[$i]," ") == 1){
							echo $da = explode(" ", $pieces[$i]);
							echo "<br>";
							if($da[0] === "" or $da[0] === null or !preg_match('/^[0-9]{1,}$/',$da[0])){
								echo "fail";
								echo "<br>";
							//echo $fail_product[] = $da[0];
								echo "<br>";
							}else{
							echo $product[] = $da[0];
								echo "<br>";
							}
							
							if($da[1] === "" or $da[1] === null or !preg_match('/^[0-9]{1,}$/',$da[1])){
								echo "fail";
								echo "<br>";
							 //$fail_qty[] = $da[1];
								echo "<br>";
							}else{
							echo $qty[] = $da[1];
								echo "<br>";
							}
						}else{
							echo "fail";
							echo "<br>";
							//echo $fail[] = $pieces[$i];
							echo "<br>";
						}
					}
					
					echo $data_detail['TransDt'] = $from;
					echo '<br>';
					
					echo $data_detail['ID_OUTLET'] = substr($db->TextSMS,7,8);
					echo '<br>--------------------------------------------<br>';
					

					if(count($product) === count($qty)){
						for($i=0;$i<count($product);$i++){
							echo $data_detail['ID_PRODUCT'] = str_pad($product[$i], 4, '0', STR_PAD_LEFT);
							echo '<br>';
							$query_product = $this->model_app->getQuerySrv("SELECT ID_Product, NAMA_PRODUCT, PRODUCT_KODE_PRINCIPLE, DESCRIPTION_PRINCIPLE FROM Ms_PRODUCT WHERE ID_Product='".str_pad($product[$i], 4, '0', STR_PAD_LEFT)."'");
							foreach($query_product->result() as $detail_product){
								echo $data_detail['NAMA_PRODUCT'] = $detail_product->NAMA_PRODUCT;
								echo '<br>';
								echo $data_detail['PRODUCT_KODE_PRINCIPLE'] = $detail_product->PRODUCT_KODE_PRINCIPLE;
								echo '<br>';
								echo $data_detail['DESCRIPTION_PRINCIPLE'] = $detail_product->DESCRIPTION_PRINCIPLE;
								echo '<br>';
								$val = str_replace(".", "", $qty[$i]);
								//preg_match_all('/\d+/',$qty[$i],$match);
								//foreach($match as $key => $val){
									echo $data_detail['Qty'] = trim($val);
								//}
								echo '<br>';
								
								$insert_new_detail = $this->model_app->getInsertSrv("NewDetail",$data_detail);
							}
							echo "<br>";
							echo "<br>";
							echo "<br>----------------------------------Batas Detail------------------------------------------------<br>";
						
							/*if($query_product->num_rows() > 0){
								$product_status[] = 1;
							}else{
								echo "CEK LAGI DEH<br>";
							}*/
						}
					unset($product);
					unset($qty);
					$data_hd['Processed'] = 'true';
					$query_update_hd = $this->model_app->getUpdateSrv('Final_Inbox',$data_hd,'ObjectID',$db->ObjectID);
					echo "<br>SUKSES MASUK HEADER DAN DETAIL NEW<BR>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
					}
				}else if($cek === 2){
					
					//echo $ba = substr($db->TextSMS,7,5);
					echo $data_header['RefObjectID'] = $db->ObjectID;
					echo "<br>";
					echo $data_header['ReceiveDt'] = $db->ReceiveDt;
					echo "<br>";
					echo $data_header['ProcessedDt'] = date('Y-m-d H:i:s');
					echo "<br>";
					echo $TransDt = substr($db->TextSMS,0,6);
					echo "<br>";
					$y = '20'.substr($TransDt,4,2);
					$m = substr($TransDt,2,2);
					$d = substr($TransDt,0,2);
					$date = strtotime($y.$m.$d);
					$from = date('Y-m-d H:i:s',$date);

					echo $data_header['TransDt'] = $from;
					echo "<br>";
					echo $data_header['SenderNumber'] = $db->SenderNumber;
					echo "<br>";
					//echo $data_header['ID_OUTLET'] = substr($db->TextSMS,13,8);
					if(strpos(substr($db->TextSMS,13,8),'#',1) == 0){
						echo $data_header['ID_OUTLET'] = substr($db->TextSMS,13,8);
					}else{
						echo $data_header['ID_OUTLET'] = substr($db->TextSMS,13,6);
					}
					echo "<br>";
					echo $data_header['ID_BA'] = substr($db->TextSMS,7,5);
					
					if($data_header['ID_BA'] == '' or $data_header['ID_BA'] == NULL){
						echo $data_header['NAMA_BA'] = '';
						echo $data_header['ID_TL'] = '';
						echo $data_header['NAMA_TL'] = '';
						echo $data_header['ID_KBA'] = '';
						echo $data_header['NAMA_KBA'] = '';
					}else{
						$cari_asalusul_ba = $this->model_app->getQuerySrv("Select NAMA_BA, ID_TL from Ms_BA WHERE ID_BA='".$data_header['ID_BA']."'");
						foreach($cari_asalusul_ba->result() as $ms_ba){
							echo $data_header['NAMA_BA'] = $ms_ba->NAMA_BA;
							echo $data_header['ID_TL'] = $ms_ba->ID_TL;
							$cari_asalusul_tl = $this->model_app->getQuerySrv("Select NAMA_TL, ID_KBA from Ms_TL WHERE ID_TL='".$data_header['ID_TL']."'");
							if($cari_asalusul_tl->num_rows() == 0){
								echo $data_header['NAMA_TL'] = '';
								echo $data_header['ID_KBA'] = '';
							}else{
								foreach($cari_asalusul_tl->result() as $ms_tl){
									echo $data_header['NAMA_TL'] = $ms_tl->NAMA_TL;
									echo $data_header['ID_KBA'] = $ms_tl->ID_KBA;
									$cari_asalusul_kba = $this->model_app->getQuerySrv("Select NAMA_KBA from Ms_KBA WHERE ID_KBA='".$data_header['ID_KBA']."'");
									if($cari_asalusul_kba->num_rows() == 0){
										echo $data_header['NAMA_KBA'] = '';
									}else{
										foreach($cari_asalusul_kba->result() as $ms_kba){
											echo $data_header['NAMA_KBA'] = $ms_kba->NAMA_KBA;
										}
									}
								}
							}
						}
					}
					
					echo "<br>";
					$query_outlet = $this->model_app->getQuerySrv("SELECT * FROM Ms_OUTLET WHERE ID_OUTLET='".$data_header['ID_OUTLET']."'");
					foreach($query_outlet->result() as $dbs){
						echo $data_header['NAMA_OUTLET'] = $dbs->NAMA_OUTLET; 
						//echo "<br>";
						//echo $data_header['ID_BA'] = $dbs->ID_BA;
					}
					echo "<br>----------------------------Batas Header----------------------------<br>";
					//insert data ke dalam NewHeader
					$insert_new_header = $this->model_app->getInsertSrv("NewHeader",$data_header);
					
					//tarik data paling atas
					$query_header_id = $this->model_app->getQuerySrv("SELECT TOP 1 ObjectID FROM NewHeader ORDER BY ObjectID DESC");
					foreach($query_header_id->result() as $detail_header){
						echo $data_detail['ParentObjectID'] = $detail_header->ObjectID;
					}
					echo $db->TextSMS;
					echo '<br>';
					 $data = substr($db->TextSMS,$crash_position);
					 
					 $pieces = explode("#", $data);
					 $jml = substr_count($data,"#");
					 for($i=1;$i<=$jml;$i++){
						if(substr_count($pieces[$i]," ") == 1){
							echo $da = explode(" ", $pieces[$i]);
							echo "<br>";
							if($da[0] === "" or $da[0] === null or !preg_match('/^[0-9]{1,}$/',$da[0])){
								echo "fail";
								echo "<br>";
							// $fail_product[] = $da[0];
								echo "<br>";
							}else{
							 echo $product[] = $da[0];
								echo "<br>";
							}
							
							if($da[1] === "" or $da[1] === null or !preg_match('/^[0-9]{1,}$/',$da[1])){
								echo "fail";
								echo "<br>";
							// $fail_qty[] = $da[1];
								echo "<br>";
							}else{
							  echo $qty[] = $da[1];
								echo "<br>";
							}
						}else{
							echo "fail";
							echo "<br>";
							// $fail[] = $pieces[$i];
							echo "<br>";
						}
					 }
					//$data = substr($db->TextSMS,21);
					//$list = preg_split("/(?<=\w)\b\s*/",$data);
					//foreach($list as $key => $value){
					//	if(substr($value,0,1) === '#'){
					//		echo $product[] = substr($value,1);
					//		echo '=id product<br>';
					//	}else if($value === ''){
					//		echo 'bukan apa apa<br>';
					//	}else if(is_numeric($value)){
					//		echo $qty[] = $value;
					//		echo '=qty product<br>';
					//	}
					//}
					
					
					echo $data_detail['TransDt'] = $from;
					echo '<br>';
					
					echo $data_detail['ID_OUTLET'] = substr($db->TextSMS,13,8);
					echo '<br>--------------------------------------------<br>';
					

					if(count($product) === count($qty)){
						for($i=0;$i<count($product);$i++){
							echo $data_detail['ID_PRODUCT'] = str_pad($product[$i], 4, '0', STR_PAD_LEFT);
							echo '<br>';
							$query_product = $this->model_app->getQuerySrv("SELECT ID_Product, NAMA_PRODUCT, PRODUCT_KODE_PRINCIPLE, DESCRIPTION_PRINCIPLE FROM Ms_PRODUCT WHERE ID_Product='".str_pad($product[$i], 4, '0', STR_PAD_LEFT)."'");
							foreach($query_product->result() as $detail_product){
								echo $data_detail['NAMA_PRODUCT'] = $detail_product->NAMA_PRODUCT;
								echo '<br>';
								echo $data_detail['PRODUCT_KODE_PRINCIPLE'] = $detail_product->PRODUCT_KODE_PRINCIPLE;
								echo '<br>';
								echo $data_detail['DESCRIPTION_PRINCIPLE'] = $detail_product->DESCRIPTION_PRINCIPLE;
								echo '<br>';
								$val = str_replace(".", "", $qty[$i]);
								//preg_match_all('/\d+/',$qty[$i],$match);
								//foreach($match as $key => $val){
									echo $data_detail['Qty'] = trim($val);
								//}
								echo '<br>';
								
								$insert_new_detail = $this->model_app->getInsertSrv("NewDetail",$data_detail);
							}
							echo "<br>";
							echo "<br>";
							echo "<br>----------------------------------Batas Detail------------------------------------------------<br>";
						
							/*if($query_product->num_rows() > 0){
								$product_status[] = 1;
							}else{
								echo "CEK LAGI DEH<br>";
							}*/
						}
					unset($product);
					unset($qty);
					$data_hd['Processed'] = 'true';
					$query_update_hd = $this->model_app->getUpdateSrv('Final_Inbox',$data_hd,'ObjectID',$db->ObjectID);
					echo "<br>SUKSES MASUK HEADER DAN DETAIL NEW<BR>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
					}
				}
			}
		}
	}
	
/*End of file*/
/*Location : */