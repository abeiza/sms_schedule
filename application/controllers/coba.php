<?php if(!defined('BASEPATH'))exit('No direct script access allowed');
	class Coba extends CI_Controller{
		function __construct(){
			parent::__construct();
		}
		
		function index(){
			$query_new_header = $this->model_app->getQuerySrv("SELECT * FROM Final_Inbox WHERE Processed='valid'");
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
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				
				
			}
		}
		//function index(){
		//	$query = $this->model_app->getQuerySrv("select TextSMS from Final_Inbox WHERE Processed='true'");
		//	foreach($query->result() as $db){
		//		echo $data = substr($db->TextSMS,7,8);
		//		echo '<br>';
				//$data = '#29 1#34 1#85 2#89 1#92 1#55 1#43 1#114 1#17 1#21 3#22 2#23 1#998 20#999 16';
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
				//echo "<br>--------------------------------------------------------------------------------<br>";
				//echo 'jumlah array product : '.count($product).'<br>';
				//echo 'jumlah array qty : ' .count($qty).'<br>';
				
				//if(count($product) === count($qty)){
				//	for($i=0;$i<count($product);$i++){
				//		echo str_pad($product[$i], 4, '0', STR_PAD_LEFT).'<br>';
				//		$query_product = $this->model_app->getQuerySrv("SELECT ID_Product FROM Table_PRODUCT WHERE ID_Product='".str_pad($product[$i], 4, '0', STR_PAD_LEFT)."'");
				//		if($query_product->num_rows() > 0){
				//			echo "MASUK SELEKSI<br>";
				//		}else{
				//			echo "CEK LAGI DEH<br>";
				//		}
				//	}
				//	echo "<br>--------------------------------------------------------------------------------<br>";
				//	echo "<br>--------------------------------------------------------------------------------<br>";
				//}else{
				//	echo "PAYAH";
				//	echo "<br>--------------------------------------------------------------------------------<br>";
				//	echo "<br>--------------------------------------------------------------------------------<br>";
				//}
				//unset($product);
				//unset($qty);
			//}
		}
		
		function z(){
			$text = "140216 IT001 KA300009#90 1#30 1#31 1#32 1#33 1#29 2#120 1#126 1#119 1#144 1#140 1#142 1#18 1#148 4#149 4#157 1#155 1#998 65#999 17";
			$y = '20'.substr($text,4,2);
			$m = substr($text,2,2);
			$d = substr($text,0,2);
			echo $date = strtotime($y.'-'.$m.'-'.$d);
			echo "<br>";
			echo $from = date("Ymd",$date);
			echo "<br>";
			echo $j=date("Ymd");
			echo "<br>";
			echo $ca = strtotime(date("Ymd")) - strtotime($from);
			echo "<br>";
			echo $range = intval($ca/86400);
			echo "<br>";
			//echo $c = strtotime("2016-02-15") - strtotime("2016-02-14");
			//echo $range = intval($c/86400);
			if($range <= 2 and $range > -1){
				echo "bener";
			}else{
				echo "salah";
			}
		}
	}

/*End of file coba.php*/
/*Location:.application/controllers/coba.php*/