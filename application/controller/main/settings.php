<?php
/**
 * application_controller_main_settings
 */
class controller_main_settings extends controller_main_base
{
	
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}

	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
      $this->show_settings();
		$this->generate_html_output();
	}
	
	public function show_settings()
	{
		view::set_col("maincol", "html/user/all/settings/default.html");
	}
}