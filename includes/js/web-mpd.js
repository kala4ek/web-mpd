$(document).ready(function() {

  // Set height to playlist.
  var height = $(window).height() - $('.play-buttons').height() - 162;
  $('.playlist').height(height);

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
    setTimeout('webMpdUpdateTitle()', 500);
    setTimeout('webMpdUpdatePlaylist()', 500);
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
    var $ul = $this.closest('ul');
    var $spanGlyphicon = $this.find('span.glyphicon');

    if ($spanGlyphicon.hasClass('glyphicon-pause')) {
      $.post('/backend.php', {command: 'pause'});
      $spanGlyphicon.removeClass('glyphicon-pause').addClass('glyphicon-play');
      $this.closest('li').removeClass('active');
    }
    else {
      $.post('/backend.php', {command: 'play', value: $this.data('id')});
      $spanGlyphicon.removeClass('glyphicon-play').addClass('glyphicon-pause');
      $ul.find('li.active').removeClass('active');
      $ul.find('.glyphicon-pause').removeClass('glyphicon-pause').addClass('glyphicon-play');
      $this.closest('li').addClass('active');
    }

    setTimeout('webMpdUpdateTitle()', 500);
    setTimeout('webMpdUpdatePlaylist()', 500);
  });

  // Auto-updating current title and playlist every 5 sec.
  setInterval(function() {
    webMpdUpdateTitle();
    webMpdUpdatePlaylist();
  }, 5000);

  // Upload files.
  $('#uploader').JSAjaxFileUploader({
    uploadUrl: '/backend.php',
    autoSubmit: false,
    uploadTest: 'Upload',
    inputText: 'Select your best music...',
    allowExt: 'mp3'
  });
});

/**
 * Update title of current track.
 */
function webMpdUpdateTitle() {
  $.get('/backend.php?current', function(data) {
    $('#current-title').html(data);
  });
}

/**
 * Update playlist by current track.
 */
function webMpdUpdatePlaylist() {
  $.get('/backend.php?current_id', function(data) {
    var $oldActive = $('.playlist ul li.active');
    $oldActive.removeClass('active');
    $oldActive.find('button span').removeClass('glyphicon-pause').addClass('glyphicon-play');

    var $newActive = $('button[data-id="' + data + '"]');
    $newActive.find('span').removeClass('glyphicon-play').addClass('glyphicon-pause');
    $newActive.closest('li').addClass('active');
  });
}
