

function videoPlaylistButton(videoId, page) {
    var currentId = null;
    var total = null;

    const VideoResults = document.getElementById("VideoResults_" + videoId);
    if (VideoResults != null) {
        currentId = VideoResults.getAttribute("data-current");
        total = VideoResults.getAttribute("data-total");
    }

    var Seloptions = document.getElementById("videoPlaylist_" + videoId);
    var playlistId = Seloptions.options[Seloptions.selectedIndex].value;

    var playlist_id = document.getElementById("videoPlaylistId_" + videoId);
    var plValue = playlist_id.getAttribute("value");

    $.ajax({
        url: "process.php",
        type: "POST",
        data: {
            submit: "playlist",
            PlaylistID: playlistId,
            playlist: { 0: videoId },
            Video_ID: videoId,
            AddToPlaylist: true,
            VideoPlayer: page,
            currentPl: plValue,
            currentId: currentId,
            total: total,
        },
        cache: false,
        success: function (data) {
            const element = document.getElementById("VideoPlaylistLabel");
            element.textContent = "Added !";
            element.style.display = "block";
            setTimeout(function () {
                $("#VideoPlaylistLabel").fadeOut(400);
                element.style.display = "none";
            }, 2000);
            if (page == "grid") {
                const videoCell = document.getElementById("Video_" + videoId);
                videoCell.innerHTML = data;
            }

            //    window.location.href = data
        },
    });
}
