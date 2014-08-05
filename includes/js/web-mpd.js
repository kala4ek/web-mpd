$(document).ready(function() {

  // Handle panel buttons.
  $('.play-buttons .btn-action').click(function() {
    var command = $(this).find('span').attr('class').split('-')[1];

    if (command == 'backward') {
      command = 'prev';
    }
    if (command == 'forward') {
      command = 'next';
    }

    $.post('/backend.php', {command: command});
    setTimeout('webMpdTitleUpdate()', 500);
  });

  // Handle volume change.
  $('#volume').change(function() {
    $.post('/backend.php', {command: 'volume', value: $(this).val()});
  });

  // Handle click at settings buttons.
  $('#repeat, #random, #single').click(function() {
    var $this = $(this);
    $this.toggleClass('active');
    $.post('/backend.php', {command: $this.attr('id')});
  });

  // Handle click at playlist buttons.
  $('.playlist button').click(function() {
    var $this = $(this);

    if ($this.find('span.glyphicon').hasClass('glyphicon-pause')) {
      $.post('/backend.php', {command: 'pause'});
      $this.find('span.glyphicon').removeClass('glyphicon-pause');
      $this.find('span.glyphicon').addClass('glyphicon-play');
    }
    else {
      $.post('/backend.php', {command: 'play', value: $this.data('id')});
      $this.find('span.glyphicon').removeClass('glyphicon-play');
      $this.find('span.glyphicon').addClass('glyphicon-pause');
    }
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
