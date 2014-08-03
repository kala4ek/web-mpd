<?php

require_once 'common.php';

if (!empty($_POST)) {
  web_mpd_post_handle();
}
elseif (!empty($_GET)) {
  web_mpd_get_handle();
}
else {
  header('HTTP/1.0 404 Not Found');
  exit;
}
