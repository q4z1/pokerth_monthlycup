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
 //$last_month = 2;
 
?>
<style>
.dropdown-menu>li{
	position:relative;
	-webkit-user-select: none; /* Chrome/Safari */        
	-moz-user-select: none; /* Firefox */
	-ms-user-select: none; /* IE10+ */
	/* Rules below not implemented in browsers yet */
	-o-user-select: none;
	user-select: none;
	cursor:pointer;
}
.dropdown-menu .sub-menu {
	left: 100%;
	position: absolute;
	top: 0;
	display:none;
	margin-top: -1px;
	border-top-left-radius:0;
	border-bottom-left-radius:0;
	border-left-color:#fff;
	box-shadow:none;
}
.right-caret:after{
	content:"";
	border-bottom: 4px solid transparent;
	border-top: 4px solid transparent;
	border-left: 4px solid;
	display: inline-block;
	height: 0;
	opacity: 0.8;
	vertical-align: middle;
	width: 0;
	margin-left:5px;
}
.left-caret:after{
	content:"";
	border-bottom: 4px solid transparent;
	border-top: 4px solid transparent;
	border-right: 4px solid;
	display: inline-block;
	height: 0;
	opacity: 0.8;
	vertical-align: middle;
	width: 0;
	margin-left:5px;
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
						<li class="dropdown" style="position:relative">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">Results
							<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li>
									<a class="trigger right-caret" href="#">Archive 2016</a>
									<ul class="dropdown-menu sub-menu">
										<?php for($i=1; $i<=12; $i++): ?>
										<li><a href="/main/results/cup/<?=$i?>/?year=2016"><?=date("F", strtotime(date("2016-").($i)."-01"))?> Cup Standings</a></li>
										<?php endfor; ?>
										<li role="separator" class="divider"></li>
										<li><a href="/main/results/series/?year=2016">Series Results</a></li>
										<li><a href="/main/results/rankings/?year=2016">Series Rankings</a></li>
										<li><a href="/main/results/halloffame/?year=2016">Hall-Of-Fame</a></li>
										<li><a href="/main/results/points/?year=2016">Cup Ranking Points</a></li>
									</ul>
								</li>
								<li>
									<a class="trigger right-caret" href="#">Archive 2017</a>
									<ul class="dropdown-menu sub-menu">
										<?php for($i=1; $i<=12; $i++): ?>
										<li><a href="/main/results/cup/<?=$i?>/?year=2017"><?=date("F", strtotime(date("2017-").($i)."-01"))?> Cup Standings</a></li>
										<?php endfor; ?>
										<li role="separator" class="divider"></li>
										<li><a href="/main/results/series/?year=2017">Series Results</a></li>
										<li><a href="/main/results/rankings/?year=2017">Series Rankings</a></li>
										<li><a href="/main/results/halloffame/?year=2017">Hall-Of-Fame</a></li>
										<li><a href="/main/results/points/?year=2017">Cup Ranking Points</a></li>
									</ul>
								</li>
								<li>
									<a class="trigger right-caret" href="#">Archive 2018</a>
									<ul class="dropdown-menu sub-menu">
										<?php for($i=1; $i<=12; $i++): ?>
										<li><a href="/main/results/cup/<?=$i?>/?year=2018"><?=date("F", strtotime(date("2018-").($i)."-01"))?> Cup Standings</a></li>
										<?php endfor; ?>
										<li role="separator" class="divider"></li>
										<li><a href="/main/results/series/?year=2018">Series Results</a></li>
										<li><a href="/main/results/rankings/?year=2018">Series Rankings</a></li>
										<li><a href="/main/results/halloffame/?year=2018">Hall-Of-Fame</a></li>
										<li><a href="/main/results/points/?year=2018">Cup Ranking Points</a></li>
									</ul>
								</li>
								<li>
									<a class="trigger right-caret" href="#">Archive 2019</a>
									<ul class="dropdown-menu sub-menu">
										<?php for($i=1; $i<=12; $i++): ?>
										<li><a href="/main/results/cup/<?=$i?>/?year=2019"><?=date("F", strtotime(date("2019-").($i)."-01"))?> Cup Standings</a></li>
										<?php endfor; ?>
										<li role="separator" class="divider"></li>
										<li><a href="/main/results/series/?year=2019">Series Results</a></li>
										<li><a href="/main/results/rankings/?year=2019">Series Rankings</a></li>
										<li><a href="/main/results/halloffame/?year=2019">Hall-Of-Fame</a></li>
										<li><a href="/main/results/points/?year=2019">Cup Ranking Points</a></li>
									</ul>
								</li>
								<li role="separator" class="divider"></li>
								<?php for($i=1; $i<=intval(date("m")); $i++): ?>
								<li><a href="/main/results/cup/<?=$i?>/"><?=date("F", strtotime(date("Y-").($i)."-01"))?> Cup Standings</a></li>
								<?php endfor; ?>
								<li role="separator" class="divider"></li>
								<li><a href="/main/results/series/">Series Results</a></li>
								<li><a href="/main/results/rankings/">Series Rankings</a></li>
								<li><a href="/main/results/halloffame/">Hall-Of-Fame</a></li>
							</ul>
						</li>
						<li><a href="/main/signup/">Registration</a></li>
						<li><a href="/main/signup/show/">Signups</a></li>
						<li><a href="/main/results/points/">Cup Ranking Points</a></li>
						<li><a href="/main/settings/">Table Settings</a></li>
					</ul>
			</div>
  </div>
 </nav>
</div>
<script>
	document.onreadystatechange = function() {
  if (document.readyState === 'complete') {
			$(function(){
				console.log("event fired");
				$(".dropdown-menu > li > a.trigger").on("click",function(e){
					console.log("archive clicked");
					var current=$(this).next();
					var grandparent=$(this).parent().parent();
					if($(this).hasClass('left-caret')||$(this).hasClass('right-caret'))
						$(this).toggleClass('right-caret left-caret');
					grandparent.find('.left-caret').not(this).toggleClass('right-caret left-caret');
					grandparent.find(".sub-menu:visible").not(current).hide();
					current.toggle();
					e.stopPropagation();
				});
				$(".dropdown-menu > li > a:not(.trigger)").on("click",function(){
					var root=$(this).closest('.dropdown');
					root.find('.left-caret').toggleClass('right-caret left-caret');
					root.find('.sub-menu:visible').hide();
				});
			});
		}
	}
</script>
