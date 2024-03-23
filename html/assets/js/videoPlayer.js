
$('.videoPlaylistButton').on('change', function (e) {
    var postData = $(this).serializeArray()
  
    var hidden = document.getElementById('videoPlaylistId')
    var idValue = hidden.getAttribute('value')
  
   
    $.ajax({
      url: 'process.php',
      type: 'POST',
      data: {
        submit: 'playlist',
        PlaylistID:  postData[0].value,
        playlist: { 0: idValue },
        AddToPlaylist: true,
        VideoPlayer: true,
      },
      cache: false,
      success: function (data) {
        console.log(data)
        window.location.href = data        
      }
    })
  })