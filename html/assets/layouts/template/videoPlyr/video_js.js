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
const player = new Plyr('video', {
    captions: { active: true },
    keyboard: { focused: true, global: true }

    // markers: { enabled: true, points: [{ time: 60, label: 'Test' }] }
})
window.player = player

videoWidth = {$width};
videoHeight = {$height};
if(videoHeight > 1080){
    videoHeight = 1080;
}
if(videoWidth > 1920){
    videoWidth = 1920;
}

windowWidth = videoWidth * .75;
windowHeight = videoHeight * .80;
window.resizeTo(windowWidth, windowHeight);
