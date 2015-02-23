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
    setTimeout('webMpdUpdateTitlePlaylist()', 500);
  });

  // Handle volume change.
  $('#volume').change(function() {
    $.post('/backend.php', {command: 'volume', value: $(this).val()});
  });

  // Handle seek change.
  $('#seek').change(function() {
    $.post('/backend.php', {command: 'seek', value: $(this).val()});
  });

  // Handle click at settings buttons.
  $('#repeat, #random, #single, #mute').click(function() {
    var $this = $(this);
    $this.toggleClass('active');
    $.post('/backend.php', {command: $this.attr('id')});
  });

  // Handle click at playlist buttons.
  $('.btn-playlist').click(function() {
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

    setTimeout('webMpdUpdateTitlePlaylist()', 500);
  });

  // Auto-updating current title and playlist every 5 sec.
  setInterval(function() {
    webMpdUpdateTitlePlaylist();
  }, 5000);
  // Auto-updating seek of current track every sec.
  setInterval(function() {
    webMpdUpdateSeek();
  }, 1000);

  // Upload music by files.
  $('#uploader').JSAjaxFileUploader({
    uploadUrl: '/backend.php',
    autoSubmit: false,
    uploadTest: 'Upload',
    inputText: 'Select your best music...',
    allowExt: 'mp3'
  });

  // Upload music by url.
  $('#by-url input[type="submit"]').click(function(e) {
    e.preventDefault();
    var $input = $(this).closest('div').find('input[type="text"]');
    var url = $input.val();

    $.post('/backend.php', {upload_url: url}, function() {
      alert('Uploaded');
      $input.val('');
    });
  });

  // Delete track from playlist.
  $('.btn-remove').click(function() {
    var $this = $(this);
    $.post('/backend.php', {command: 'del', value: $this.data('id')});
    $this.closest('li').remove();
  });
});

/**
 * Update title of current track.
 */
function webMpdUpdateTitlePlaylist() {
  $.get('/backend.php?current', function(data) {
    $('#current-title').html(data.current);

    var $oldActive = $('.playlist ul li.active');
    $oldActive.removeClass('active');
    $oldActive.find('button span.glyphicon-pause').removeClass('glyphicon-pause').addClass('glyphicon-play');

    var $newActive = $('button[data-id="' + data.current_id + '"]');
    $newActive.find('span.glyphicon-play').removeClass('glyphicon-play').addClass('glyphicon-pause');
    $newActive.closest('li').addClass('active');
  });
}

/**
 * Update seek of track.
 */
function webMpdUpdateSeek() {
  $.get('/backend.php?current_seek', function(data) {
    var $seek = $('#seek');

    $seek.val(data.seek.current);
    $seek.attr('max', data.seek.total);
  });
}
