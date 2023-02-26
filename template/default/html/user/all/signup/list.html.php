<?php
/**
 * template_default_html_user_all_signup_list
 *
 */
$cup_dates = json_decode(app::$settings["dates"]);
$i = intval(date("m"));
$list = array_key_exists('signups', app::$content) ? app::$content['signups'] : null;
$subs = app::$content['subs'];
?>
<div class="row">
	<div class="col-md-10 col-md-offset-1 text-center">
		<h3 class="text-primary">SignUps for <?=date("F", strtotime($cup_dates->$i))?> Cup (scheduled for: <span class="text-success"><?=date("l, F jS Y, H:i T", strtotime($cup_dates->$i))?>)</span></h3>
		<!--<h3 class="text-primary">SignUps for December Cup (scheduled for: <span class="text-success"><?=date("l, F jS Y", strtotime("2016-12-23 20:00:00"))?>)</span></h3>-->
	</div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <table class="table table-hover table-bordered table-striped signups">
      <thead>
        <tr>
					<th class="col-md-1">No.</th>
          <th class="col-md-11">Player</th>
        </tr>
      </thead>
      <tbody>
				<?php if(count((array)$list) > 0): ?>
				<?php foreach($list as $i =>$sup): ?>
				<tr>
					<td><?=($i+1)?>.</td>
					<td><a target="_blank" href="https://www.pokerth.net/player?u=<?=$sup->playername?>"><?=$sup->playername?></a></td>
				</tr>
				<?php endforeach; ?>
				<?php if(count($subs) > 0): ?>
				<tr>
					<td colspan="2" class="text-center">
						<div class="row">
							<div class="col-md-8 col-md-offset-2">
								<h4 class="text-info">Substitutes:</h4>
								<p>
								<?php foreach($subs as $i => $sub): ?>
									<?=$sub->playername?><?=($i<count($subs)-1)?', ': ''?>
								<?php endforeach; ?>
								</p>
							</div>
						</div>
					</td>
				</tr>
				<?php endif; ?>
				<?php else: ?>
				<tr>
					<td class="text-center text-info" colspan="2">No Signups found!</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
