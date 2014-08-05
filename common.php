<?php

/**
 * Render all css files.
 */
function web_mpd_render_css() {
  $css[] = '<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">';
  $css[] = '<link rel="stylesheet" href="/includes/css/web-mpd.css">';

  return implode(PHP_EOL, $css) . PHP_EOL;
}

/**
 * Render all js files.
 */
function web_mpd_render_js() {
  $js[] = '<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>';
  $js[] = '<script src="/includes/js/web-mpd.js"></script>';

  return implode(PHP_EOL, $js) . PHP_EOL;
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
}

/**
 * Render playlist.
 */
function web_mpd_render_playlist() {
  if ($playlist = web_mpd_playlist()) {
    $already = FALSE;

    foreach ($playlist as $id => $track) {
      $list[$id] = '<li class="list-group-item">';
      if (!$already && $track == web_mpd_current()) {
        $already = TRUE;
        $list[$id] .= '<div class="btn-group btn-group-xs"><button data-id="' . $id . '" type="button" class="btn btn-default btn-playlist"><span class="glyphicon glyphicon-pause"></span></button></div>';
      }
      else {
        $list[$id] .= '<div class="btn-group btn-group-xs"><button data-id="' . $id . '" type="button" class="btn btn-default btn-playlist"><span class="glyphicon glyphicon-play"></span></button></div>';
      }
      $list[$id] .= $track;
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
 * Callback for handle GET requests.
 */
function web_mpd_get_handle() {
  if (isset($_GET['current'])) {
    print web_mpd_current();
  }
}

/**
 * Execute specific mpc command.
 */
function web_mpd_command($command, $arg = '') {
  ob_start();
  $result = system("mpc $command $arg");
  ob_clean();

  return $result;
}

/**
 * Print title of current track.
 */
function web_mpd_current() {
  return web_mpd_command('current');
}

/**
 * Get current volume.
 */
function web_mpd_volume_get() {
  ob_start();
  $volume = web_mpd_command('volume');
  ob_clean();

  return trim(substr($volume, 7), '% ');
}

/**
 * Get mpd status.
 */
function web_mpd_status() {
  ob_start();
  $status = web_mpd_command('status');
  ob_clean();

  return $status;
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
  $list = array();
  exec('mpc playlist', $list);

  $count = count($list);
  for($i=$count; $i>0; $i--){
    $list[$i] = $list[$i-1];
  }
  unset($list[0]);

  return $list;
}
