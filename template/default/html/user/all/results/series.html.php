<?php
/**
 * template_default_html_user_all_results_results:
 *
 */

$top3 = app::$content["top3"];

$cup_dates = json_decode(app::$settings["dates"]);
$forum_links = array_key_exists("forum_links", app::$settings) ? json_decode(app::$settings["forum_links"]) : false;

?>
<div class="row">
  <div class="col-md-10 col-md-offset-1 text-center">
    <h3 class="text-primary">Series <?=app::$content["year"]?> Results</h3>
  </div>
</div>
<?php for($i=1; $i<=count($top3); $i++): ?>
<?php if(!is_null($top3[$i])): ?>
<div class="row">
  <div class="col-md-6 col-md-offset-3 text-center">
    <h4 class="text-success"><?=date("F", strtotime(date("Y-$i-01")));?> Cup</h4>
  </div>
  <div class="col-md-6 col-md-offset-3 text-center">
    <h6 class="text-warning"><?=date("l, F jS Y", strtotime($cup_dates->$i))?> Cup</h6>
  </div>
  <div class="col-md-6 col-md-offset-3 text-center">
    <?php if($forum_links): ?>
    <h6 class="text-primary"><a href="<?=$forum_links->$i?>" target="_blank">Forum thread for all results</a></h6>
  </div>
  <?php endif; ?>
</div>
<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">
    <br />
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">
    <div class="row">
      <div class="col-md-4 text-center">
        <img src="/res/award/?type=gold1st&month=<?=$i?>&year=<?=$this->year?>" alt="Gold 1st" />
        <br />
        <strong><a target="_blank" href="https://www.pokerth.net/player?u=<?=$top3[$i][0]->playername?>"><?=$top3[$i][0]->playername?></a></strong>
      </div>
      <div class="col-md-4 text-center">
        <img src="/res/award/?type=gold2nd&month=<?=$i?>&year=<?=$this->year?>" alt="Gold 2nd" />
        <br />
        <strong><a target="_blank" href="https://www.pokerth.net/player?u=<?=$top3[$i][1]->playername?>"><?=$top3[$i][1]->playername?></a></strong>
      </div>
      <div class="col-md-4 text-center">
        <img src="/res/award/?type=gold3rd&month=<?=$i?>&year=<?=$this->year?>" alt="Gold 3rd" />
        <br />
        <strong><a target="_blank" href="https://www.pokerth.net/player?u=<?=$top3[$i][2]->playername?>"><?=$top3[$i][2]->playername?></a></strong>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">
    <hr />
  </div>
</div>
<?php endif; ?>
<?php endfor;?>
