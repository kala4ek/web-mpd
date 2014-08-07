<?php
require_once 'common.php';
?>

<!DOCTYPE html>
<html>
  <head>
    <title>
      Web MPD
    </title>
    <?php print web_mpd_render_metatag(); ?>
    <?php print web_mpd_render_css(); ?>
    <?php print web_mpd_render_js(); ?>
  </head>
  <body>
    <div class="well">
      <div class="additional-buttons">
        <div class="btn-group">
          <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#popup-upload">Upload music</button>
        </div>
      </div>
      <div class="play-buttons">
        <h1 id="current-title">
          <?php print web_mpd_current() . ' (#' . web_mpd_current_track_state() . ')'; ?>
        </h1>
        <div class="btn-group">
          <?php print web_mpd_render_buttons(); ?>
          <button id="repeat" type="button" class="btn btn-default <?php print web_mpd_is_repeat(); ?>"><span class="glyphicon glyphicon-refresh"></span></button>
          <button id="single" type="button" class="btn btn-default <?php print web_mpd_is_single(); ?>"><span class="glyphicon glyphicon-repeat"></span></button>
          <button id="random" type="button" class="btn btn-default <?php print web_mpd_is_random(); ?>"><span class="glyphicon glyphicon-random"></span></button>
        </div>
        <input id="volume" type="range" value="<?php print web_mpd_volume_get(); ?>" min="0" max="100" step="1">
      </div>
    </div>
    <div class="well playlist">
      <ul class="list-group">
        <?php print web_mpd_render_playlist(); ?>
      </ul>
    </div>

    <div class="modal fade" id="popup-upload" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Upload music</h4>
          </div>
          <div class="modal-body">
            <div id="uploader"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Close</button>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
