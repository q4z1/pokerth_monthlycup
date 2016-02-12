<?php
/**
 * template_default_html_core_leftcol_navi:
 *
 */
 $last_month = date("m")-1;
 if($last_month < 1){
  $last_month = 1;
 }
?>
<div id="headnavi">
	<ul class="nav nav-tabs nav-justified">
		<li><a href="/">Home</a></li>
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Results
			<span class="caret"></span></a>
			<ul class="dropdown-menu">
        <li><a href="/main/results/lastmonth/"><?=date("F", strtotime(date("Y-").($last_month)."-01"))?> Standings</a></li>
        <li><a href="/main/results/results/">Series Results</a></li>
        <li><a href="/main/results/rankings/">Series Rankings</a></li>
        <li><a href="/main/results/halloffame/">Hall-Of-Fame</a></li>
        <li><a href="/main/results/points/">Cup Ranking Points</a></li>
			</ul>
		</li>
	</ul>
</div>

