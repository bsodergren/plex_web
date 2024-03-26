
$('.videoPlaylistButton').on('change', function (e) {
    var postData = $(this).serializeArray()
  
    var video_Id = document.getElementById('videoPlaylistVideoId')
    var idValue = video_Id.getAttribute('value')
    
   
    var playlist_id = document.getElementById('videoPlaylistId')
    var plValue = playlist_id.getAttribute('value')

    $.ajax({
      url: 'process.php',
      type: 'POST',
      data: {
        submit: 'playlist',
        PlaylistID:  postData[0].value,
        playlist: { 0: idValue },
        AddToPlaylist: true,
        VideoPlayer: true,
        currentPl: plValue,
      },
      cache: false,
      success: function (data) {
        const element = document.getElementById('VideoPlaylistLabel');
        element.textContent = 'Added !';
        element.style.display = "block";
        setTimeout(function() {
          $("#VideoPlaylistLabel").fadeOut(400);
          element.style.display = "none";
      }, 2000);

    //    window.location.href = data        
      }
    })
  })