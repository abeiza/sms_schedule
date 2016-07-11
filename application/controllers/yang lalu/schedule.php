<?php if(!defined('BASEPATH'))exit('No direct script access allowed');

class Schedule extends CI_Controller{
	function __construct(){
		parent::__construct();
	}
	
	function index(){
		$this->new_inbox();
	}
	
	function new_inbox(){
		$n_inbox = $this->model_app->getWhereMy('inbox','Processed','false');
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
		//$unique[] = null;
		//$TextDecoded[] = null;
		//$FreqSMS[] = null;
		$f_inbox = $this->model_app->getQuerySrv("select * from new_inbox where Processed='false' order by UDH");
		if(!empty($f_inbox)){
			foreach($f_inbox->result() as $db){
			$data_new_inbox['Processed'] = "proses";
			$this->model_app->getUpdateSrv('new_inbox',$data_new_inbox,'object_id',$db->object_id);
			
			$udh = $db->UDH;
			$no = substr($db->SenderNumber,0,3);
			$text = substr($db->TextDecoded,0,6);
			$num = intval(substr($db->UDH,10,2));
			$cnt = substr($db->UDH,8,2);
			//filter UDH ADA ATAU TIDAK
			echo $data5['ReceiveDt'] = $db->ReceivingDateTime;
			ECHO "<BR>";
			echo $data5['SenderNumber'] = $db->SenderNumber;
			ECHO "<BR>";
			echo $data5['FreqSMS'] = 1;
			ECHO "<BR>";
			echo $data5['TextSMS'] = $db->TextDecoded;
			ECHO "<BR>";
			echo $data5['Processed'] = "false";
			ECHO "<BR>";
			echo $data5['ProcessedDt'] = date("Y-m-d H:i:s");
			ECHO "<BR>";
				if(!$udh or $udh === NULL or $udh === ""){
				//UDH TIDAK TERSEDIA
					if($no === '+62'){
					//FILTER NO SENDER BENAR
						if(is_numeric($text)){
						//FILTER 6 TEXT AWAL NUMERIC
							
							/*$y = '20'.substr($text,4,2);
							$m = substr($text,2,2);
							$d = substr($text,0,2);
							$date = strtotime($y.$m.$d);
							$from = date("Ymd",$date);
							$c = abs(strtotime($from) - strtotime(date("Ymd")));
							$range = intval($c/86400);
							
							if($range <= 2){*/
								echo "INPUT UDH KOSONG TAPI LOLOS SELEKSI<BR>";
								echo "<br>-----------------------------------------------<br><br>";
								echo $data5['ReceiveDt'] = $db->ReceivingDateTime;
								echo $data5['SenderNumber'] = $db->SenderNumber;
								echo $data5['FreqSMS'] = 1;
								echo $data5['TextSMS'] = $db->TextDecoded;
								echo $data5['Processed'] = "loading";
								echo $data5['ProcessedDt'] = date("Y-m-d H:i:s");
							
								$insert_final = $this->model_app->getInsertSrv("Final_Inbox",$data5);
								
								$data_new_inbox_f['Processed'] = "true";
								$this->model_app->getUpdateSrv('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);

							/*}else{
								echo "INPUT UDH KOSONG GAK MASUK H-2<br>";
								echo "<br>-----------------------------------------------<br><br>";
							}*/
						}else{
							echo "INPUT UDH KOSONG GAK SESUAI FORMAT 6 TEXT AWAL<BR>";
							echo "<br>-----------------------------------------------<br><br>";
							$data_new_inbox_f['Processed'] = "true";
							$this->model_app->getUpdateSrv('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);

						}
					}else{
						echo "INPUT UDH KOSONG NO OPERATOR NEH<BR>";
						echo "<br>-----------------------------------------------<br><br>";
						$data_new_inbox_f['Processed'] = "true";
						$this->model_app->getUpdateSrv('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);
					}
				}else{
					//UDH TERSEDIA
					//echo $text.'<br>';
							
					if($no === '+62'){
						if(is_numeric($text) or $num >= 2){
							//echo substr($db->UDH,0,8).'<br>';
							$TextDecoded[] = $db->TextDecoded;
							$ReceivingDateTime[] = $db->ReceivingDateTime;
							$SenderNumber[]	= $db->SenderNumber;
							$Processed[] = "loading";
							$ProcessedDt[]	= date("Y-m-d H:i:s");
							//echo substr($db->UDH,0,8).'<br>';
							$unique[] = substr($db->UDH,0,8).$cnt;
							$FreqSMS[] = $cnt;
							$numb[] = intval(substr($db->UDH,10,2));
							$data_new_inbox_f['Processed'] = "true";
							$this->model_app->getUpdateSrv('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);
						}else{
							echo "INPUT UDH ADA GAK SESUAI FORMAT 6 TEXT AWAL<BR>";
							echo "<br>-----------------------------------------------<br><br>";
							$data_new_inbox_f['Processed'] = "true";
							$this->model_app->getUpdateSrv('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);

						}
					}else{
						echo "INPUT UDH ADA NO OPERATOR NEH<BR>";
						echo "<br>-----------------------------------------------<br><br>";
						$data_new_inbox_f['Processed'] = "true";
						$this->model_app->getUpdateSrv('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);

					}
				}
			}

			$non_duplicate = array_unique($unique);
	
			foreach($non_duplicate as $key => $value){
						//echo '<br/> array ke '.$key.' - '.$value.' votes'; 
						$data1 = "";
						for($i = 0; $i < count($unique); $i++){
							//echo "<br> array ke".$i." ".$unique[$i]."<br><br>";
							if($unique[$i] === $value){
								//echo "<br>".$unique[$i].$count[$i],$numb[$i]."=".$value."<br>";
								$data1 .= $TextDecoded[$i];
								$data2 = $ReceivingDateTime[$i];
								$data3 = $SenderNumber[$i];
								$data4 = $Processed[$i];
								$data5 = $ProcessedDt[$i];
								$dc = $FreqSMS[$i];
								//echo "-".$data1;
								//echo "<br>-----------------------------------------------";
							}
						}
						if(is_numeric(substr($data1,0,6))){
							echo "INPUT UDH TERSEDIA<BR>";
							echo "<br>++++++++++++++++++++++++++++++++++++".$dc."<br>";
							echo "-".$data1."<br>";
							echo "-".$data2."<br>";
							echo "-".$data3."<br>";
							echo "-".$data4."<br>";
							echo "-".$data5."<br>";
							echo "-".$dc."<br>";
							echo "<br> valid";
							$text = substr($data1,0,6);
							if(is_numeric($text)){
								//FILTER 6 TEXT AWAL NUMERIC
									
									/*$y = '20'.substr($text,4,2);
									$m = substr($text,2,2);
									$d = substr($text,0,2);
									$date = strtotime($y.$m.$d);
									$from = date("Ymd",$date);
									$c = abs(strtotime($from) - strtotime(date("Ymd")));
									$range = intval($c/86400);
									
									if($range <= 2){*/
										echo "INPUT UDH TERSEDIA LOLOS SELEKSI YEEE<BR>";
										echo "<br>-----------------------------------------------<br><br>";
										$data6['ReceiveDt'] = $data2;
										$data6['SenderNumber'] = $data3;
										$data6['FreqSMS'] = $dc;
										$data6['TextSMS'] = $data1;
										$data6['Processed'] = $data4;
										$data6['ProcessedDt'] = $data5;
										
										$insert_final = $this->model_app->getInsertSrv("Final_Inbox",$data6);
										$data_new_inbox_f['Processed'] = "true";
										$this->model_app->getUpdateSrv('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);

									/*}else{
										echo "GAK LOLOS SELEKSI H-2<br>";
										echo "<br>-----------------------------------------------<br><br>";
									}*/
							}else{
								
								echo "GAK LOLOS 6 DIGIT PERTAMA BUKAN NUMERIC<br>";
								echo "<br>-----------------------------------------------<br><br>";
								$data_new_inbox_f['Processed'] = "true";
								$this->model_app->getUpdateSrv('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);
							}
						}else{
							//echo "-".$data1;
							//echo "<br> gak valid";
							//echo "<br>-----------------------------------------------";
						}
			}			
		}else{
			echo "Empty Data";
		}
	}
	
	function filter_full_format(){
		$query = $this->model_app->getQuerySrv("select ObjectID, TextSMS from Final_Inbox WHERE Processed='loading'");
		foreach($query->result() as $db){
			echo $data = substr($db->TextSMS,15);
			echo '<br>';
			echo $outlet = substr($db->TextSMS,7,8);
			echo '<br>';
			//$data = '#29 1#34 1#85 2#89 1#92 1#55 1#43 1#114 1#17 1#21 3#22 2#23 1#998 20#999 16';
			$list = preg_split("/(?<=\w)\b\s*/",$data);
			foreach($list as $key => $value){
				if(substr($value,0,1) === '#'){
					echo $product[] = substr($value,1);
					echo '=id product<br>';
				}else if($value === ''){
					echo 'bukan apa apa<br>';
				}else if(is_numeric($value)){
					echo $qty[] = $value;
					echo '=qty product<br>';
				}
			}
			echo "<br>--------------------------------------------------------------------------------<br>";
			
			echo 'jumlah array product : '.count($product).'<br>';
			echo 'jumlah array qty : ' .count($qty).'<br>';
			
			if(count($product) === count($qty)){
				for($i=0;$i<count($product);$i++){
					echo str_pad($product[$i], 4, '0', STR_PAD_LEFT).'<br>';
					$query_product = $this->model_app->getQuerySrv("SELECT ID_Product FROM Table_PRODUCT WHERE ID_Product='".str_pad($product[$i], 4, '0', STR_PAD_LEFT)."'");
					if($query_product->num_rows() > 0){
						$product_status[] = 1;
					}else{
						echo "CEK LAGI DEH<br>";
					}
				}
				$count_stts = array_count_values($product_status);
				echo 'jumlahnya : '.$count_stts['1'];
				$query_outlet = $this->model_app->getQuerySrv("SELECT ID_OUTLET FROM Table_OUTLET WHERE ID_OUTLET='".$outlet."'");
				
				if ($count_stts['1'] == count($product) and $query_outlet->num_rows() > 0){
					$data_f['Processed'] = 'valid';
					$query_update = $this->model_app->getUpdateSrv('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
					echo "SUKSES SIMPAN VALID";
				}else{
					$data_f['Processed'] = 'invalid';
					$query_update = $this->model_app->getUpdateSrv('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
					echo "gagal";
				}
				echo "<br>--------------------------------------------------------------------------------<br>";
				echo "<br>--------------------------------------------------------------------------------<br>";
			}else{
				$data_f['Processed'] = 'invalid';
				$query_update = $this->model_app->getUpdateSrv('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
				echo "PAYAH";
				echo "<br>--------------------------------------------------------------------------------<br>";
				echo "<br>--------------------------------------------------------------------------------<br>";
			}
			unset($product_status);
			unset($product);
			unset($qty);
		}
	}
	
	function new_header_detail(){
		$query_new_header = $this->model_app->getQuerySrv("SELECT TOP 5 * FROM Final_Inbox WHERE Processed='valid'");
		foreach($query_new_header->result() as $db){
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
			echo $data_header['ID_OUTLET'] = substr($db->TextSMS,7,8);
			echo "<br>";
			$query_outlet = $this->model_app->getQuerySrv("SELECT * FROM Table_OUTLET WHERE ID_OUTLET='".substr($db->TextSMS,7,8)."'");
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
			$data = substr($db->TextSMS,15);
			$list = preg_split("/(?<=\w)\b\s*/",$data);
			foreach($list as $key => $value){
				if(substr($value,0,1) === '#'){
					echo $product[] = substr($value,1);
					echo '=id product<br>';
				}else if($value === ''){
					echo 'bukan apa apa<br>';
				}else if(is_numeric($value)){
					echo $qty[] = $value;
					echo '=qty product<br>';
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
					$query_product = $this->model_app->getQuerySrv("SELECT ID_Product, NAMA_PRODUCT, PRODUCT_KODE_PRINCIPLE, DESCRIPTION_PRINCIPLE FROM Table_PRODUCT WHERE ID_Product='".str_pad($product[$i], 4, '0', STR_PAD_LEFT)."'");
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
							echo $data_detail['Qty'] = $val;
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
 
/*End of file schedule.php*/
/*Location:.application/controllers/schedule.php*/