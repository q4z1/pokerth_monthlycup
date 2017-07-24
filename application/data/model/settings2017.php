<?php
/**
 * application_data_model_settings
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
 *
 */
class model_settings2017 extends model_base
{

	public function __construct()
	{
		parent::__construct('settings2017');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $settings_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($settings_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($settings_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'settings2017',
			$filter = array
			(
				'settings2017_id' => $settings_id
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
		return data_entry::get_all('settings2017', __CLASS__);
	}
  
	public static function get_all_entries_assoc()
	{
		$db = database::get_instance();
		$sql = "
			SELECT * FROM `settings2017`;
		";
		return $db->get_all_assoc($sql);
	}
	/*
	 * delete_entry_by_id()
	 *
	 * @param String $settings_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($settings_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($settings_id) betreten.");
		$db = database::get_instance();
		$settings_id = $db->escape($settings_id);
		$sql = "
			DELETE FROM `settings2017` WHERE `settings2017_id` = $settings_id;
		";
		return $db->query($sql);
	}

}
