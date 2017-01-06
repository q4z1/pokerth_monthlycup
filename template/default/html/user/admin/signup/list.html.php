<?php
/**
 * template_default_html_user_admin_signup_list
 *
 */
$cup_dates = json_decode(app::$settings["dates"]);
$i = intval(date("m"));
$list = app::$content['signups'];
?>
<div class="row">
	<div class="col-md-10 col-md-offset-1 text-center">
		<!--<h3 class="text-primary">SignUps for <?=date("F, Y")?></h3>-->
		<h3 class="text-primary">SignUps forfor <?=date("F", strtotime($cup_dates->$i))?> Cup (scheduled for: <span class="text-success"><?=date("l, F jS Y", strtotime($cup_dates->$i))?>)</span></h3>
	</div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <table class="table table-hover table-bordered table-striped signups">
      <thead>
        <tr>
					<th>No.</th>
          <th>Date</th>
          <th>Player</th>
          <th>IP</th>
					<th>Accepted</th>
					<th>Action</th>
        </tr>
      </thead>
      <tbody>
				<?php if(count($list) > 0): ?>
				<?php $i=0; ?>
				<?php foreach($list as $sup): ?>
				<?php $i++ ?>
				<?php $sid = "signup" . date("Y") . "_id"; ?>
				<tr>
					<td><?=$i?>.</td>
					<td><?=$sup->date?></td>
					<td><?=$sup->playername?></td>
					<td><?=$sup->ip?></td>
					<td><?=($sup->valid==1)?'Yes':'No'?></td>
					<td class="text-center">
						<button class="btn btn-danger removesup" type="submit" name="removesup" __sup_id__="<?=$sup->$sid?>">Remove</button>
						<?php if($sup->valid != 1): ?>
						<br /><button class="btn btn-success validatesup" type="submit" name="validatesup" __sup_id__="<?=$sup->$sid?>">Accept</button>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php else: ?>
				<tr>
					<td colspan="6" class="text-center text-info">No Signups found!</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
