<?php
/**
 * application_data_model_signup2023
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
 *
 */
class model_signup2023 extends model_base
{

	public function __construct()
	{
		parent::__construct('signup2023');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $signup2023_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($signup2023_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($signup2023_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'signup2023',
			$filter = array
			(
				'signup2023_id' => $signup2023_id
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
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($signup2023_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'signup2023',
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
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($signup2023_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'signup2023',
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
	 * get_signup2023_by_playername()
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
			$table = 'signup2023',
			$filter = array
			(
				'playername' => $playername
			),
			$single = true
		);
	}
	
	/*
	 * get_signup2023_by_month_playername()
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
			$table = 'signup2023',
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
		return data_entry::get_all('signup2023', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $signup2023_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($signup2023_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($signup2023_id) betreten.");
		$db = database::get_instance();
		$signup2023_id = $db->escape($signup2023_id);
		$sql = "
			DELETE FROM `signup2023` WHERE `signup2023_id` = $signup2023_id;
		";
		return $db->query($sql);
	}

}
