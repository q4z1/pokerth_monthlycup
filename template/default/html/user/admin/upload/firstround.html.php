<?php

/**
 * template_default_html_admin_upload_firstround
 */

$months = array(
	"01" => "January",
	"02" => "February",
	"03" => "March",
	"04" => "April",
	"05" => "May",
	"06" => "June",
	"07" => "July",
	"08" => "August",
	"09" => "September",
	"10" => "October",
	"11" => "November",
	"12" => "December",
);
$aMonth = date("m");
?>
<div class="row">
	<div class="uploadform col-md-10 col-md-offset-1 form-group">
		<form name="firstround" id="firstround" action="<?= cfg::$web_root . 'ajax/upload/firstround' ?>" method="post">
			<fieldset>
				<legend class="text-primary">Upload 1st round table:</legend>
				<div class="row">
					<div class="col-md-6">
						<label for="month">Month:</label><br>
						<select name="month" id="month" class="form-control">
							<?php foreach ($months as $key => $month) : ?>
								<option value="<?= $month; ?>" <?= ($aMonth == $key) ? 'selected="selected"' : '' ?>><?= $month ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-6">
						<label for="table">Table:</label><br>
						<select name="table" id="table" class="form-control">
							<?php for ($i = 1; $i <= 10; $i++) : ?>
								<option value="<?= $i; ?>"><?= $i ?></option>
							<?php endfor; ?>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<label for="url">Log-Link:</label><br>
						<input type="text" name="url" id="logLink" class="form-control"
						placeholder="https://pokerth.net/gamelog?pdb=1234567890abcdef&game_id=1" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<hr />
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 text-right">
						<button class="btn btn-primary" type="submit" name="submit" id="submit">Submit</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
</div>