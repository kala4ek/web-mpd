$(document).ready(function() {

  // Handle panel buttons.
  $('.play-buttons button').click(function() {
    var command = $(this).find('span').attr('class').split('-')[1];

    if (command == 'backward') {
      command = 'prev';
    }
    if (command == 'forward') {
      command = 'next';
    }

    $.post('/backend.php', {command: command});
    webMpdTitleUpdate();
  });

  // Handle volume change.
  $('#volume').change(function() {
    $.post('/backend.php', {volume: $(this).val()});
  });

  // Auto-updating current title every 5 sec.
  setInterval(function() {
    webMpdTitleUpdate();
  }, 5000);
});

/**
 * Get title of current track.
 */
function webMpdTitleUpdate() {
  $.get('/backend.php?current', function(data) {
    $('#current-title').html(data);
  });
};
