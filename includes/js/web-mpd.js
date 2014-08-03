$(document).ready(function() {

  var command = '';
  $('.play-buttons button').click(function() {
    command = $(this).find('span').attr('class').split('-')[1];

    if (command == 'backward') {
      command = 'prev';
    }
    if (command == 'forward') {
      command = 'next';
    }

    $.post('/backend.php', {command: command});
  });
});
