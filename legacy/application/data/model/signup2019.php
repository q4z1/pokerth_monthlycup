<?php
/**
 * application_data_model_signup2019
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
 *
 */
class model_signup2019 extends model_base
{

	public function __construct()
	{
		parent::__construct('signup2019');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $signup2019_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($signup2019_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($signup2019_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'signup2019',
			$filter = array
			(
				'signup2019_id' => $signup2019_id
			),
			$single = true
		);
	}

	/*
	 * get_entries_by_client_id()
	 *
	 * @param String $client_id
	 *
	 * @return Object
	 */
	public static function get_entries_by_month($month)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($signup2019_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'signup2019',
			$filter = array
			(
				'month' => intval($month)
			),
			$single = false,
			$order_by = array("field" => "date", "direction", "desc")
		);
	}
	
	public static function get_public_valid_entries_by_month($month)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($signup2019_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'signup2019',
			$filter = array
			(
				'month' => intval($month),
				'valid' => 1
			),
			$single = false,
			$order_by = array("field" => "date", "direction", "desc")
		);
	}
	/*
	 * get_signup2019_by_playername()
	 *
	 * @param String $username
	 *
	 * @return Object
	 */
	public static function get_entry_by_playername($playername)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		return data_entry::get_by_filter
		(
			$table = 'signup2019',
			$filter = array
			(
				'playername' => $playername
			),
			$single = true
		);
	}
	
	/*
	 * get_signup2019_by_month_playername()
	 *
	 * @param String $username
	 *
	 * @return Object
	 */
	public static function get_entry_by_month_playername($month, $playername)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		return data_entry::get_by_filter
		(
			$table = 'signup2019',
			$filter = array
			(
				'month' => $month,
				'playername' => $playername
			),
			$single = true
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
		return data_entry::get_all('signup2019', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $signup2019_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($signup2019_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($signup2019_id) betreten.");
		$db = database::get_instance();
		$signup2019_id = $db->escape($signup2019_id);
		$sql = "
			DELETE FROM `signup2019` WHERE `signup2019_id` = $signup2019_id;
		";
		return $db->query($sql);
	}

}
