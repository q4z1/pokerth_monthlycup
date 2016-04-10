<?php
/**
 * template_default_html_core_leftcol_navi:
 *
 */
 $last_month = date("m")-1;
 if($last_month < 1){
  $last_month = 1;
 }
 
  // @XXX: temporary february results
 $last_month = 2;
 
?>
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
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Results
			<span class="caret"></span></a>
			<ul class="dropdown-menu">
			 <?php for($i=1; $i<=intval(date("m")); $i++): ?>
			 <li><a href="/main/results/cup/<?=$i?>/"><?=date("F", strtotime(date("Y-").($i)."-01"))?> Cup Standings</a></li>
			 <?php endfor; ?>
			 <li role="separator" class="divider"></li>
			 <li><a href="/main/results/series/">Series Results</a></li>
			 <li><a href="/main/results/rankings/">Series Rankings</a></li>
			 <li><a href="/main/results/halloffame/">Hall-Of-Fame</a></li>
			 <li><a href="/main/results/points/">Cup Ranking Points</a></li>
			</ul>
		</li>
		<li><a href="/main/signup/">Registration</a></li>
		<li><a href="/main/signup/show/">Signups</a></li>
	  </ul>
	</div>
  </div>
 </nav>
</div>

