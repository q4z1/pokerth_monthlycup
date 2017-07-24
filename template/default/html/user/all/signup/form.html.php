<?php
/**
 * template_default_html_user_all_signup_form:
 *
 */
$cup_dates = json_decode(app::$settings["dates"]);
$i = intval(date("m"));
?>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-left form-group">
		<form name="signup" id="signup" action="<?=cfg::$web_root . 'ajax/signup/'?>" method="post">
		<fieldset>
			<legend class="text-primary">Registration for <?=date("F", strtotime($cup_dates->$i))?> Cup (scheduled for: <span class="text-success"><?=date("l, F jS Y", strtotime($cup_dates->$i))?>, 20:00:00 CEST)</span></legend>
		<!--<h3 class="text-primary">SignUps for December Cup (scheduled for: <span class="text-success"><?=date("l, F jS Y", strtotime("2016-12-23 20:00:00"))?>)</span></h3>-->
      <div class="row">
				<div class="col-md-12">
          <label for="playername">Playername:</label>
          <input type="text" name="playername" id="playername" class="form-control" />
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <br />
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 text-right">
					<span class="text-danger">Due to some same IP signups: Multiple signups with same IP will result in a randomly picked acceptance of only one registered account by a member of Orga Team!</span>&nbsp;&nbsp;
          <button class="btn btn-success" type="submit" name="submit" id="submit">Register</button>
        </div>
      </div>
    </fieldset>
    </form>
  </div>
</div>