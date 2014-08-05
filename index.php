<?php
require_once 'common.php';
?>

<!DOCTYPE html>
<html>
  <head>
    <title>
      Web MPD
    </title>
    <?php print web_mpd_render_css(); ?>
    <?php print web_mpd_render_js(); ?>
  </head>
  <body>
    <div class="well play-buttons">
      <h1 id="current-title">
        <?php print web_mpd_current(); ?>
      </h1>
      <div class="btn-group">
        <?php print web_mpd_render_buttons(); ?>
        <button id="repeat" type="button" class="btn btn-default <?php print web_mpd_is_repeat(); ?>"><span class="glyphicon glyphicon-refresh"></span></button>
        <button id="single" type="button" class="btn btn-default <?php print web_mpd_is_single(); ?>"><span class="glyphicon glyphicon-repeat"></span></button>
        <button id="random" type="button" class="btn btn-default <?php print web_mpd_is_random(); ?>"><span class="glyphicon glyphicon-random"></span></button>
      </div>
      <input id="volume" type="range" value="<?php print web_mpd_volume_get(); ?>" min="0" max="100" step="1">
    </div>
    <div class="well playlist">
      <ul class="list-group">
        <?php print web_mpd_render_playlist(); ?>
      </ul>
    </div>
  </body>
</html>
