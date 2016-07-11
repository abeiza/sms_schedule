<?php if(!defined('BASEPATH'))exit('No direct script access allowed');

class Model_App extends CI_Model{
	function __construct(){
		parent::__construct();
	}
	
	function getData($table){
		return $this->db->get($table);
	}
	
	function getWhereMy($table,$pk,$id){
		$this->load->database('mysql_dbs',FALSE,TRUE);
		$this->db->where($pk,$id);
		return $this->db->get($table);
	}
	
	function getQuerySrv($data){
		$this->load->database('default',FALSE,TRUE);
		return $this->db->query($data);
	}
	
	function getQueryMy($data){
		$this->load->database('mysql_dbs',FALSE,TRUE);
		return $this->db->query($data);
	}
	
	function getUpdateMy($table,$data1,$pk,$id){
		$this->load->database('mysql_dbs',FALSE,TRUE);
		$this->db->where($pk,$id);
		return $this->db->update($table,$data1);
	}
	
	function getUpdateSrv($table,$data,$pk,$id){
		$this->load->database('default',FALSE,TRUE);
		$this->db->where($pk,$id);
		return $this->db->update($table,$data);
	}
	
	function getInsertMy($table,$data){
		$this->load->database('mysql_dbs',FALSE,TRUE);
		return $this->db->insert($table,$data);
	}
	
	function getInsertSrv($table,$data){
		$this->load->database('default',FALSE,TRUE);
		return $this->db->insert($table,$data);
	}
}

/*End of file model_app.php*/
/*Location :.application/models/model_app.php*/