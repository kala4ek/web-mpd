<?php

/**
 * Callback for handle POST requests.
 */
function web_mpd_post_handle() {
  if (!empty($_POST['command'])) {
    web_mpd_command($_POST['command']);
  }
  elseif (!empty($_POST['volume'])) {
    web_mpd_command('volume ' . $_POST['volume']);
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
function web_mpd_command($command) {
  return system('mpc ' . $command);
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