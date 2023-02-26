<?php
/**
 * application_controller_ajax_signup
 */
class controller_ajax_signup extends controller_ajax_base
{
	protected $output_type;
	
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
		if(count(app::$param) > 0 && method_exists($this, app::$param[0]))
		{
			$this->{app::$param[0]}();
		}else{
      $this->do_signup();
    }
		$this->generate_special_output($this->output_type);
	}
  
  public function do_signup(){
    view::set_special("ajax", "browser/ajax/modal.html");
    if(!array_key_exists("playername", app::$request) && app::$request['playername'] == ""){
      app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
      app::$content['modal']["content"] = "No playername given!";
      return;
    }
	$cls = "model_signup" . date("Y");
	
	$sup = $cls::get_entry_by_month_playername(intval(date("m")), trim(app::$request['playername']));
	// @XXX: temporary static month value
	// $sup = $cls::get_entry_by_month_playername(12, app::$request['playername']);
	
    if(!is_null($sup)){
      app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
      app::$content['modal']["content"] = "The player <strong>" . app::$request['playername'] . "</strong> is already registered!";
      return;
    }
    
	$cls = "model_signup" . date("Y");
    $sup = new $cls();
    $sup->playername = trim(app::$request['playername']);
    $sup->date = date("Y-m-d H:i:s");
	
    $sup->month = intval(date("m"));
    
	// @XXX: temporary static month value
	// $sup->month = 12;
	
	$sup->fp = array_key_exists('fp', app::$request) ? app::$request['fp'] : null;
	
	$sup->fpnew = array_key_exists('fpnew', app::$request) ? app::$request['fpnew'] : null;
	
	$sup->ip = $_SERVER['REMOTE_ADDR'];
	// @XXX: cloudflare
	if(array_key_exists("HTTP_CF_CONNECTING_IP", $_SERVER)) $sup->ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
    $sup->save();
    
    app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
    app::$content['modal']["content"] = "Thank you <strong>" . htmlentities(app::$request["playername"]) . "</strong> - you have registered!";
  }
	
	public function validate()
	{
			view::set_special("ajax", "browser/ajax/modal.html");

			$id = app::$request['id'];

	  $cls = "model_signup" . date("Y");
      $sup = $cls::get_entry_by_id($id);
      if(is_null($sup)){
				app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
				app::$content['modal']["content"] = "A Signupd with id $id not found!";
				return;
      }

			// validate player      
      $sup->valid = 1;
			$sup->save();
			
			app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
			app::$content['modal']["content"] = "The Signup with id $id has been accepted!";
	}
	
	
	public function delete()
	{
			view::set_special("ajax", "browser/ajax/modal.html");

			$id = app::$request['id'];

	  $cls = "model_signup" . date("Y");
      $sup = $cls::get_entry_by_id($id);
      if(is_null($sup)){
				app::$content['modal']["heading"] = "<div class='text-danger'>Fail!</div>";
				app::$content['modal']["content"] = "A Signupd with id $id not found!";
				return;
      }
      
      // @TODO: remove assignments from each player !!!
      
      $cls::delete_entry_by_id($id);
			app::$content['modal']["heading"] = "<div class='text-success'>Success!</div>";
			app::$content['modal']["content"] = "The Signup with id $id has been deleted!";
	}
}
