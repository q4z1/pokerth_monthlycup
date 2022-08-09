<?php
/**
 * template_default_html_user_all_results_rankings:
 *
 */

$gen = app::$content["general"];
$monthly = app::$content['monthly'];
?>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h3 class="text-primary">Rankings <?=($this->year == date("Y")) ? date("Y") : $this->year?></h3>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h4 class="text-warning">Overview</h4>
  </div>
</div>
<div class="row">
  <div class="col-md-2 col-md-offset-5 text-center">
    <ul class="nav nav-tabs nav-justified">
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Month
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <?php if($this->year == date("Y")): ?>
          <?php for($i=1;$i<=intval(date("m"));$i++): ?>
          <?php $month = date("F", strtotime(date("Y-$i-1"))); ?>
          <?php if(!is_null($monthly[$month])): ?>
            <li><a href="#<?=strtolower($month)?>"><?=$month?> Cup</a></li>
          <?php endif; ?>
          <?php endfor; ?>
          <?php else: ?>
          <?php for($i=1;$i<=12;$i++): ?>
          <?php $month = date("F", strtotime(date("Y-$i-1"))); ?>
          <?php if(!is_null($monthly[$month])): ?>
            <li><a href="#<?=strtolower($month)?>"><?=$month?> Cup</a></li>
          <?php endif; ?>
          <?php endfor; ?>
          <?php endif; ?>
        </ul>
      </li>
    </ul>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <hr />
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h4 class="text-success">General Ranking</h4>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <table class="table table-hover table-bordered table-striped">
      <thead>
        <tr>
          <th>Rank</th>
          <th>Player</th>
          <th>Total CRPs</th>
          <?php for($i=1;$i<=intval(date("m"));$i++): ?>
          <th><?=date("F", strtotime(date("Y-$i-1")))?> Cup</th>
          <?php endfor; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($gen as $i => $plyr): ?>
        <tr>
          <td><?=($i+1)?></td>
          <td><a target="_blank" href="https://www.pokerth.net/player?u=<?=$plyr['playername']?>"><?=$plyr['playername']?></a></td>
          <td><?=$plyr['points']?></td>
          <?php $months = $plyr["months"]; ?>
          <?php foreach($months as $m => $month): ?>
          <?php if(!is_null($month) && is_array($month) && count($month) > 0): ?>
          <td><?=$month['points']?></td>
          <?php else: ?>
          <td>&nbsp;</td>
          <?php endif; ?>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <hr />
  </div>
</div>
<?php foreach($monthly as $month => $players): ?>
<?php if(is_null($players)): ?>
<?php continue; ?>
<?php endif; ?>
<div class="row" id="<?=strtolower($month)?>">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h4 class="text-success"><?=$month?> Cup</h4>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <table class="table table-hover table-bordered table-striped">
      <thead>
        <tr>
          <th>Rank</th>
          <th>Player</th>
          <th><?=$month?> CRPs</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($players as $i => $plyr): ?>
        <tr>
          <td><?=($i+1)?></td>
          <td><a target="_blank" href="https://www.pokerth.net/player?u=<?=$plyr['playername']?>"><?=$plyr['playername']?></a></td>
          <td><?=$plyr['points']?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <hr />
  </div>
</div>
<?php endforeach; ?>
