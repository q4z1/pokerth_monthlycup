<?php
/**
 * template_default_html_user_all_signup_list
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
    <table class="table table-hover table-bordered table-striped signups">
      <thead>
        <tr>
          <th>Player</th>
        </tr>
      </thead>
      <tbody>
				<?php if(count($list) > 0): ?>
				<?php foreach($list as $sup): ?>
				<tr>
					<td><?=$sup->playername?></td>
				</tr>
				<?php endforeach; ?>
				<?php else: ?>
				<tr>
					<td class="text-center text-info">No Signups found!</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
