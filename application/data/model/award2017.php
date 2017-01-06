<?php
/**
 * application_data_model_award2017
 *
 * Stellt alle Daten der Tabelle Moderator zur Verfügung
 *
 */
class model_award2017 extends model_base
{

	public function __construct()
	{
		parent::__construct('award2017');
	}

	/*
	 * get_entry_by_id()
	 *
	 * @param String $award2017_id
	 *
	 * @return Object
	 */
	public static function get_entry_by_id($award2017_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($award2017_id) betreten.");
		return data_entry::get_by_filter
		(
			$table = 'award2017',
			$filter = array
			(
				'award2017_id' => $award2017_id
			),
			$single = true
		);
	}
	
	public static function get_entry_by_month_type($month, $type){
    if($type == "admin" || $type == "rank1st" || $type =="rank2nd" || $type =="rank3rd" || $type == "top20"){
      return data_entry::get_by_filter
      (
        $table = 'award2017',
        $filter = array
        (
          'type' => $type,
        ),
        $single = true
      );
    }
    else{
      return data_entry::get_by_filter
      (
        $table = 'award2017',
        $filter = array
        (
          'type' => $type,
          'month' => intval($month)
        ),
        $single = true
      );
    }
	}
	
	public static function get_all_entries_by_month($month){
		return data_entry::get_by_filter
		(
			$table = 'award2017',
			$filter = array
			(
				'month' => intval($month),
			),
			$single = false
		);
	}
	
	public static function get_award_by_month_type($month, $type){
		return data_entry::get_by_filter
		(
			$table = 'award2017',
			$filter = array
			(
				'month' => intval($month),
				'type' => $type
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
		return data_entry::get_all('award2017', __CLASS__);
	}

	/*
	 * delete_entry_by_id()
	 *
	 * @param String $award2017_id
	 *
	 * @return Object
	 */
	public static function delete_entry_by_id($award2017_id)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."($award2017_id) betreten.");
		$db = database::get_instance();
		$award2017_id = $db->escape($award2017_id);
		$sql = "
			DELETE FROM `award2017` WHERE `award2017_id` = $award2017_id;
		";
		return $db->query($sql);
	}

}
