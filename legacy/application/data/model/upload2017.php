<?php
/**
 * application_data_model_upload2017
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
 *
 */
class model_upload2017 extends model_base
{

	public function __construct()
	{
		parent::__construct('upload2017');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $upload2017_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($upload2017_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($upload2017_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'upload2017',
			$filter = array
			(
				'upload2017_id' => $upload2017_id
			),
			$single = true
		);
	}
	
	public static function check_firstround_entries_by_month_table($month, $table_){
		return data_entry::get_by_filter
		(
			$table = 'upload2017',
			$filter = array
			(
				'type' => 'firstround',
				'month' => intval($month),
				'table_' => $table_
			),
			$single = false
		);
	}

		public static function check_final_entries_by_month_table($month, $table_){
		return data_entry::get_by_filter
		(
			$table = 'upload2017',
			$filter = array
			(
				'type' => 'final',
				'month' => intval($month),
				'table_' => $table_
			),
			$single = false
		);
	}
	
	public static function get_all_entries_by_month($month){
		return data_entry::get_by_filter
		(
			$table = 'upload2017',
			$filter = array
			(
				'month' => intval($month),
			),
			$single = false,
			$order_by = array("field" => "month ASC, type ASC, table_ ASC, position", "direction" => "ASC")
		);
	}

	/*
	 * get_all_entries()
	 *
	 *
	 * @return Array of Objects
	 */
	public static function get_all_entries()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		return data_entry::get_all('upload2017', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $upload2017_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($upload2017_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($upload2017_id) betreten.");
		$db = database::get_instance();
		$upload2017_id = $db->escape($upload2017_id);
		$sql = "
			DELETE FROM `upload2017` WHERE `upload2017_id` = $upload2017_id;
		";
		return $db->query($sql);
	}

}
