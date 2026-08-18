<?php
/**
 * application_data_model_player2020
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
 *
 */
class model_player2020 extends model_base
{

	public function __construct()
	{
		parent::__construct('player2020');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $player2020_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($player2020_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($player2020_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'player2020',
			$filter = array
			(
				'player2020_id' => $player2020_id
			),
			$single = true
		);
	}
  
	/*
	 * get_entry_by_playername()
	 *
	 * @param String $playername
	 *
	 * @return Object
	 */
	public static function get_entry_by_playername($playername)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($player2020_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'player2020',
			$filter = array
			(
				'playername' => $playername
			),
			$single = true
		);
	}

		public static function get_all_sorted()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($ranking2020_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'player2020',
			$filter = array(),
			$single = false,
			$order_by = array("field" => "playername", "direction", "asc")
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
		return data_entry::get_all('player2020', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $player2020_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($player2020_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($player2020_id) betreten.");
		$db = database::get_instance();
		$player2020_id = $db->escape($player2020_id);
		$sql = "
			DELETE FROM `player2020` WHERE `player2020_id` = $player2020_id;
		";
		return $db->query($sql);
	}

}
