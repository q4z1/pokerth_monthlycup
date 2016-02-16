<?php
/**
 * application_data_model_signup2016
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
 *
 */
class model_signup2016 extends model_base
{

	public function __construct()
	{
		parent::__construct('signup2016');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $signup2016_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($signup2016_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($signup2016_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'signup2016',
			$filter = array
			(
				'signup2016_id' => $signup2016_id
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
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($signup2016_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'signup2016',
			$filter = array
			(
				'month' => intval($month)
			),
			$single = false
		);
	}

	/*
	 * get_signup2016_by_playername()
	 *
	 * @param String $username
	 *
	 * @return Object
	 */
	public static function get_signup2016_by_playername($playername)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		return data_entry::get_by_filter
		(
			$table = 'signup2016',
			$filter = array
			(
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
		return data_entry::get_all('signup2016', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $signup2016_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($signup2016_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($signup2016_id) betreten.");
		$db = database::get_instance();
		$signup2016_id = $db->escape($signup2016_id);
		$sql = "
			DELETE FROM `signup2016` WHERE `signup2016_id` = $signup2016_id;
		";
		return $db->query($sql);
	}

}
