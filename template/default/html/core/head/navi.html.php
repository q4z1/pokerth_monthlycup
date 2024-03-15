<?php

/**
 * template_default_html_core_leftcol_navi:
 *
 */
$last_month = date("m") - 1;
if ($last_month < 1) {
	$last_month = 1;
}

?>
<style>
	.dropdown-menu>li {
		position: relative;
		-webkit-user-select: none;
		/* Chrome/Safari */
		-moz-user-select: none;
		/* Firefox */
		-ms-user-select: none;
		/* IE10+ */
		/* Rules below not implemented in browsers yet */
		-o-user-select: none;
		user-select: none;
		cursor: pointer;
	}

	.dropdown-menu .sub-menu {
		left: 100%;
		position: absolute;
		top: 0;
		display: none;
		margin-top: -1px;
		border-top-left-radius: 0;
		border-bottom-left-radius: 0;
		border-left-color: #fff;
		box-shadow: none;
	}

	.right-caret:after {
		content: "";
		border-bottom: 4px solid transparent;
		border-top: 4px solid transparent;
		border-left: 4px solid;
		display: inline-block;
		height: 0;
		opacity: 0.8;
		vertical-align: middle;
		width: 0;
		margin-left: 5px;
	}

	.left-caret:after {
		content: "";
		border-bottom: 4px solid transparent;
		border-top: 4px solid transparent;
		border-right: 4px solid;
		display: inline-block;
		height: 0;
		opacity: 0.8;
		vertical-align: middle;
		width: 0;
		margin-left: 5px;
	}
</style>
<div id="headnavi">
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bbc-navbar-all" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bbc-navbar-all">
				<ul class="nav navbar-nav">
					<li><a href="/">Home</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Uploads<span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="/admin/upload/firstroundtable/">1st Round</a></li>
							<li><a href="/admin/upload/finaltable/">Final</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">Results
							<span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li>
								<a class="trigger right-caret" href="#">Archive 2021</a>
								<ul class="dropdown-menu sub-menu">
									<?php for ($i = 1; $i <= 12; $i++) : ?>
										<li><a href="/main/results/cup/<?= $i ?>/?year=2021"><?= date("F", strtotime(date("2021-") . ($i) . "-01")) ?> Cup Standings</a></li>
									<?php endfor; ?>
									<li role="separator" class="divider"></li>
									<li><a href="/main/results/series/?year=2021">Series Results</a></li>
									<li><a href="/main/results/rankings/?year=2021">Series Rankings</a></li>
									<li><a href="/main/results/halloffame/?year=2021">Hall-Of-Fame</a></li>
									<li><a href="/main/results/points/?year=2021">Cup Ranking Points</a></li>
								</ul>
							</li>
							<li>
								<a class="trigger right-caret" href="#">Archive 2022</a>
								<ul class="dropdown-menu sub-menu">
									<?php for ($i = 1; $i <= 12; $i++) : ?>
										<li><a href="/main/results/cup/<?= $i ?>/?year=2022"><?= date("F", strtotime(date("2022-") . ($i) . "-01")) ?> Cup Standings</a></li>
									<?php endfor; ?>
									<li role="separator" class="divider"></li>
									<li><a href="/main/results/series/?year=2022">Series Results</a></li>
									<li><a href="/main/results/rankings/?year=2022">Series Rankings</a></li>
									<li><a href="/main/results/halloffame/?year=2022">Hall-Of-Fame</a></li>
									<li><a href="/main/results/points/?year=2022">Cup Ranking Points</a></li>
								</ul>
							</li>
							<li>
								<a class="trigger right-caret" href="#">Archive 2023</a>
								<ul class="dropdown-menu sub-menu">
									<?php for ($i = 1; $i <= 12; $i++) : ?>
										<li><a href="/main/results/cup/<?= $i ?>/?year=2023"><?= date("F", strtotime(date("2023-") . ($i) . "-01")) ?> Cup Standings</a></li>
									<?php endfor; ?>
									<li role="separator" class="divider"></li>
									<li><a href="/main/results/series/?year=2023">Series Results</a></li>
									<li><a href="/main/results/rankings/?year=2023">Series Rankings</a></li>
									<li><a href="/main/results/halloffame/?year=2023">Hall-Of-Fame</a></li>
									<li><a href="/main/results/points/?year=2023">Cup Ranking Points</a></li>
								</ul>
							</li>
							<li role="separator" class="divider"></li>
							<?php for ($i = 1; $i <= intval(date("m")); $i++) : ?>
								<li><a href="/main/results/cup/<?= $i ?>/"><?= date("F", strtotime(date("Y-") . ($i) . "-01")) ?> Cup Standings</a></li>
							<?php endfor; ?>
							<li role="separator" class="divider"></li>
							<li><a href="/main/results/series/">Series Results</a></li>
							<li><a href="/main/results/rankings/">Series Rankings</a></li>
							<li><a href="/main/results/halloffame/">Hall-Of-Fame</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">Awards
							<span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="/admin/award/upload/">Upload</a></li>
							<li><a href="/admin/award/edit/">Assign / Edit</a></li>
						</ul>
					</li>
					<li><a href="/admin/signup/">Signups</a></li>
					<li><a href="/admin/signup/randomizer/">Randomizer</a></li>
					<li><a href="/main/results/points/">Cup Ranking Points</a></li>
					<li><a href="/main/settings/">Table Settings</a></li>
					<li><a href="/admin/settings/">Admin Settings</a></li>
					<li><a href="/main/logout/">Logout</a></li>
				</ul>
			</div>
		</div>
	</nav>
</div>
<script>
	document.onreadystatechange = function() {
		if (document.readyState === 'complete') {
			$(function() {
				$(".dropdown-menu > li > a.trigger").on("click", function(e) {
					var current = $(this).next();
					var grandparent = $(this).parent().parent();
					if ($(this).hasClass('left-caret') || $(this).hasClass('right-caret'))
						$(this).toggleClass('right-caret left-caret');
					grandparent.find('.left-caret').not(this).toggleClass('right-caret left-caret');
					grandparent.find(".sub-menu:visible").not(current).hide();
					current.toggle();
					e.stopPropagation();
				});
				$(".dropdown-menu > li > a:not(.trigger)").on("click", function() {
					var root = $(this).closest('.dropdown');
					root.find('.left-caret').toggleClass('right-caret left-caret');
					root.find('.sub-menu:visible').hide();
				});
			});
		}
	}
</script>
