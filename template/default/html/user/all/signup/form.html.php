<?php
/**
 * template_default_html_user_all_signup_form:
 *
 */
$cup_dates = json_decode(app::$settings["dates"]);
$i = intval(date("m"));

$closeTime = strtotime($cup_dates->$i) - 60*60;

$show = time() < $closeTime ? true : false; 
?>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-left form-group">
		<form name="signup" id="signup" action="<?=cfg::$web_root . 'ajax/signup/'?>" method="post">
		<fieldset>
			<legend class="text-primary">Registration for <?=date("F", strtotime($cup_dates->$i))?> Cup (scheduled for: <span class="text-success"><?=date("l, F jS Y, H:i T", strtotime($cup_dates->$i))?>)</span></legend>
      <?php if ($show): ?>
      <div class="row">
				<div class="col-md-12">
          <label for="playername">Playername <span class="text-danger">(case-sensitive!)</span>:</label>
          <input type="text" name="playername" id="playername" class="form-control" />
        </div>
      </div>
      <?php else: ?>
        <div class="row">
				<div class="col-md-12">
          <h4 style="color: red">Registration is closed.<br /><br />
          Please leave a message in shoutbox, if you want to participate ... usually you will get a seat as substitute.</h4>
        </div>
      </div>        
      <?php endif; ?>
      <div class="row">
        <div class="col-md-12">
          <br />
        </div>
      </div>
      <?php if ($show): ?>
      <div class="row">
		<div class="col-md-6">&nbsp</div>
        <div class="col-md-6">
			<ul style="list-style-type: bullet;">
				<li><span class="text-warning">Due to some same IP signups: Multiple signups from same IP/Person will result in a randomly picked acceptance of only one registered account by a member of Orga Team!</span></li>
				<li><span class="text-danger"><strong>Double registrations and also not following admin instructions will be temporarily banned for pokerth until firstround cup games started.</strong></span></li>
				<li><span class="text-danger"><strong>If in a later review a double registration appears - both double/multiple registered players will get 0 points for participation.</strong></span></li>
			</ul>
        </div>
		<div class="col-md-6">&nbsp</div>
        <div class="col-md-6 text-right">
          <button class="btn btn-success" type="submit" name="submit" id="submit">Register</button>
        </div>
      </div>
      <?php endif; ?>
    </fieldset>
    </form>
  </div>
</div>
