<?php
/**
 * application_data_model_player2017
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
 *
 */
class model_player2017 extends model_base
{

	public function __construct()
	{
		parent::__construct('player2017');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $player2017_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($player2017_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($player2017_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'player2017',
			$filter = array
			(
				'player2017_id' => $player2017_id
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
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($player2017_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'player2017',
			$filter = array
			(
				'playername' => $playername
			),
			$single = true
		);
	}

		public static function get_all_sorted()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($ranking2017_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'player2017',
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
		return data_entry::get_all('player2017', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $player2017_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($player2017_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($player2017_id) betreten.");
		$db = database::get_instance();
		$player2017_id = $db->escape($player2017_id);
		$sql = "
			DELETE FROM `player2017` WHERE `player2017_id` = $player2017_id;
		";
		return $db->query($sql);
	}

}
