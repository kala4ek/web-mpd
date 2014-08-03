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
<!--    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css">-->
  </head>
  <body>
    <div class="well play-buttons">
      <h1 id="">
        <?php print web_mpd_current(); ?>
      </h1>
      <div class="btn-group">
        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-backward"></span> Previous</button>
        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-play"></span> Play</button>
        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-pause"></span> Pause</button>
        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-stop"></span> Stop</button>
        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-forward"></span> Next</button>
      </div>
    </div>
  </body>
</html>
