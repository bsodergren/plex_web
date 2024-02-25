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

const seekid = getElementsStartsWithId('plyr-seek');
const seek = document.getElementById(seekid);
seek.addEventListener('contextmenu', rightClickSeekTooltip);
  });
//child = seek.child;
//

// 

function rightClickSeekTooltip (event) {
    console.log("Width " + event.target.clientWidth)
    console.log("offset " + event.offsetX )

    const skipTo = Math.round(
        // (event.offsetX / event.target.clientWidth) *
            parseInt(event.target.getAttribute('aria-valuenow'), 10)
    )
    console.log(skipTo)

    addChapter(skipTo)
    const t = formatTime(skipTo)

    time = `${t.minutes}:${t.seconds}`
    console.log(time)
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

    markers: { enabled: true, points: !!ChapterIndex!! }
})
window.player = player


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
