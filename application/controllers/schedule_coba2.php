<?php if(!defined('BASEPATH'))exit('No direct script access allowed');

class Schedule_coba2 extends CI_Controller{
	function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
	}
	
	function index(){
		$this->load->library('ci_phpgrid');
		$data['phpgrid'] = $this->ci_phpgrid->final_sms();
		
		//$this->load->view('show_grid',$data);
		//$this->new_inbox();
		//$this->final_inbox();
		//$this->filter_full_format();
		//$this->sync_new_inbox();
		//$this->sync_final_inbox();
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------------------------------//
	//---------------------------------------------------------VALIDASI UNTUK MYSQL FORMAT LAMA-------------------------------------------------------------//
	//------------------------------------------------------------------------------------------------------------------------------------------------------//
	
	
	//sudah diubah menjadi load mysql
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
			$data2['status'] = 'unsync';
			
			$insert_n_inbox = $this->model_app->getInsertMy("new_inbox",$data2);
			
			$data3['Processed'] = "true";
			$update_flag_end = $this->model_app->getUpdateMy('inbox',$data3,'ID',$db->ID);
		}
		/*Note : tambahkan enum proses di table inbox field Processed */
		
	}
	//final inbox sudah di load di mysql
	function final_inbox(){
		//$unique[] = null;
		//$TextDecoded[] = null;
		//$FreqSMS[] = null;
		$f_inbox = $this->model_app->getQueryMy("select * from new_inbox where Processed='false' order by UDH");
		if(!empty($f_inbox)){
			foreach($f_inbox->result() as $db){
			$data_new_inbox['Processed'] = "proses";
			$this->model_app->getUpdateMy('new_inbox',$data_new_inbox,'object_id',$db->object_id);
			
			$udh = $db->UDH;
			$no = substr($db->SenderNumber,0,3);
			$text = substr($db->TextDecoded,0,6);
			$num = intval(substr($db->UDH,10,2));
			$cnt = substr($db->UDH,8,2);
			//filter UDH ADA ATAU TIDAK
			$data5['ReceiveDt'] = $db->ReceivingDateTime;
			//ECHO "<BR>";
			$data5['SenderNumber'] = $db->SenderNumber;
			//ECHO "<BR>";
			$data5['FreqSMS'] = 1;
			//ECHO "<BR>";
			$data5['TextSMS'] = $db->TextDecoded;
			//ECHO "<BR>";
			$data5['Processed'] = "false";
			//ECHO "<BR>";
			$data5['ProcessedDt'] = date("Y-m-d H:i:s");
			//ECHO "<BR>";
				if(!$udh or $udh === NULL or $udh === ""){
				//UDH TIDAK TERSEDIA
					if($no === '+62'){
					//FILTER NO SENDER BENAR
						if(is_numeric($text)){
							if(!preg_match('/^[0-9]{1,}$/',$text)){
								//echo "INPUT UDH KOSONG GAK SESUAI FORMAT 6 TEXT AWAL ADA SPASI<BR>";
								//echo "<br>-----------------------------------------------<br><br>";
								$data_new_inbox_f['Processed'] = "true";
								$data_new_inbox_f['ReplySMS'] = "Tanggal transaksi tidak sesuai, periksa kembali SMS anda";
								$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);
								
								echo $dataoutbox2['DestinationNumber'] = $db->SenderNumber;
								echo $dataoutbox2['TextDecoded'] = 'Tanggal transaksi tidak sesuai, periksa kembali SMS anda';
								echo $dataoutbox2['CreatorID'] = 'Gammu';
								$this->model_app->getInsertMy('outbox',$dataoutbox2);
							}
							else{
									
									//FILTER 6 TEXT AWAL NUMERIC
									
									$y = '20'.substr($text,4,2);
									$m = substr($text,2,2);
									$d = substr($text,0,2);
									$date = strtotime($y.$m.$d);
									$from = date("Ymd",$date);
									$c = strtotime(date("Ymd")) - strtotime($from);
									$range = intval($c/86400);
									
									if($range <= 2 and $range > -1){
										//echo "INPUT UDH KOSONG TAPI LOLOS SELEKSI<BR>";
										//echo "<br>-----------------------------------------------<br><br>";
										$data5['RefObjectID'] = $db->object_id;
										$data5['ReceiveDt'] = $db->ReceivingDateTime;
										$data5['SenderNumber'] = $db->SenderNumber;
										$data5['FreqSMS'] = 1;
										$data5['TextSMS'] = $db->TextDecoded;
										$data5['Processed'] = "loading";
										$data5['ProcessedDt'] = date("Y-m-d H:i:s");
										$data5['ReplySMS'] = 'Terima kasih, sms anda akan segera kami proses';
										$data5['status'] = 'unsync';
										
										$insert_final = $this->model_app->getInsertMy("Final_Inbox",$data5);
										
										$data_new_inbox_f['Processed'] = "true";
										$data_new_inbox_f['ReplySMS'] = "";
										$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);
										
										//$dataoutbox1['DestinationNumber'] = $db->SenderNumber;
										//$dataoutbox1['TextDecoded'] = 'Terima kasih, sms anda akan segera kami proses';
										//$dataoutbox1['CreatorID'] = 'Gammu';
										//$this->model_app->getInsertMy('outbox',$dataoutbox1);
										//INSERT INTO outbox(DestinationNumber, TextDecoded, CreatorID) VALUES('".$get_sender_number."','Maaf, kode toko tidak terdaftar.', 'Gammu')

									}else{
									//	echo "INPUT UDH KOSONG GAK MASUK H-2<br>";
									//	echo "<br>-----------------------------------------------<br><br>";
										$data_new_inbox_f['ReplySMS'] = "Tanggal transaksi tidak sesuai, periksa kembali SMS anda";
										$data_new_inbox_f['Processed'] = "true";
										$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);
										
										$dataoutbox8['DestinationNumber'] = $db->SenderNumber;
										$dataoutbox8['TextDecoded'] = 'Tanggal transaksi tidak sesuai, periksa kembali SMS anda';
										$dataoutbox8['CreatorID'] = 'Gammu';
										$this->model_app->getInsertMy('outbox',$dataoutbox8);
									}
							}		
						}else{
							//echo "INPUT UDH KOSONG GAK SESUAI FORMAT 6 TEXT AWAL<BR>";
							//echo "<br>-----------------------------------------------<br><br>";
							$data_new_inbox_f['Processed'] = "true";
							//$data_new_inbox_f['ReplySMS'] = "Tanggal transaksi tidak sesuai, periksa kembali SMS anda";
							$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);
							
							//$dataoutbox2['DestinationNumber'] = $db->SenderNumber;
							//$dataoutbox2['TextDecoded'] = 'Tanggal transaksi tidak sesuai, periksa kembali SMS anda';
							//$dataoutbox2['CreatorID'] = 'Gammu';
							//$this->model_app->getInsertMy('outbox',$dataoutbox2);
						}
					}else{
						//echo "INPUT UDH KOSONG NO OPERATOR NEH<BR>";
						//echo "<br>-----------------------------------------------<br><br>";
						$data_new_inbox_f['Processed'] = "true";
						$data_new_inbox_f['ReplySMS'] = "No Operator";
						$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);
					}
				}else{
					//UDH TERSEDIA
					//echo $text.'<br>';
					//echo $num.'<br>';
					if($no === '+62'){
						if(is_numeric($text) or $num >= 2){
							//if(!preg_match('/^[0-9]{1,}$/',$text)){
							//	$data_new_inbox_f['Processed'] = "true";
							//	$data_new_inbox_f['ReplySMS'] = "Tanggal transaksi tidak sesuai, periksa kembali SMS anda";
							//	$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);
							//}
							//else{
								//echo substr($db->UDH,0,8).'<br>';
								$TextDecoded[] = $db->TextDecoded;
								$ReceivingDateTime[] = $db->ReceivingDateTime;
								$SenderNumber[]	= $db->SenderNumber;
								$object_id[]	= $db->object_id;
								$Processed[] = "loading";
								$ProcessedDt[]	= date("Y-m-d H:i:s");
								//echo substr($db->UDH,0,8).'<br>';
								$unique[] = substr($db->UDH,0,8).$cnt;
								$FreqSMS[] = $cnt;
								$numb[] = intval(substr($db->UDH,10,2));
								$data_new_inbox_f['Processed'] = "true";
								$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);
							//}
						}else{
							//echo "INPUT UDH ADA GAK SESUAI FORMAT 6 TEXT AWAL<BR>";
							//echo "<br>-----------------------------------------------<br><br>";
							$data_new_inbox_f['Processed'] = "true";
							//$data_new_inbox_f['ReplySMS'] = "Tanggal transaksi tidak sesuai, periksa kembali SMS anda";
							$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);
							
							//$dataoutbox3['DestinationNumber'] = $db->SenderNumber;
							//$dataoutbox3['TextDecoded'] = 'Tanggal transaksi tidak sesuai, periksa kembali SMS anda';
							//$dataoutbox3['CreatorID'] = 'Gammu';
							//$this->model_app->getInsertMy('outbox',$dataoutbox3);

						}
					}else{
						//echo "INPUT UDH ADA NO OPERATOR NEH<BR>";
						//echo "<br>-----------------------------------------------<br><br>";
						$data_new_inbox_f['Processed'] = "true";
						//$data_new_inbox_f['ReplySMS'] = "No Operator";
						$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$db->object_id);

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
					$objectID = $object_id[$i];
					$dc = $FreqSMS[$i];
					//echo "-".$data1;
					//echo "<br>-----------------------------------------------";
				}
			}
			//if(is_numeric(substr($data1,0,6))){
				//echo "INPUT UDH TERSEDIA<BR>";
				//echo "<br>++++++++++++++++++++++++++++++++++++".$dc."<br>";
				//echo "-".$data1."<br>";
				//echo "-".$data2."<br>";
				//echo "-".$data3."<br>";
				//echo "-".$data4."<br>";
				//echo "-".$data5."<br>";
				//echo "-".$dc."<br>";
				//echo "<br> valid";
				$text = substr($data1,0,6);
				if(is_numeric($text)){
					if(!preg_match('/^[0-9]{1,}$/',$text)){
						//echo "GAK LOLOS 6 DIGIT PERTAMA BUKAN NUMERIC<br>";
						//echo "<br>-----------------------------------------------<br><br>";
						$data_new_inbox_f['Processed'] = "true";
						$data_new_inbox_f['ReplySMS'] = "Tanggal transaksi tidak sesuai, periksa kembali SMS anda";
						$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$objectID);
						
						$dataoutbox7['DestinationNumber'] = $data3;
						$dataoutbox7['TextDecoded'] = 'Tanggal transaksi tidak sesuai, periksa kembali SMS anda';
						$dataoutbox7['CreatorID'] = 'Gammu';
						$this->model_app->getInsertMy('outbox',$dataoutbox7);
					}
					else{
						//FILTER 6 TEXT AWAL NUMERIC
							
						$y = '20'.substr($text,4,2);
						$m = substr($text,2,2);
						$d = substr($text,0,2);
						$date = strtotime($y.$m.$d);
						$from = date("Ymd",$date);
						$c = strtotime(date("Ymd")) - strtotime($from);
						$range = intval($c/86400);
						
						if($range <= 2 and $range > -1){
						//	echo "INPUT UDH TERSEDIA LOLOS SELEKSI YEEE<BR>";
						//	echo "<br>-----------------------------------------------<br><br>";
							$data6['RefObjectID'] = $objectID;
							$data6['ReceiveDt'] = $data2;
							$data6['SenderNumber'] = $data3;
							$data6['FreqSMS'] = $dc;
							$data6['TextSMS'] = $data1;
							$data6['Processed'] = $data4;
							$data6['ProcessedDt'] = $data5;
							$data6['ReplySMS'] = 'Terimakasih, sms anda akan segera kami proses [Multi SMS]';
							$data6['status'] = 'unsync';
							
							$insert_final = $this->model_app->getInsertMy("Final_Inbox",$data6);
							$data_new_inbox_f['Processed'] = "true";
							$data_new_inbox_f['ReplySMS'] = "";
							$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$objectID);
							
							//echo $dataoutbox5['DestinationNumber'] = $data3;
							//echo $dataoutbox5['TextDecoded'] = 'Terimakasih, sms anda berhasil dengan beberapa sms';
							//echo $dataoutbox5['CreatorID'] = 'Gammu';
							//$this->model_app->getInsertMy('outbox',$dataoutbox5);

						}else{
						//	echo "GAK LOLOS SELEKSI H-2<br>";
						//	echo "<br>-----------------------------------------------<br><br>";
							$data_new_inbox_f['ReplySMS'] = "Tanggal transaksi tidak sesuai, periksa kembali SMS anda";
							$data_new_inbox_f['Processed'] = "true";
							$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$objectID);
							
							$dataoutbox6['DestinationNumber'] = $data3;
							$dataoutbox6['TextDecoded'] = 'Tanggal transaksi tidak sesuai, periksa kembali SMS anda';
							$dataoutbox6['CreatorID'] = 'Gammu';
							$this->model_app->getInsertMy('outbox',$dataoutbox6);
						}
					}
				}else{
					//echo "GAK LOLOS 6 DIGIT PERTAMA BUKAN NUMERIC<br>";
					//echo "<br>-----------------------------------------------<br><br>";
					$data_new_inbox_f['Processed'] = "true";
					//$data_new_inbox_f['ReplySMS'] = "Tanggal transaksi tidak sesuai, periksa kembali SMS anda3";
					$this->model_app->getUpdateMy('new_inbox',$data_new_inbox_f,'object_id',$objectID);
					
					//$dataoutbox7['DestinationNumber'] = $data3;
					//$dataoutbox7['TextDecoded'] = 'Tanggal transaksi tidak sesuai, periksa kembali SMS anda';
					//$dataoutbox7['CreatorID'] = 'Gammu';
					//$this->model_app->getInsertMy('outbox',$dataoutbox7);
				}
			}			
		}else{
			echo "Empty Data";
		}
	}
	
	//SUDAH DI RUBAH NGELOAD DI MYSQL
	function filter_full_format(){
		$query = $this->model_app->getQueryMy("select ObjectID, RefObjectID, TextSMS, SenderNumber from Final_Inbox WHERE Processed='loading'");
		foreach($query->result() as $db){
			$crash_position = strpos($db->TextSMS, '#', 1);
			$first_data = substr($db->TextSMS,0,$crash_position);
			$space_format1 = strpos($first_data, ' ', 1)+1;
			$second_data = substr($db->TextSMS,7,14);
			$space_format2 = strpos($second_data, ' ', 1)+1;
			
			$cek = substr_count($first_data,' ');
			
			if($cek === 1){
			//format lama tanpa kode ba
					$data = substr($db->TextSMS,$crash_position);
					//echo '<br>';
					$outlet = substr($db->TextSMS,$space_format1,8);
					$sender = $db->SenderNumber;
					$query_ba1 = $this->model_app->getQueryMy("SELECT ID_BA FROM Table_BA WHERE CONTACT='".$sender."'");
					//echo '<br>';
					
					$pieces = explode("#", $data);
					$jml = substr_count($data,"#");
					for($i=1;$i<=$jml;$i++){
					if(substr_count($pieces[$i]," ") == 1){
						$da = explode(" ", $pieces[$i]);
						//echo "<br>";
						if($da[0] === "" or $da[0] === null or !preg_match('/^[0-9]{1,}$/',$da[0])){
						//	echo "fail";
						//	echo "<br>";
						 $fail_product[] = $da[0];
						//	echo "<br>";
						}else{
						 $product[] = $da[0];
						//	echo "<br>";
						}
						
						if($da[1] === "" or $da[1] === null or !preg_match('/^[0-9]{1,}$/',$da[1])){
						//	echo "fail";
						//	echo "<br>";
						 $fail_qty[] = $da[1];
						//	echo "<br>";
						}else{
						 $qty[] = $da[1];
						//	echo "<br>";
						}
					}else{
						//echo "fail";
						//echo "<br>";
						$fail[] = $pieces[$i];
						//echo "<br>";
					}
				 }
					
					//$data = '#29 1#34 1#85 2#89 1#92 1#55 1#43 1#114 1#17 1#21 3#22 2#23 1#998 20#999 16';
					//$list = preg_split("/(?<=\w)\b\s*/",$data);
					//foreach($list as $key => $value){
					//	if(substr($value,0,1) === '#'){
					//		$product[] = substr($value,1);
							//echo '=id product<br>';
					//	}else if($value === ''){
							//echo 'bukan apa apa<br>';
					//	}else if(is_numeric($value)){
					//		$qty[] = $value;
							//echo '=qty product<br>';
					//	}
					//}
					//echo "<br>--------------------------------------------------------------------------------<br>";
					
					//echo 'jumlah array product : '.count($product).'<br>';
					//echo 'jumlah array qty : ' .count($qty).'<br>';
					//echo 'jumlah fail : ' .count($fail).'<br>';
					
					if(count($product) === count($qty) and count($fail) === 0){
						for($i=0;$i<count($product);$i++){
							//echo str_pad($product[$i], 4, '0', STR_PAD_LEFT).'<br>';
							$query_product = $this->model_app->getQueryMy("SELECT ID_Product FROM Ms_PRODUCT WHERE ID_Product='".str_pad($product[$i], 4, '0', STR_PAD_LEFT)."'");
							if($query_product->num_rows() > 0){
								$product_status[] = 1;
							}else{
								$product_not[] = $product[$i];
							}
						}
						$count_stts = array_count_values($product_status);
						//echo 'jumlahnya : '.$count_stts['1'];
						$query_outlet = $this->model_app->getQueryMy("SELECT ID_OUTLET FROM Ms_OUTLET WHERE ID_OUTLET='".$outlet."'");
						
						if ($count_stts['1'] == count($product) and $query_outlet->num_rows() > 0){
							foreach($query_ba1->result() as $bad){
								$data_f['ID_BA'] = "";
								$data_f['ID_BA'] = $bad->ID_BA;
							}
							$data_f['Processed'] = 'invalid';
							$data_f['ReplySMS'] = 'Maaf, silahkan kirim lagi dengan format baru yaitu tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$query_update1 = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
							
							$data_a['ReplySMS'] = 'Maaf, silahkan kirim lagi dengan format baru yaitu tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
							//echo "SUKSES SIMPAN VALID";
							$dataout3['DestinationNumber'] = $sender;
							$dataout3['TextDecoded'] = 'Maaf, silahkan kirim lagi dengan format baru yaitu tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$dataout3['CreatorID'] = 'Gammu';
							$this->model_app->getInsertMy('outbox',$dataout3);
						}else if($query_outlet->num_rows() == 0){
							foreach($query_ba1->result() as $bad){
								$data_f['ID_BA'] = "";
								$data_f['ID_BA'] = $bad->ID_BA;
							}
							$data_f['Processed'] = 'invalid';
							$data_f['ReplySMS'] = 'Maaf, Kode Toko tidak terdaftar. Mohon anda periksa kembali Kode Toko. Silahkan kirim lagi dengan format baru.';
							$query_update1 = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
							
							$data_a['ReplySMS'] = 'Maaf, Kode Toko tidak terdaftar. Mohon anda periksa kembali Kode Toko. Silahkan kirim lagi dengan format baru.';
							$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
							//echo "gagal";
							$dataout2['DestinationNumber'] = $sender;
							$dataout2['TextDecoded'] = 'Maaf, Kode Toko tidak terdaftar, Mohon anda periksa kembali Kode Toko. Silahkan kirim lagi dengan format baru.';
							$dataout2['CreatorID'] = 'Gammu';
							$this->model_app->getInsertMy('outbox',$dataout2);
						}else if($count_stts['1'] != count($product)){
							for($i = 0; $i <= count($product_not);$i++){
									$prd .= "#".$product_not[$i]; 
							}
							foreach($query_ba1->result() as $bad){
								$data_f['ID_BA'] = "";
								$data_f['ID_BA'] = $bad->ID_BA;
							}
							$data_f['Processed'] = 'invalid';
							$data_f['ReplySMS'] = 'Maaf, Ada Kode Barang yang tidak terdaftar. Kode Barang '.$prd.' Tidak Terdaftar. Silahkan kirim lagi dengan format baru';
							$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
							
							$data_a['ReplySMS'] = 'Maaf, Ada Kode Barang yang tidak terdaftar. Kode Barang '.$prd.' Tidak Terdaftar. Silahkan kirim lagi dengan format baru';
							$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
							//echo "gagal";
							$dataout2['DestinationNumber'] = $sender;
							$dataout2['TextDecoded'] = 'Maaf, Ada Kode Barang yang tidak terdaftar. Kode Barang '.$prd.' Tidak Terdaftar. Silahkan kirim lagi dengan format baru';
							$dataout2['CreatorID'] = 'Gammu';
							$this->model_app->getInsertMy('outbox',$dataout2);
							$prd = "";
						}
						//echo "<br>--------------------------------------------------------------------------------<br>";
						//echo "<br>--------------------------------------------------------------------------------<br>";
					}else{
						
						if(count($fail) !== 0 or count($fail_product) !== 0 or count($fail_qty) !== 0)
						{
							if(count($fail) !== 0){
								for($i = 0; $i <= count($fail);$i++){
									$salah .= " #".$fail[$i].", "; 
								}
								foreach($query_ba1->result() as $bad){
									$data_f['ID_BA'] = "";
									$data_f['ID_BA'] = $bad->ID_BA;
								}
								$data_f['Processed'] = 'invalid';
								$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
								$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
						
								$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
								$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
								
								$dat2['DestinationNumber'] = $sender;
								$dat2['TextDecoded'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
								$dat2['CreatorID'] = 'Gammu';
								$this->model_app->getInsertMy('outbox',$dat2);
								
								//$data_f['Processed'] = 'invalid';
								//$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah yaitu '.$salah.'. Format benar : tglsales(ddmmyy) <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty[lama]';
								//$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
						
								//$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah yaitu '.$salah.'. Format benar : tglsales(ddmmyy) <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty[lama]';
								//$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
								$salah = "";
							}else if(count($fail_product) !== 0){
								for($i = 0; $i <= count($fail_product);$i++){
									$salah .= " #".$fail_product[$i].", "; 
								}
								
								foreach($query_ba1->result() as $bad){
									$data_f['ID_BA'] = "";
									$data_f['ID_BA'] = $bad->ID_BA;
								}
								$data_f['Processed'] = 'invalid';
								$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
								$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
						
								$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
								$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
								
								$dat1['DestinationNumber'] = $sender;
								$dat1['TextDecoded'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
								$dat1['CreatorID'] = 'Gammu';
								$this->model_app->getInsertMy('outbox',$dat1);
								
								
								//$data_f['Processed'] = 'invalid';
								//$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah yaitu '.$salah.'. Format benar : tglsales(ddmmyy) <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty[lama]';
								//$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
						
								//$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah yaitu '.$salah.'. Format benar : tglsales(ddmmyy) <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty[lama]';
								//$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
								$salah = "";

							}else if(count($fail_qty) !== 0){
								for($i = 0; $i <= count($fail_qty);$i++){
									$salah .= " #".$fail_qty[$i].", "; 
								}
								
								foreach($query_ba1->result() as $bad){
									$data_f['ID_BA'] = "";
									$data_f['ID_BA'] = $bad->ID_BA;
								}
								$data_f['Processed'] = 'invalid';
								$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
								$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
						
								$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
								$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);

								
								$dat3['DestinationNumber'] = $sender;
								$dat3['TextDecoded'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
								$dat3['CreatorID'] = 'Gammu';
								$this->model_app->getInsertMy('outbox',$dat3);
								
								
								//$data_f['Processed'] = 'invalid';
								//$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah yaitu '.$salah.'. Format benar : tglsales(ddmmyy) <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty[lama]';
								//$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
						
								//$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah yaitu '.$salah.'. Format benar : tglsales(ddmmyy) <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty[lama]';
								//$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
								$salah = "";

							}
						}
						else
						{
							foreach($query_ba1->result() as $bad){
								$data_f['ID_BA'] = "";
								$data_f['ID_BA'] = $bad->ID_BA;
							}
							$data_f['Processed'] = 'invalid';
							$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
						
							$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
						
							$dat5['DestinationNumber'] = $sender;
							$dat5['TextDecoded'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$dat5['CreatorID'] = 'Gammu';
							$this->model_app->getInsertMy('outbox',$dat5);
						}
						
						//$data_f['Processed'] = 'invalid';
						//$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy)<spasi>kodetoko#kodeproduk<spasi>qty#kodeproduk<spasi>qty[lama]';
						//$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
						
						//$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy)<spasi>kodetoko#kodeproduk<spasi>qty#kodeproduk<spasi>qty[lama]';
						//$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
						//echo "PAYAH";
						//echo "<br>--------------------------------------------------------------------------------<br>";
						//echo "<br>--------------------------------------------------------------------------------<br>";
						//echo $dataout1['DestinationNumber'] = $sender;
						//echo $dataout1['TextDecoded'] = 'Maaf, Format Salah. Mohon di cek lagi #kode product <spasi> qty# [lama].';
						//echo $dataout1['CreatorID'] = 'Gammu';
						//$this->model_app->getInsertMy('outbox',$dataout1);
					}
					unset($product_status);
					unset($product_not);
					unset($product);
					unset($qty);
					unset($fail_qty);
					unset($fail_product);
					unset($fail);
			}else if($cek === 2){
			//kode baru dengan kode ba
				 $data = substr($db->TextSMS,$crash_position);
				 //echo '<br>';
				 $third_data = substr($db->TextSMS,7);
				 //echo '<br>';
				 $nt_sparate = explode("#",$third_data);
				 //echo $nt_sparate[0];
				 //echo '<br>';
				 $sp = explode(" ",$nt_sparate[0]);
				 $outlet = $sp[1];
				 $ba = null;
				 $ba = substr($db->TextSMS,$space_format1,5);
				 $sender = $db->SenderNumber;
				 $query_ba1 = $this->model_app->getQueryMy("SELECT ID_BA FROM Table_BA WHERE CONTACT='".$sender."'");
				 
				 $pieces = explode("#", $data);
				 $jml = substr_count($data,"#");
				 for($i=1;$i<=$jml;$i++){
					if(substr_count($pieces[$i]," ") == 1){
						 $da = explode(" ", $pieces[$i]);
						//echo "<br>";
						if($da[0] === "" or $da[0] === null or !preg_match('/^[0-9]{1,}$/',$da[0])){
						//	echo "fail";
						//	echo "<br>";
						 $fail_product[] = $da[0];
						//	echo "<br>";
						}else{
						 $product[] = $da[0];
						//	echo "<br>";
						}
						
						if($da[1] === "" or $da[1] === null or !preg_match('/^[0-9]{1,}$/',$da[1])){
						//	echo "fail";
						//	echo "<br>";
						 $fail_qty[] = $da[1];
						//	echo "<br>";
						}else{
						 $qty[] = $da[1];
						//	echo "<br>";
						}
					}else{
						//echo "fail";
						//echo "<br>";
						 $fail[] = $pieces[$i];
						//echo "<br>";
					}
				 }
					//echo '<br>';
					//$data = '#29 1#34 1#85 2#89 1#92 1#55 1#43 1#114 1#17 1#21 3#22 2#23 1#998 20#999 16';
					//$list = preg_split("/(?<=\w)\b\s*/",$data);
					//foreach($list as $key => $value){
					//	if(substr($value,0,1) === '#'){
					//		 $product[] = substr($value,1);
							//echo '=id product<br>';
					//	}else if($value === ''){
							//echo 'bukan apa apa<br>';
					//	}else if(is_numeric($value)){
					//		 $qty[] = $value;
							//echo '=qty product<br>';
					//	}
					//}
					//echo "<br>--------------------------------------------------------------------------------<br>";
				
				//echo 'jumlah array product : '.count($product).'<br>';
				//echo 'jumlah array qty : ' .count($qty).'<br>';
				//echo 'jumlah fail : ' .count($fail).'<br>';
				
				if(count($product) === count($qty) and count($fail) === 0){
					for($i=0;$i<count($product);$i++){
						//echo str_pad($product[$i], 4, '0', STR_PAD_LEFT).'<br>';
						$query_product = $this->model_app->getQueryMy("SELECT ID_Product FROM Ms_PRODUCT WHERE ID_Product='".str_pad($product[$i], 4, '0', STR_PAD_LEFT)."'");
						if($query_product->num_rows() > 0){
							$product_status[] = 1;
						}else{
							$product_not[] = $product[$i];
						}
					}
					$count_stts = array_count_values($product_status);
					//echo 'jumlahnya : '.$count_stts['1'];
					$query_outlet = $this->model_app->getQueryMy("SELECT ID_OUTLET FROM Ms_OUTLET WHERE ID_OUTLET='".$outlet."'");
					$query_ba = $this->model_app->getQueryMy("SELECT ID_BA FROM Ms_BA WHERE ID_BA='".$ba."'");
					
					if ($count_stts['1'] == count($product) and $query_outlet->num_rows() > 0 and $query_ba->num_rows() > 0){
						$data_f['ID_BA'] = $ba;
						$data_f['Processed'] = 'valid';
						$data_f['ReplySMS'] = 'Terimakasih, sms anda akan segera kami proses [Format Baru]';
						$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
						
						$data_a['ReplySMS'] = 'Terimakasih, sms anda akan segera kami proses [Format Baru]';
						$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
						//echo "SUKSES SIMPAN VALID";
						$dataout3['DestinationNumber'] = $sender;
						$dataout3['TextDecoded'] = 'Terimakasih, sms anda akan segera kami proses [Format Baru]';
						$dataout3['CreatorID'] = 'Gammu';
						$this->model_app->getInsertMy('outbox',$dataout3);
					}else if($query_ba->num_rows() == 0){
						$data_f['ID_BA'] = "";
						$data_f['Processed'] = 'invalid';
						$data_f['ReplySMS'] = 'Maaf, Kode BA tidak terdaftar. Mohon anda periksa kembali kode BA[Format Baru]';
						$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
						
						$data_a['ReplySMS'] = 'Maaf, Kode BA tidak terdaftar. Mohon anda periksa kembali kode BA[Format Baru]';
						$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
						//echo "gagal";
						$dataout2['DestinationNumber'] = $sender;
						$dataout2['TextDecoded'] = 'Maaf, Kode BA tidak terdaftar. Mohon anda periksa kembali kode BA[Format Baru]';
						$dataout2['CreatorID'] = 'Gammu';
						$this->model_app->getInsertMy('outbox',$dataout2);
					}else if($query_outlet->num_rows() == 0){
						//foreach($query_ba1->result() as $bad){
						//		$data_f['ID_BA'] = $bad->ID_BA;
						//}
						$data_f['ID_BA'] = $ba;
						$data_f['Processed'] = 'invalid';
						$data_f['ReplySMS'] = 'Maaf, Kode Toko tidak terdaftar. Mohon anda periksa kembali kode toko [Format Baru]';
						$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
						
						$data_a['ReplySMS'] = 'Maaf, Kode Toko tidak terdaftar. Mohon anda periksa kembali kode toko [Format Baru]';
						$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
						//echo "gagal";
						$dataout2['DestinationNumber'] = $sender;
						$dataout2['TextDecoded'] = 'Maaf, Kode Toko tidak terdaftar. Mohon anda periksa kembali kode toko [Format Baru]';
						$dataout2['CreatorID'] = 'Gammu';
						$this->model_app->getInsertMy('outbox',$dataout2);
					}else if($count_stts['1'] != count($product)){
						for($i = 0; $i <= count($product_not);$i++){
							$prd .= "#".$product_not[$i]; 
						}
						//foreach($query_ba1->result() as $bad){
						//		$data_f['ID_BA'] = $bad->ID_BA;
						//}
						$data_f['ID_BA'] = $ba;
						$data_f['Processed'] = 'invalid';
						$data_f['ReplySMS'] = 'Maaf, Ada Kode Barang yang tidak terdaftar. Kode Barang '.$prd.' Tidak Terdaftar. Mohon anda periksa kembali Kode Barang [Format Baru]';
						$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
						
						$data_a['ReplySMS'] = 'Maaf, Ada Kode Barang yang tidak terdaftar. Kode Barang '.$prd.' Tidak Terdaftar. Mohon anda periksa kembali Kode Barang [Format Baru]';
						$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
						//echo "gagal";
						$dataout2['DestinationNumber'] = $sender;
						$dataout2['TextDecoded'] = 'Maaf, Ada Kode Barang yang tidak terdaftar. Kode Barang '.$prd.' Tidak Terdaftar. Mohon anda periksa kembali Kode Barang [Format Baru]';
						$dataout2['CreatorID'] = 'Gammu';
						$this->model_app->getInsertMy('outbox',$dataout2);
						$prd = "";
					}
					//echo "<br>--------------------------------------------------------------------------------<br>";
					//echo "<br>--------------------------------------------------------------------------------<br>";
				}else{
					if(count($fail) !== 0 or count($fail_product) !== 0 or count($fail_qty) !== 0)
					{
						if(count($fail) !== 0){
							for($i = 0; $i <= count($fail);$i++){
								$salah .= " #".$fail[$i].", "; 
							}
							//foreach($query_ba1->result() as $bad){
							//	$data_f['ID_BA'] = $bad->ID_BA;
							//}
							$data_f['ID_BA'] = $ba;
							$data_f['Processed'] = 'invalid';
							$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
					
							$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);

							$dat2['DestinationNumber'] = $sender;
							$dat2['TextDecoded'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$dat2['CreatorID'] = 'Gammu';
							$this->model_app->getInsertMy('outbox',$dat2);
							
							//$data_f['Processed'] = 'invalid';
							//$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah yaitu '.$salah.'. Format benar : tglsales(ddmmyy) <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty[baru]';
							//$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
					
							//$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah yaitu '.$salah.'. Format benar : tglsales(ddmmyy) <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty[baru]';
							//$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
							$salah = "";
						}else if(count($fail_product) !== 0){
							for($i = 0; $i <= count($fail_product);$i++){
								$salah .= " #".$fail_product[$i].", "; 
							}
							//foreach($query_ba1->result() as $bad){
							//	$data_f['ID_BA'] = $bad->ID_BA;
							//}
							$data_f['ID_BA'] = $ba;
							$data_f['Processed'] = 'invalid';
							$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
					
							$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
							
							$dat2['DestinationNumber'] = $sender;
							$dat2['TextDecoded'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$dat2['CreatorID'] = 'Gammu';
							$this->model_app->getInsertMy('outbox',$dat2);
							
							//$data_f['Processed'] = 'invalid';
							//$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah yaitu '.$salah.'. Format benar : tglsales(ddmmyy) <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty[baru]';
							//$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
					
							//$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah yaitu '.$salah.'. Format benar : tglsales(ddmmyy) <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty[baru]';
							//$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
							$salah = "";

						}else if(count($fail_qty) !== 0){
							for($i = 0; $i <= count($fail_qty);$i++){
								$salah .= " #".$fail_qty[$i].", "; 
							}
							//foreach($query_ba1->result() as $bad){
							//	$data_f['ID_BA'] = $bad->ID_BA;
							//}
							$data_f['ID_BA'] = $ba;
							$data_f['Processed'] = 'invalid';
							$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
					
							$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
							
							$dat2['DestinationNumber'] = $sender;
							$dat2['TextDecoded'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
							$dat2['CreatorID'] = 'Gammu';
							$this->model_app->getInsertMy('outbox',$dat2);
							
							//$data_f['Processed'] = 'invalid';
							//$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah yaitu '.$salah.'. Format benar : tglsales(ddmmyy) <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty[baru]';
							//$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
					
							//$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah yaitu '.$salah.'. Format benar : tglsales(ddmmyy) <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty[baru]';
							//$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
							$salah = "";
						}
					}
					else
					{
						//foreach($query_ba1->result() as $bad){
						//		$data_f['ID_BA'] = $bad->ID_BA;
						//}
						$data_f['ID_BA'] = $ba;
						$data_f['Processed'] = 'invalid';
						$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
						$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
					
						$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
						$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
						
						$dat2['DestinationNumber'] = $sender;
						$dat2['TextDecoded'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy) <spasi> kodeBA <spasi> kodetoko#kodeproduk <spasi> qty#kodeproduk <spasi> qty';
						$dat2['CreatorID'] = 'Gammu';
						$this->model_app->getInsertMy('outbox',$dat2);
					}
				
					//$data_f['Processed'] = 'invalid';
					//$data_f['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy)<spasi>kodeBA<spasi>kodetoko#kodeproduk<spasi>qty#kodeproduk<spasi>qty[baru].';
					//$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
					
					//$data_a['ReplySMS'] = 'Mohon maaf, Format yang anda gunakan salah. Format benar : tglsales(ddmmyy)<spasi>kodeBA<spasi>kodetoko#kodeproduk<spasi>qty#kodeproduk<spasi>qty[baru].';
					//$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
					//echo "PAYAH";
					//echo "<br>--------------------------------------------------------------------------------<br>";
					//echo "<br>--------------------------------------------------------------------------------<br>";
					//echo $dataout1['DestinationNumber'] = $sender;
					//echo $dataout1['TextDecoded'] = 'Maaf, Format Salah. Mohon di cek lagi #kode product <spasi> qty#[baru].';
					//echo $dataout1['CreatorID'] = 'Gammu';
					//$this->model_app->getInsertMy('outbox',$dataout1);
				}
				unset($product_status);
				unset($product_not);
				unset($product);
				unset($qty);
				unset($fail_qty);
				unset($fail_product);
				unset($fail);
			}else{
				$query_ba1 = $this->model_app->getQueryMy("SELECT ID_BA FROM Table_BA WHERE CONTACT='".$db->SenderNumber."'");
				$data_f['ID_BA'] = "";
				$data_f['Processed'] = 'invalid';
				$data_f['ReplySMS'] = 'Format tidak sesuai format baru atau format lama.';
				$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
				
				$data_a['ReplySMS'] = 'Format tidak sesuai format baru atau format lama.';
				$query_update2 = $this->model_app->getUpdateMy('new_inbox',$data_a,'object_id',$db->RefObjectID);
											
				$dat10['DestinationNumber'] = $db->SenderNumber;
				$dat10['TextDecoded'] = 'Format tidak sesuai format baru atau format lama.';
				$dat10['CreatorID'] = 'Gammu';
				$this->model_app->getInsertMy('outbox',$dat10);
			}
		}
	}
	
	
	//function filter_full_format_baru(){
	//	$query = $this->model_app->getQueryMy("select ObjectID, TextSMS, SenderNumber from Final_Inbox WHERE Processed='loading'");
	//	foreach($query->result() as $db){
	//		echo $data = substr($db->TextSMS,21);
	//		echo '<br>';
	//		echo $outlet = substr($db->TextSMS,7,8);
	//		echo $ba = substr($db->TextSMS,16,5);
	//		echo $sender = $db->SenderNumber;
	//		echo '<br>';
	//		//$data = '#29 1#34 1#85 2#89 1#92 1#55 1#43 1#114 1#17 1#21 3#22 2#23 1#998 20#999 16';
	//		$list = preg_split("/(?<=\w)\b\s*/",$data);
	//		foreach($list as $key => $value){
	//			if(substr($value,0,1) === '#'){
	//				echo $product[] = substr($value,1);
	//				echo '=id product<br>';
	//			}else if($value === ''){
	//				echo 'bukan apa apa<br>';
	//			}else if(is_numeric($value)){
	//				echo $qty[] = $value;
	//				echo '=qty product<br>';
	//			}
	//		}
	//		echo "<br>--------------------------------------------------------------------------------<br>";
	//		
	//		echo 'jumlah array product : '.count($product).'<br>';
	//		echo 'jumlah array qty : ' .count($qty).'<br>';
	//		
	//		if(count($product) === count($qty)){
	//			for($i=0;$i<count($product);$i++){
	//				echo str_pad($product[$i], 4, '0', STR_PAD_LEFT).'<br>';
	//				$query_product = $this->model_app->getQueryMy("SELECT ID_Product FROM Ms_PRODUCT WHERE ID_Product='".str_pad($product[$i], 4, '0', STR_PAD_LEFT)."'");
	//				if($query_product->num_rows() > 0){
	//					$product_status[] = 1;
	//				}else{
	//					echo "CEK LAGI DEH<br>";
	//				}
	//			}
	//			$count_stts = array_count_values($product_status);
	//			echo 'jumlahnya : '.$count_stts['1'];
	//			$query_outlet = $this->model_app->getQueryMy("SELECT ID_OUTLET FROM Ms_OUTLET WHERE ID_OUTLET='".$outlet."'");
	//			$query_ba = $this->model_app->getQueryMy("SELECT ID_BA FROM Ms_BA WHERE ID_BA='".$ba."'");
	//			
	//			if ($count_stts['1'] == count($product) and $query_outlet->num_rows() > 0 and $query_ba->num_rows() > 0){
	//				$data_f['Processed'] = 'valid';
	//				$data_f['ReplySMS'] = 'Terimakasih, sms berhasil langsung kami proses';
	//				$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
	//				echo "SUKSES SIMPAN VALID";
	//				//echo $dataout3['DestinationNumber'] = $sender;
	//				//echo $dataout3['TextDecoded'] = 'Terimakasih, sms berhasil langsung kami proses';
	//				//echo $dataout3['CreatorID'] = 'Gammu';
	//				//$this->model_app->getInsertMy('outbox',$dataout3);
	//			}else if($query_outlet->num_rows() == 0){
	//				$data_f['Processed'] = 'invalid';
	//				$data_f['ReplySMS'] = 'Maaf, Kode Outlet Tidak Terdaftar';
	//				$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
	//				echo "gagal";
	//				//echo $dataout2['DestinationNumber'] = $sender;
	//				//echo $dataout2['TextDecoded'] = 'Maaf, Kode Outlet Tidak Terdaftar';
	//				//echo $dataout2['CreatorID'] = 'Gammu';
	//				//$this->model_app->getInsertMy('outbox',$dataout2);
	//			}else if($query_ba->num_rows() == 0){
	//				$data_f['Processed'] = 'invalid';
	//				$data_f['ReplySMS'] = 'Maaf, Kode BA Tidak Terdaftar';
	//				$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
	//				echo "gagal";
	//				//echo $dataout2['DestinationNumber'] = $sender;
	//				//echo $dataout2['TextDecoded'] = 'Maaf, Maaf, Kode BA Tidak Terdaftar';
	//				//echo $dataout2['CreatorID'] = 'Gammu';
	//				//$this->model_app->getInsertMy('outbox',$dataout2);
	//			}else if($count_stts['1'] != count($product)){
	//				$data_f['Processed'] = 'invalid';
	//				$data_f['ReplySMS'] = 'Maaf, Mohon dicek kembali ID PRODUCT dan QTY';
	//				$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
	//				echo "gagal";
	//				//echo $dataout2['DestinationNumber'] = $sender;
	//				//echo $dataout2['TextDecoded'] = 'Maaf, Mohon dicek kembali ID PRODUCT dan QTY';
	//				//echo $dataout2['CreatorID'] = 'Gammu';
	//				//$this->model_app->getInsertMy('outbox',$dataout2);
	//			}
	//			echo "<br>--------------------------------------------------------------------------------<br>";
	//			echo "<br>--------------------------------------------------------------------------------<br>";
	//		}else{
	//			$data_f['Processed'] = 'invalid';
	//			$data_f['ReplySMS'] = 'Maaf, Format Salah. Mohon di cek lagi #kode product <spasi> qty#.';
	//			$query_update = $this->model_app->getUpdateMy('Final_Inbox',$data_f,'ObjectID',$db->ObjectID);
	//			echo "PAYAH";
	//			echo "<br>--------------------------------------------------------------------------------<br>";
	//			echo "<br>--------------------------------------------------------------------------------<br>";
	//			//echo $dataout1['DestinationNumber'] = $sender;
	//			//echo $dataout1['TextDecoded'] = 'Maaf, Format Salah. Mohon di cek lagi #kode product <spasi> qty#.';
	//			//echo $dataout1['CreatorID'] = 'Gammu';
	//			//$this->model_app->getInsertMy('outbox',$dataout1);
	//		}
	//		unset($product_status);
	//		unset($product);
	//		unset($qty);
	//	}
	//}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//------------------------------------------------------------------------------------------------------------------------------------------------------//
	//---------------------------------------------------------VALIDASI UNTUK DBSERVER----------------------------------------------------------------------//
	//------------------------------------------------------------------------------------------------------------------------------------------------------//
	
	function new_header_detail(){
		$query_new_header = $this->model_app->getQuerySrv("SELECT TOP 500 * FROM Final_Inbox WHERE Processed='valid'");
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
				echo $data_header['ID_OUTLET'] = substr($db->TextSMS,7,8);
				echo "<br>";
				$query_outlet = $this->model_app->getQuerySrv("SELECT * FROM Ms_OUTLET WHERE ID_OUTLET='".substr($db->TextSMS,7,8)."'");
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
				echo $data_header['ID_OUTLET'] = substr($db->TextSMS,13,8);
				echo "<br>";
				echo $data_header['ID_BA'] = substr($db->TextSMS,7,5);
				echo "<br>";
				$query_outlet = $this->model_app->getQuerySrv("SELECT * FROM Ms_OUTLET WHERE ID_OUTLET='".substr($db->TextSMS,13,8)."'");
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
	
	function sync_new_inbox(){
		$query5 = $this->model_app->getQueryMy("select * from new_inbox where status='unsync'");
		foreach($query5->result() as $db){
			$query_insert = $this->model_app->getQuerySrv("insert into new_inbox (object_id,ReceivingDateTime,Text,SenderNumber,Coding,UDH,SMSCNumber,Class,TextDecoded,RecipientID,Processed,ReplySMS) VALUES 
			('".$db->object_id."','".$db->ReceivingDateTime."','".$db->Text."','".$db->SenderNumber."','".$db->Coding."','".$db->UDH."','".$db->SMSCNumber."','".$db->Class."','".$db->TextDecoded."','".$db->RecipientID."','".$db->Processed."','".$db->ReplySMS."')");
			$query_update = $this->model_app->getQueryMy("update new_inbox set status='sync' where object_id='".$db->object_id."'");
		}
	}
	
	function sync_final_inbox(){
		$query6 = $this->model_app->getQueryMy("select * from final_inbox where status='unsync'");
		foreach($query6->result() as $db){
			$query_insert = $this->model_app->getQuerySrv("insert into Final_Inbox (ReceiveDt,SenderNumber,ID_BA,FreqSMS,TextSMS,Processed,ProcessedDt,ReplySMS) VALUES 
			('".$db->ReceiveDt."','".$db->SenderNumber."','".$db->ID_BA."','".$db->FreqSMS."','".$db->TextSMS."','".$db->Processed."','".$db->ProcessedDt."','".$db->ReplySMS."')");
			$query_update = $this->model_app->getQueryMy("update final_inbox set status='sync' where ObjectID='".$db->ObjectID."'");
		}
	}
	
	function summary_freq(){
		$query7 = $this->model_app->getQuerySrv("select distinct ID_BA from Ms_BA where status=1");
		foreach($query7->result() as $db){
			//echo $db->ID_BA;
			$query8 = $this->model_app->getQuerySrv("select ID_BA from Final_Inbox Where ID_BA='".$db->ID_BA."' and datediff(day, ReceiveDt, '".date('m-d-Y',strtotime('-1 days'))."') = 0");
			//echo "<br>";
			//echo $query8->num_rows();
			//echo "<br>";
			$this->model_app->getQuerySrv("insert into Summary_BA_Freq (ID_BA,SUMMARY_DATE,FREQ_SMS) values ('".$db->ID_BA."','".date('m-d-Y',strtotime('-1 days'))."','".$query8->num_rows()."')");
		}
	}
	
	function summary_freq_sms(){
		$query = $this->model_app->getQuerySrv("select distinct ID_BA from Ms_BA where status=1");
		foreach($query->result() as $db){
			$query8 = $this->model_app->getQuerySrv("select * from Final_Inbox Where ID_BA='".$db->ID_BA."' and datediff(day, ReceiveDt, '".date('m-d-Y',strtotime('-1 days'))."') = 0 or datediff(day, ReceiveDt, '".date('m-d-Y',strtotime('0 days'))."') = 0");
			foreach($query8->result() as $abs){
				echo $dte = substr($abs->TextSMS,0,6);
				echo "<br/>";
				echo date('dmy',strtotime('-1 days'));
				echo "<br/>";
				if($dte == date('dmy',strtotime('-1 days'))){
					echo $dte." - OK";
					echo "<br/>";
					echo "<br/>";
					$this->model_app->getQuerySrv("insert into Summary_BA_Freq (ID_BA,SUMMARY_DATE,FREQ_SMS) values ('".$db->ID_BA."','".date('m-d-Y',strtotime('-1 days'))."','".$query8->num_rows()."')");
				}else{
					echo $dte." - #NA";
					echo "<br/>";
					echo "<br/>";
				}
			}
		}
		
	}
}
 
/*End of file schedule.php*/
/*Location:.application/controllers/schedule.php*/