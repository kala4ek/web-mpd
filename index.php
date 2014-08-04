<?php
require_once 'common.php';
?>

<!DOCTYPE html>
<html>
  <head>
    <title>
      Web MPD
    </title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/includes/css/web-mpd.css">
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="/includes/js/web-mpd.js"></script>
  </head>
  <body>
    <div class="well play-buttons">
      <h1 id="current-title">
        <?php web_mpd_current(); ?>
      </h1>
      <div class="btn-group">
        <button type="button" class="btn btn-default btn-action"><span class="glyphicon glyphicon-backward"></span> Previous</button>
        <button type="button" class="btn btn-default btn-action"><span class="glyphicon glyphicon-play"></span> Play</button>
        <button type="button" class="btn btn-default btn-action"><span class="glyphicon glyphicon-pause"></span> Pause</button>
        <button type="button" class="btn btn-default btn-action"><span class="glyphicon glyphicon-stop"></span> Stop</button>
        <button type="button" class="btn btn-default btn-action"><span class="glyphicon glyphicon-forward"></span> Next</button>
        <button id="repeat" type="button" class="btn btn-default <?php print web_mpd_is_repeat(); ?>"><span class="glyphicon glyphicon-refresh"></span></button>
        <button id="single" type="button" class="btn btn-default <?php print web_mpd_is_single(); ?>"><span class="glyphicon glyphicon-repeat"></span></button>
        <button id="random" type="button" class="btn btn-default <?php print web_mpd_is_random(); ?>"><span class="glyphicon glyphicon-random"></span></button>
      </div>
      <input id="volume" type="range" value="<?php print web_mpd_volume_get(); ?>" min="0" max="100" step="1">
    </div>
  </body>
</html>
