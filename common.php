<?php

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
 * Callback for handle GET requests.
 */
function web_mpd_get_handle() {
  if (isset($_GET['current'])) {
    web_mpd_current();
  }
  elseif (isset($_GET['volume'])) {
    web_mpd_volume_get();
  }
}

/**
 * Execute specific mpc command.
 */
function web_mpd_command($command, $arg = '') {
  return system("mpc $command $arg");
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
  $status = system('mpc status');
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
