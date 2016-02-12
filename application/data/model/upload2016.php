<?php
/**
 * application_data_model_upload2016
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
 *
 */
class model_upload2016 extends model_base
{

	public function __construct()
	{
		parent::__construct('upload2016');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $upload2016_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($upload2016_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($upload2016_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'upload2016',
			$filter = array
			(
				'upload2016_id' => $upload2016_id
			),
			$single = true
		);
	}
	
	public static function check_firstround_entries_by_month_table($month, $table_){
		return data_entry::get_by_filter
		(
			$table = 'upload2016',
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
			$table = 'upload2016',
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
			$table = 'upload2016',
			$filter = array
			(
				'month' => intval($month),
			),
			$single = false,
			$order_by = array("field" => "month, type, table, position", "direction", "asc")
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
		return data_entry::get_all('upload2016', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $upload2016_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($upload2016_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($upload2016_id) betreten.");
		$db = database::get_instance();
		$upload2016_id = $db->escape($upload2016_id);
		$sql = "
			DELETE FROM `upload2016` WHERE `upload2016_id` = $upload2016_id;
		";
		return $db->query($sql);
	}

}
