<?php

if (!empty($_POST['command'])) {
  system('mpc ' . $_POST['command']);
}
