const nextButton = document.getElementById("next-button");
const prevButton = document.getElementById("previous-button");
if(nextButton != null) {
    nextButton.addEventListener("click", nextVideo);
    prevButton.addEventListener("click", prevVideo);
    
function nextVideo() {
    setTimeout(function () { window.location.href = '!!__URL_HOME__!!/video.php?id=!!NEXT_VIDEO_ID!!&playlist_id=!!PLAYLIST_ID!!&r=true'; }, 0);
}

function prevVideo() {
    setTimeout(function () { window.location.href = '!!__URL_HOME__!!/video.php?id=!!PREV_VIDEO_ID!!&playlist_id=!!PLAYLIST_ID!!'+ '&r=true'; }, 0);
}

}

function updateVideoPlayer (video_id, pl_id) {
    setTimeout(function () {
        window.location.href =
            '{$__URL_HOME__}/video.php?id=' +
            video_id +
            '&playlist_id=' +
            pl_id +
            '&r=true'
    }, 0)
}

function getElementsStartsWithId( id ) {
    if (document.getElementsByClassName)
    {
        children = document.getElementsByClassName("plyr__progress");
     


    }

    var elements = [], child;

      child = children[0];
      return child.querySelector('input').id; 
  }
  document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('contextmenu', function(e) {
        //alert("You've tried to open context menu"); //here you draw your own menu
       // rightClickCloseForm();
        e.preventDefault();
      }, false);
const seekid = getElementsStartsWithId('plyr-seek');
const seek = document.getElementById(seekid);
seek.addEventListener('contextmenu', rightClickSeekTooltip);
  });
//child = seek.child;
//
var modal = document.getElementById('info')

window.addEventListener('keydown', function (event) {
  if (event.key === 'Escape') {
    modal.style.display = 'none'
  }
})
// 
function rightClickCloseForm(e)
{
   
    txtField = document.getElementById("info");
    txtField.className = "mb-3  hidden position-absolute";

}

function rightClickSeekTooltip (event) {
    const skipTo = Math.round(
            parseInt(event.target.getAttribute('aria-valuenow'), 10)
    )
    addChapter(event,skipTo)
    const t = formatTime(skipTo)
    time = `${t.minutes}:${t.seconds}`
}

function formatTime (timeInSeconds) {
    const result = new Date(timeInSeconds * 1000).toISOString().substr(11, 8)

    return {
        minutes: result.substr(3, 2),
        seconds: result.substr(6, 2)
    }
}

const player = new Plyr('video', {
    captions: { active: true },
    keyboard: { focused: true, global: true },
    hideControls: false,
    disableContextMenu: true,
    mediaMetadata: { title: '!!VideoTitle!!', artist: '!!VideoArtist!!', album: '!!VideoStudio!!'},
    markers: { enabled: true, points: !!ChapterIndex!! }
})
window.player = player

function skip(value) {
    player.currentTime += value;
}   
function setPlayerTime(time){
    player.currentTime = time;
}

videoWidth = !!width!!
videoHeight = !!height!!
if (videoHeight > 1080) {
    videoHeight = 1080
}
if (videoWidth > 1920) {
    videoWidth = 1920
}

windowWidth = videoWidth * 0.75
windowHeight = videoHeight * .90
window.resizeTo(windowWidth, windowHeight)



function addChapter(event, timeCode) {
    let x = event.clientX;
    let y = event.clientY;

    txtField = document.getElementById("info");
    txtField.style.left = x + "px";
    txtField.style.top = y + "px";
    txtField.style.display = "block";

    var hiddenAction = document.getElementById("timeCodeInput");
    hiddenAction.value = timeCode;

    // form.submit();
}

$("#addChapter").submit(function (e) {
    var postData = $(this).serializeArray();
    console.info(postData);
    $.ajax({
        url: "process.php",
        type: "POST",
        data: postData,
        success: function (data) {
        //    window.location.href = data;
        var form = $('#addChapter')
        console.log(data)
        return false;
        setPlayerTime(data)
        },
    });
});
