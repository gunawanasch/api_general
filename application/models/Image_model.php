<?php
class Image_model extends CI_Model {

	function addImage($name) {
		$this->load->database();
		$this->db->set("name", $name);
		$this->db->insert("image");
		if($this->db->affected_rows() > 0) {
			return $id = $this->db->insert_id();
		}
		else {
			return 0;
		}
	}
	
}
?>