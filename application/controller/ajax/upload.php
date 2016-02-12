<?php
/**
 * application_controller_ajax_default
 */
class controller_ajax_upload extends controller_ajax_base
{
	protected $output_type;

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
		$this->output_type = "ajax";
	}

	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		app::$content['ajax'] = "";
		if(app::$session != 'admin')
		{
			app::$content['ajax_error'] = "Access only for admins!";
			view::set_special("ajax", "browser/error/ajax.html");
		}
		elseif(count(app::$param) > 0 && method_exists($this, app::$param[0]))
		{
			$this->{app::$param[0]}();
		}
		$this->generate_special_output($this->output_type);
	}
  
  public function firstround()
  {
			view::set_special("ajax", "browser/ajax/modal.html");

			if(!array_key_exists("json", app::$request)){
					app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
					app::$content['modal']["content"] = "No json data given!";
					return;
			}
			
			$jObj = app::$request['json'];
			$month = $jObj->month;
			$table = $jObj->table;
			$players = $jObj->players;
			
			// check if upload is already done
			$imonth = 0;
			foreach($this->months as $key => $value){
				if($value == $month){
					$imonth = $key;
					break;
				}
			}

			$model_upload = "model_upload".date("Y");
			$month_done = $model_upload::check_firstround_entries_by_month_table($imonth, $table);
			if(!is_null($month_done)){
				app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
				app::$content['modal']["content"] = $month . " - firstround table " . $table . " was already uploaded!";
			
				return;
			}
			app::$content['modal']["content"] = "";
			for($i=1;$i<=10;$i++){
				if($players[$i] == ""){
					continue;
				}
				
				// check if player already exists
				$cls = "model_player" . date("Y");
				if(is_null($cls::get_entry_by_playername($players[$i])))
				{
					$pl = new $cls();
					$pl->playername = $players[$i];
					$pl->save();
				}
				
				$ul = new $model_upload;
				$ul->type = 'firstround';
				$ul->table_ = $table;
				$ul->month = $imonth;
				$ul->playername = $players[$i];
				$ul->position = $i;
				$ul->points = $this->points["first"][$i];
				$ul->save();
			}
			app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
			app::$content['modal']["content"] = $month . " - 1st round table " . $table . " successfully uploaded!";

  }
	
  public function finaltable()
  {
			view::set_special("ajax", "browser/ajax/modal.html");
			
			if(!array_key_exists("json", app::$request)){
					app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
					app::$content['modal']["content"] = "No json data given!";
					return;
			}
		
			$jObj = app::$request['json'];
			$month = $jObj->month;
			$table = $jObj->table;
			$players = $jObj->players;
			
			// check if upload is already done
			$imonth = 0;
			foreach($this->months as $key => $value){
				if($value == $month){
					$imonth = $key;
					break;
				}
			}

			$model_upload = "model_upload".date("Y");
			$month_done = $model_upload::check_final_entries_by_month_table($imonth, $table);
			if(!is_null($month_done)){
				app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
				app::$content['modal']["content"] = $month . " - final table " . $table . " was already uploaded!";
			
				return;
			}
			app::$content['modal']["content"] = "";
			for($i=1;$i<=10;$i++){
				if($players[$i] == ""){
					continue;
				}
				
				// check if player already exists
				$cls = "model_player" . date("Y");
				if(is_null($cls::get_entry_by_playername($players[$i])))
				{
					$pl = new $cls();
					$pl->playername = $players[$i];
					$pl->save();
				}
				
				$ul = new $model_upload;
				$ul->type = 'final';
				$ul->table_ = $table;
				$ul->month = $imonth;
				$ul->playername = $players[$i];
				$ul->position = $i;
				$ul->points = $this->points["final"][$table][$i];
				$ul->save();
			}
			app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
			app::$content['modal']["content"] = $month . " - final table " . $table . " successfully uploaded!";
  }
	
	public function award()
  {
		view::set_special("ajax", "browser/ajax/modal.html");
		
		if(!is_array($_FILES) || !array_key_exists("file", $_FILES)){
			app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
			app::$content['modal']["content"] = "No image file received!";
			return;
		}
		
		$blob = addslashes(file_get_contents($_FILES['file']['tmp_name']));
		$filename = $_FILES['file']['name'];
		$mime = $_FILES['file']['type'];
		
		$cls = "model_award" . date("Y");
		
		if(!is_null($cls::get_award_by_month_type(intval(app::$request['month']), app::$request['type'])))
		{
			app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
			app::$content['modal']["content"] = "This award has alread been uploaded!";
			return;
		}
		
		$award = new $cls();
		$award->month = intval(app::$request['month']);
		$award->type = app::$request['type'];
		$award->file = $blob;
		$award->filename = $filename;
		$award->mime = $mime;
		$award->save();
		
		unlink($_FILES['file']['tmp_name']);
		
		app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
		app::$content['modal']["content"] = "Award $filename successfully uploaded!";
	}
}
