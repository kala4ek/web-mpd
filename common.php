<?php

// Include settings file.
require_once 'settings.php';

/**
 * Render all css files.
 */
function web_mpd_render_css() {
  $css[] = '<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">';
  $css[] = '<link rel="stylesheet" href="/includes/css/JQuery.JSAjaxFileUploader.css" >';
  $css[] = '<link rel="stylesheet" href="/includes/css/web-mpd.css">';

  return implode(PHP_EOL, $css) . PHP_EOL;
}

/**
 * Render all js files.
 */
function web_mpd_render_js() {
  $js[] = '<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>';
  $js[] = '<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>';
  $js[] = '<script src="/includes/js/JQuery.JSAjaxFileUploader.min.js"></script>';
  $js[] = '<script src="/includes/js/web-mpd.js"></script>';

  return implode(PHP_EOL, $js) . PHP_EOL;
}

/**
 * Render additional head tags files.
 */
function web_mpd_render_metatag() {
  $meta[] = '<link rel="shortcut icon" href="/includes/images/favicon.ico" type="image/x-icon">';
  $meta[] = '<link rel="icon" href="/includes/images/favicon.ico" type="image/x-icon">';
  $meta[] = '<link rel="apple-touch-icon" href="/includes/images/touch-icon-iphone.jpg">';
  $meta[] = '<link rel="apple-touch-icon" sizes="76x76" href="/includes/images/touch-icon-ipad.jpg">';
  $meta[] = '<link rel="apple-touch-icon" sizes="120x120" href="/includes/images/touch-icon-iphone-retina.jpg">';
  $meta[] = '<link rel="apple-touch-icon" sizes="152x152" href="/includes/images/touch-icon-ipad-retina.jpg">';

  return implode(PHP_EOL, $meta) . PHP_EOL;
}

/**
 * Callback for handle POST requests.
 */
function web_mpd_post_handle() {
  if (!empty($_POST['command'])) {
    !empty($_POST['value'])
      ? web_mpd_command($_POST['command'], $_POST['value'])
      : web_mpd_command($_POST['command']);
  }
  elseif (!empty($_POST['upload_url'])) {
    web_mpd_upload_url($_POST['upload_url']);
  }
}

/**
 * Callback for handle GET requests.
 */
function web_mpd_get_handle() {
  if (isset($_GET['current'])) {
    print web_mpd_current() . ' (#' . web_mpd_current_track_state() . ')';
  }
  if (isset($_GET['current_id'])) {
    print web_mpd_current_id();
  }
}

/**
 * Callback for handle FILE requests.
 */
function web_mpd_files_handle() {
  global $conf;

  // Handle errors.
  switch ($_FILES['file']['error']) {
    case UPLOAD_ERR_OK:
      break;

    case UPLOAD_ERR_NO_FILE:
      throw new RuntimeException('No file sent.');

    case UPLOAD_ERR_INI_SIZE:
    case UPLOAD_ERR_FORM_SIZE:
      throw new RuntimeException('Exceeded filesize limit.');

    default:
      throw new RuntimeException('Unknown errors.');
  }

  // Save file into music directory and update playlist
  move_uploaded_file($_FILES['file']['tmp_name'], $conf['music_path'] . '/' . $_FILES['file']['name']);
  sleep(1);
  web_mpd_update_playlist();
}

/**
 * Upload file by url.
 */
function web_mpd_upload_url($url) {
  global $conf;

  if ($data = file_get_contents($url)) {
    file_put_contents($conf['music_path'] . '/' . time() . '.mp3', $data);
    sleep(1);
    web_mpd_update_playlist();
  }
}

/**
 * Render playlist.
 */
function web_mpd_render_playlist() {
  $playlist = web_mpd_playlist();
  if ($playlist[1]) {
    $already = FALSE;

    foreach ($playlist as $id => $track) {

      if (!$already && $track == web_mpd_current()) {
        $already = TRUE;
        $class = 'active';
        $button = '<div class="btn-group btn-group-xs"><button data-id="' . $id . '" type="button" class="btn btn-default btn-playlist"><span class="glyphicon glyphicon-pause"></span></button></div>';
      }
      else {
        $class = '';
        $button = '<div class="btn-group btn-group-xs"><button data-id="' . $id . '" type="button" class="btn btn-default btn-playlist"><span class="glyphicon glyphicon-play"></span></button></div>';
      }
      $list[$id] = '<li class="list-group-item ' . $class . '">';
      $list[$id] .= $button;
      $list[$id] .= $track;
      $list[$id] .= '<button data-id="' . $id . '" type="button" class="btn btn-default btn-remove"><span class="glyphicon glyphicon-remove"></span></button>';
      $list[$id] .= '</li>';
    }

    return implode(PHP_EOL, $list) . PHP_EOL;
  }

  return '';
}

/**
 * Render control buttons.
 */
function web_mpd_render_buttons() {
  $buttons = array(
    'backward' => 'Previous',
    'play' => 'Play',
    'pause' => 'Pause',
    'stop' => 'Stop',
    'forward' => 'Next',
  );

  foreach ($buttons as $class => $text) {
    $list[] = "<button type='button' class='btn btn-default btn-action'><span class='glyphicon glyphicon-$class'></span> $text</button>";
  }

  return implode(PHP_EOL, $list) . PHP_EOL;
}

/**
 * Execute specific mpc command.
 */
function web_mpd_command($command, $arg = '') {
  $result = array();
  exec("mpc $command $arg", $result);

  return implode(PHP_EOL, $result);
}

/**
 * Print title of current track.
 */
function web_mpd_current() {
  return web_mpd_command('current');
}

/**
 * Get current track state (43/152).
 */
function web_mpd_current_track_state() {
  $status = web_mpd_status();
  preg_match('/#[0-9]*\/[0-9]*/', $status, $matches);
  $track_info = trim(reset($matches), '#');

  return $track_info;
}

/**
 * Get current volume.
 */
function web_mpd_volume_get() {
  $volume = web_mpd_command('volume');

  return trim(substr($volume, 7), '% ');
}

/**
 * Get mpd status.
 */
function web_mpd_status() {
  return web_mpd_command('status');
}

/**
 * Check if repeat is enabled.
 */
function web_mpd_is_repeat() {
  return strpos(web_mpd_status(), 'repeat: on') === FALSE ? '' : 'active';
}


/**
 * Check if single is enabled.
 */
function web_mpd_is_single() {
  return strpos(web_mpd_status(), 'single: on') === FALSE ? '' : 'active';
}


/**
 * Check if random is enabled.
 */
function web_mpd_is_random() {
  return strpos(web_mpd_status(), 'random: on') === FALSE ? '' : 'active';
}

/**
 * Get playlist.
 *
 * @return array.
 */
function web_mpd_playlist() {
  $list = web_mpd_command('playlist');
  $list = explode(PHP_EOL, $list);

  $count = count($list);
  for($i=$count; $i>0; $i--){
    $list[$i] = $list[$i-1];
  }
  unset($list[0]);

  return $list;
}

/**
 * Get current track id.
 */
function web_mpd_current_id() {
  $track_info = reset(explode('/', web_mpd_current_track_state()));

  return $track_info;
}

/**
 * Add all tracks to playlist.
 */
function web_mpd_update_playlist() {
  web_mpd_command('clear');
  web_mpd_command('update');
  web_mpd_command('ls', '| mpc add');
}
