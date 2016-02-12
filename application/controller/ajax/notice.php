<?php
/**
 * Basis Klasse controller
 */
class controller_ajax_notice extends controller_ajax_base
{
	protected $output_type;

	public function __construct()
	{
		$this->output_type = "ajax";
	}

	public function run()
	{
		// debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		if(array_key_exists("notice", $_SESSION) && array_key_exists("default", $_SESSION['notice']) && strlen($_SESSION['notice']['default']) > 0)
		{
			app::$content['ajax'] = $_SESSION['notice']['default'];
			unset($_SESSION['notice']['default']);
		}
		else
		{
			app::$content['ajax'] = "none";
		}
		view::set_special("ajax", "browser/ajax/default.html");
		$this->generate_special_output($this->output_type);
	}

}
