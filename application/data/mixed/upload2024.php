<?php
/**
 * application_data_mixed_upload2019
 *
 *
 *
 */
class mixed_upload2024 extends mixed_base
{
	public function __construct()
	{
		parent::__construct();
	}

	public static function get_hall_of_fame()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		$db = database::get_instance();
		$sql = "
      SELECT sum(u.points) as points, u.playername, p.awards, p.avatar, p.avatar_mime FROM upload2024 u
      LEFT JOIN `player2024` p USING (playername)
			WHERE p.awards IS NOT NULL AND
			p.awards != ''
      GROUP BY playername, awards, avatar, avatar_mime ORDER BY points DESC;
		";
		debug::add_info("sql:<br /><pre>$sql</pre>");
		return $db->get_objects($sql, __CLASS__);
	}
  
	public static function get_top_three_by_month($month)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		$db = database::get_instance();
    $month = $db->escape($month);
		$sql = "
      SELECT u.playername, u.position, p.avatar, p.avatar_mime FROM upload2024 u
      LEFT JOIN `player2024` p ON(u.playername = p.playername)
      WHERE u.month = $month
      AND table_ = 'gold'
      AND position < 4
      ORDER BY u.position ASC;
		";
		//debug::add_info("sql:<br /><pre>$sql</pre>");
		return $db->get_objects($sql, __CLASS__);
	}
	
	public static function get_top_three_by_month_2($month)
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		$db = database::get_instance();
    $month = $db->escape($month);
		$sql = "
      SELECT u.playername, u.position FROM upload2024 u
      WHERE u.month = $month
      AND table_ = 'gold'
      AND position < 4
      ORDER BY u.position ASC;
		";
		//debug::add_info("sql:<br /><pre>$sql</pre>");
		return $db->get_objects($sql, __CLASS__);
	}
	
	public static function get_general_ranking(){
		$db = database::get_instance();
		$sql = "
      SELECT sum(points) as points, playername FROM upload2024
			WHERE TRUE
      GROUP BY playername ORDER BY points DESC;
		";
		//debug::add_info("sql:<br /><pre>$sql</pre>");
		return $db->get_all_assoc($sql);
	}
	
	public static function get_ranking_by_month($month){
		$db = database::get_instance();
		$month = $db->escape($month);
		$sql = "
      SELECT sum(points) as points, playername FROM upload2024
			WHERE month = '$month'
      GROUP BY playername ORDER BY points DESC;
		";
		//debug::add_info("sql:<br /><pre>$sql</pre>");
		return $db->get_all_assoc($sql);
	}
	
	public static function get_points_by_player_month($player, $month){
		$db = database::get_instance();
		$player = $db->escape($player);
		$month = $db->escape($month);
		$sql = "
      SELECT sum(points) as points FROM upload2024
			WHERE 
			playername = '$player' AND
			month = '$month'
      GROUP BY playername ORDER BY points DESC;
		";
		//debug::add_info("sql:<br /><pre>$sql</pre>");
		return $db->get_assoc($sql);
	}
}

?>
