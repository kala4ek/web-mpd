<?php

function web_mpd_current() {
  $result = '';
  passthru('mpc current', $result);

  return $result;
}
