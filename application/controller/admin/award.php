<?php
/**
 * application_controller_admin_default
 */
class controller_admin_award extends controller_admin_base
{
	protected $year;
	
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}

	public function run()
	{
		$this->year = (array_key_exists("year", app::$request)) ? app::$request["year"] : date("Y");
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		if(app::$session != 'admin')
		{
			app::$content['ajax_error'] = "Access only for admins!";
			view::set_special("ajax", "browser/error/ajax.html");
		}
		elseif(count(app::$param) > 0 && method_exists($this, app::$param[0]))
		{
			$this->{app::$param[0]}();
		}
		$this->generate_html_output();
	}
  
  public function upload()
  {
		view::set_col("maincol", "html/user/admin/award/upload.html");
  }
	
  public function edit()
  {
	
	view::set_col("maincol", "html/user/admin/award/edit.html");
    $cls = "model_award" . $this->year;
    app::$content['awards'] = $cls::get_all_entries();
    $cls = "model_player" . $this->year;
    app::$content['players'] = $cls::get_all_sorted();
  }
}