<?php
/**
 * template_default_html_user_admin_signup_list
 *
 */
$list = app::$content['signups'];
?>
<div class="row">
	<div class="col-md-10 col-md-offset-1 text-center">
		<h3 class="text-primary">SignUps for <?=date("F, Y")?></h3>
	</div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <table class="table table-hover table-bordered table-striped">
      <thead>
        <tr>
					<th>ID</th>
          <th>Date</th>
          <th>Player</th>
          <th>IP</th>
					<th>Action</th>
        </tr>
      </thead>
      <tbody>
				<?php if(count($list) > 0): ?>
				<?php foreach($list as $sup): ?>
				<?php $sid = "signup" . date("Y") . "_id"; ?>
				<tr>
					<td><?=$sup->$sid?></td>
					<td><?=$sup->date?></td>
					<td><?=$sup->playername?></td>
					<td><?=$sup->ip?></td>
					<td>
						<button class="btn btn-danger removesup" type="submit" name="removesup" __sup_id__="<?=$sup->$sid?>">Remove</button>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php else: ?>
				<tr>
					<td colspan="5" class="text-center text-info">No Signups found!</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
