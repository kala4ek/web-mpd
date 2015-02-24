<?php

// Include settings file.
if (file_exists('settings.php')) {
  require_once 'settings.php';
}
else {
  throw new Exception('Missing settings file');
}

// Define commands.
define('WEB_MPD_MUTE', 'mute');
define('WEB_MPD_VOLUME', 'volume');
define('WEB_MPD_PLAY', 'play');
define('WEB_MPD_STOP', 'stop');
define('WEB_MPD_PAUSE', 'pause');
define('WEB_MPD_CURRENT', 'current');
define('WEB_MPD_STATUS', 'status');
define('WEB_MPD_PLAYLIST', 'playlist');
define('WEB_MPD_CLEAR', 'clear');
define('WEB_MPD_UPDATE', 'update');

define('WEB_MPD_SEEK_CURRENT', 'current');
define('WEB_MPD_SEEK_TOTAL', 'total');

/**
 * Render all css files.
 *
 * @return string
 */
function web_mpd_render_css() {
  $css[] = '<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">';
  $css[] = '<link rel="stylesheet" href="/includes/css/JQuery.JSAjaxFileUploader.css" >';
  $css[] = '<link rel="stylesheet" href="/includes/css/web-mpd.css">';

  return implode(PHP_EOL, $css) . PHP_EOL;
}

/**
 * Render all js files.
 *
 * @return string
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
 *
 * @return string
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
    $command = $_POST['command'];
    if ($command == WEB_MPD_MUTE) {
      web_mpd_mute_toggle();
      exit;
    }

    isset($_POST['value'])
      ? web_mpd_command($command, $_POST['value'])
      : web_mpd_command($command);
  }
  elseif (!empty($_POST['upload_url'])) {
    web_mpd_upload_url($_POST['upload_url']);
  }
}

/**
 * Callback for handle GET requests.
 */
function web_mpd_get_handle() {
  header('Content-Type: application/json');
  $data = array();

  if (isset($_GET['current'])) {
    $data['current'] = web_mpd_current() . ' (#' . web_mpd_current_track_state() . ')';
    $data['current_id'] = web_mpd_current_id();
  }
  if (isset($_GET['current_seek'])) {
    $data['seek']['current'] = web_mpd_seek_get(WEB_MPD_SEEK_CURRENT);
    $data['seek']['total'] = web_mpd_seek_get(WEB_MPD_SEEK_TOTAL);
  }

  print json_encode($data);
  exit;
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
 *
 * @return string
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
 *
 * @return string
 */
function web_mpd_render_buttons() {
  $buttons = array(
    'backward' => 'Previous',
    WEB_MPD_PLAY => 'Play',
    WEB_MPD_PAUSE => 'Pause',
    WEB_MPD_STOP => 'Stop',
    'forward' => 'Next',
  );

  foreach ($buttons as $class => $text) {
    $list[] = "<button type='button' class='btn btn-default btn-action' title='$text'><span class='glyphicon glyphicon-$class'></span> $text</button>";
  }

  return implode(PHP_EOL, $list) . PHP_EOL;
}

/**
 * Execute specific mpc command.
 *
 * @param string $command
 * @param string $arg
 *
 * @return string
 */
function web_mpd_command($command, $arg = '') {
  $result = array();
  exec("mpc $command $arg", $result);

  return implode(PHP_EOL, $result);
}

/**
 * Print title of current track.
 *
 * @return string
 */
function web_mpd_current() {
  return web_mpd_command(WEB_MPD_CURRENT);
}

/**
 * Get current track state.
 *
 * @return string
 */
function web_mpd_current_track_state() {
  $status = web_mpd_status();
  preg_match('/#[0-9]*\/[0-9]*/', $status, $matches);
  $track_info = trim(reset($matches), '#');

  return $track_info;
}

/**
 * Get current volume.
 *
 * @return int
 */
function web_mpd_volume_get() {
  $volume = web_mpd_command(WEB_MPD_VOLUME);

  return trim(substr($volume, 7), '% ');
}

/**
 * Get seek of current song.
 *
 * @param string $type
 *
 * @return int
 */
function web_mpd_seek_get($type = WEB_MPD_SEEK_TOTAL) {
  $status = web_mpd_status();

  if (preg_match('/\d+:\d+\/\d+:\d+/', $status, $seeks)) {
    list($current, $total) = explode('/', $seeks[0]);

    $current = explode(':', $current);
    $total = explode(':', $total);

    $current = ($current[0] * 60) + $current[1];
    $total = ($total[0] * 60) + $total[1];

    return $type == WEB_MPD_SEEK_TOTAL ? $total : $current;
  }

  return 0;
}

/**
 * Get mpd status.
 *
 * @return string
 */
function web_mpd_status() {
  return web_mpd_command(WEB_MPD_STATUS);
}

/**
 * Check if repeat is enabled.
 *
 * @return string
 */
function web_mpd_is_repeat() {
  return strpos(web_mpd_status(), 'repeat: on') === FALSE ? '' : 'active';
}

/**
 * Check if single is enabled.
 *
 * @return string
 */
function web_mpd_is_single() {
  return strpos(web_mpd_status(), 'single: on') === FALSE ? '' : 'active';
}

/**
 * Check if random is enabled.
 *
 * @return string
 */
function web_mpd_is_random() {
  return strpos(web_mpd_status(), 'random: on') === FALSE ? '' : 'active';
}

/**
 * Check if volume is disable.
 *
 * @return string
 */
function web_mpd_is_mute() {
  return web_mpd_volume_get() == 0 ? 'active' : '';
}

/**
 * Get playlist.
 *
 * @return array.
 */
function web_mpd_playlist() {
  $list = web_mpd_command(WEB_MPD_PLAYLIST);
  $list = explode(PHP_EOL, $list);

  $count = count($list);
  for($i = $count; $i > 0; $i--){
    $list[$i] = $list[$i - 1];
  }
  unset($list[0]);

  return $list;
}

/**
 * Get current track id.
 *
 * @return int
 */
function web_mpd_current_id() {
  $track_info = reset(explode('/', web_mpd_current_track_state()));

  return $track_info;
}

/**
 * Add all tracks to playlist.
 */
function web_mpd_update_playlist() {
  web_mpd_command(WEB_MPD_CLEAR);
  web_mpd_command(WEB_MPD_UPDATE);
  web_mpd_command('ls', '| mpc add');
}

/**
 * Toggle the volume muting.
 */
function web_mpd_mute_toggle() {
  if (web_mpd_is_mute()) {
    web_mpd_command(WEB_MPD_VOLUME, 50);
  }
  else {
    web_mpd_command(WEB_MPD_VOLUME, 0);
  }
}
