<?php
/**
 * application_controller_main_ranking
 */
class controller_main_results extends controller_main_base
{
	protected $points = array(
		"first" => array(
			1 => 13,
			2 => 10,
			3 => 8,
			4 => 7,
			5 => 6,
			6 => 5,
			7 => 4,
			8 => 3,
			9 => 2,
			10 => 1
		),
		
		"final" => array(
			"bronze" => array(
				1 => 16,
				2 => 11,
				3 => 8,
				4 => 6,
				5 => 5,
				6 => 4,
				7 => 3,
				8 => 2,
				9 => 1,
				10 => 0
			),
			"silver" => array(
				1 => 24,
				2 => 18,
				3 => 14,
				4 => 11,
				5 => 9,
				6 => 7,
				7 => 5,
				8 => 3,
				9 => 2,
				10 => 1
			),
			"gold" => array(
				1 => 36,
				2 => 26,
				3 => 22,
				4 => 17,
				5 => 13,
				6 => 10,
				7 => 7,
				8 => 5,
				9 => 3,
				10 => 1
			)
		)
	);
	
	protected  $months = array(
		"01" => "January",
		"02" => "February",
		"03" => "March",
		"04" => "April",
		"05" => "May",
		"06" => "June",
		"07" => "July",
		"08" => "August",
		"09" => "October",
		"10" => "September",
		"11" => "November",
		"12" => "December",
	 );
	
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}

	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		if(count(app::$param) > 0 && method_exists($this, app::$param[0]))
		{
			$this->{app::$param[0]}();
		}
    else{
      $this->results();
    }
		$this->generate_html_output();
	}
  
  public function results()
  {
			view::set_col("maincol", "html/user/all/results/results.html");
			$cls = "mixed_upload".date("Y");
			
			$month = intval(Date("m"));
			
			$top3 = array();
			for($i=1;$i<=$month;$i++){
				$top3[$i] = $cls::get_top_three_by_month($i);
			}
			
			app::$content["top3"] = $top3;
			

  }
  
  public function halloffame()
  {
			view::set_col("maincol", "html/user/all/results/halloffame.html");
			$cls = "mixed_upload".date("Y");
			$plyrs = $cls::get_hall_of_fame();
			$plyr2 = array();
			foreach($plyrs as $pl){
				$awards = array();
				$awds = json_decode($pl->awards);
				foreach($awds as $awd){
					$cls = "model_award" . date("Y");
					$award = $cls::get_entry_by_month_type($awd->month, $awd->type);
					$awards[] = '<img src="data:'.$award->mime.';base64,'.base64_encode(stripslashes($award->file)).'" alt="Award '.$awd->type.'">';
				}
				$pl->awds = $awards;
				$plyr2[] = $pl;
			}
			
			app::$content['ranking'] = $plyr2;
  }
	
  public function lastmonth()
  {
			view::set_col("maincol", "html/user/all/results/lastmonth.html");
			$last_month = date("m")-1;
			if($last_month < 1){
			 $last_month = 1;
			}
			$class = "model_upload" . date("Y");
			$standings = $class::get_all_entries_by_month($last_month);
			app::$content["standings"] = $standings;
  }
	
  public function points()
  {
		// switch array from rows to columns
		$npoints = array();
		
		foreach($this->points as $type => $points){
			if($type == "first"){
				foreach($points as $pos => $pts){
					$npoints[$pos][$type] = $pts;
				}
			}else{
				foreach($points as $typ => $pt){
					foreach($pt as $i => $p){
						$npoints[$i][$typ] = $p;
					}
				}
			}
		}
		
		app::$content['points'] = $npoints;
		view::set_col("maincol", "html/user/all/results/points.html");
  }

	
  public function rankings()
  {
			view::set_col("maincol", "html/user/all/results/rankings.html");

			$cls = "mixed_upload".date("Y");
			$gen = $cls::get_general_ranking();
			$gen2 = array();
			foreach($gen as $rank){
				$rank["months"] = array();
				for($i=1;$i<=intval(date("m"));$i++){
					$m = $cls::get_points_by_player_month($rank['playername'], $i);
					$rank['months'][$i] = $m;
				}
				$gen2[] = $rank;
			}
			app::$content['general'] = $gen2;
			
			$monthly = array();
			for($i=1;$i<=intval(date("m"));$i++){
				$month = date("F", strtotime(date("Y-$i-1")));
				$monthly[$month] = $cls::get_ranking_by_month($i);
			}
			app::$content['monthly'] = $monthly;
  }
	
}
