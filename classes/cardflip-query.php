<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class cardflip_cls_dbquery {

	public static function cardflip_count($id = 0) {

		global $wpdb;
		$result = '0';
		
		if($id <> "" && $id > 0) {
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS count FROM " . $wpdb->prefix . "cardflip_slideshow WHERE cf_id = %d", array($id));
		} 
		else {
			$sSql = "SELECT COUNT(*) AS count FROM " . $wpdb->prefix . "cardflip_slideshow";
		}
		
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function cardflip_select_bygroup($group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "cardflip_slideshow";

		if($group <> "") {
			$sSql = $sSql . " WHERE cf_group = %s order by cf_order";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " order by cf_group, cf_order";
		}
		
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function cardflip_select_byid($id = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "cardflip_slideshow";

		if($id <> "") {
			$sSql = $sSql . " WHERE cf_id = %d LIMIT 1";
			$sSql = $wpdb->prepare($sSql, array($id));
			$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		}
		else {
			$sSql = $sSql . " order by cf_group, cf_order";
			$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		}
		
		return $arrRes;
	}
	
	public static function cardflip_group() {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT distinct(cf_group) FROM " . $wpdb->prefix . "cardflip_slideshow order by cf_group";
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}

	public static function cardflip_delete($id = "") {

		global $wpdb;

		if($id <> "") {
			$sSql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "cardflip_slideshow WHERE cf_id = %s LIMIT 1", $id);
			$wpdb->query($sSql);
		}
		
		return true;
	}

	public static function cardflip_action_ins($data = array(), $action = "insert") {

		global $wpdb;
		
		if($action == "insert") {
			$sql = $wpdb->prepare("INSERT INTO " . $wpdb->prefix . "cardflip_slideshow
				(cf_image, cf_link, cf_title, cf_target, cf_order, cf_group, cf_status) VALUES (%s, %s, %s, %s, %d, %s, %s)", 
				array($data["cf_image"], $data["cf_link"], $data["cf_title"], $data["cf_target"], $data["cf_order"], $data["cf_group"], $data["cf_status"]));
			$wpdb->query($sql);
			return "inserted";
		}
		elseif($action == "update") {
			$sSql = $wpdb->prepare("UPDATE " . $wpdb->prefix . "cardflip_slideshow SET cf_image = %s, cf_link = %s, cf_title = %s, cf_target = %s, 
				cf_order = %d, cf_group = %s, cf_status = %s WHERE cf_id = %d LIMIT 1", 
				array($data["cf_image"], $data["cf_link"], $data["cf_title"], $data["cf_target"], $data["cf_order"], $data["cf_group"], $data["cf_status"], $data["cf_id"]));
			$wpdb->query($sSql);
			return "update";
		}
	}
	


	public static function cardflip_default() {

		$count = cardflip_cls_dbquery::cardflip_count($id = 0);
		if($count == 0){
			$img1 = plugin_dir_url( __DIR__ ) . '/images/sing_1.jpg';
			$img2 = plugin_dir_url( __DIR__ ) . '/images/sing_2.jpg';
			$img3 = plugin_dir_url( __DIR__ ) . '/images/sing_3.jpg';
			$img4 = plugin_dir_url( __DIR__ ) . '/images/sing_4.jpg';
			
			$data['cf_image'] = $img1;
			$data['cf_link'] = '';
			$data['cf_title'] = 'Sample Images';
			$data['cf_target'] = '_blank';
			$data['cf_order'] = 1;
			$data['cf_group'] = 'Group1';
			$data['cf_status'] = 'Yes';
			
			cardflip_cls_dbquery::cardflip_action_ins($data, "insert");
			$data['cf_image'] = $img2;
			cardflip_cls_dbquery::cardflip_action_ins($data, "insert");
			$data['cf_image'] = $img3;
			cardflip_cls_dbquery::cardflip_action_ins($data, "insert");
			$data['cf_image'] = $img4;
			cardflip_cls_dbquery::cardflip_action_ins($data, "insert");
		}
	}
	
	public static function cardflip_common_text($value) {
		
		$returnstring = "";
		switch ($value) 
		{
			case "Yes":
				$returnstring = '<span style="color:#006600;">Yes</span>';
				break;
			case "No":
				$returnstring = '<span style="color:#FF0000;">No</span>';
				break;
			case "_blank":
				$returnstring = '<span style="color:#006600;">New window</span>';
				break;
			case "_self":
				$returnstring = '<span style="color:#0000FF;">Same window</span>';
				break;
			default:
       			$returnstring = $value;
		}
		return $returnstring;
	}
}