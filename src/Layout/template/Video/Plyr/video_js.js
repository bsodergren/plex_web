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

// const player = new Plyr('video', {
//     captions: { active: true },
//     keyboard: { focused: true, global: true },
//     hideControls: false,
//     disableContextMenu: true,
//     seekTime: 30,

//     markers: { enabled: true, points: !!ChapterIndex!! }
// })
// window.player = player


function setPlayerTime(time){
    player.currentTime = time;
}


function resizeWindow(small)
{

    videoWidth = !!width!!
    videoHeight = !!height!!
    if (videoHeight > 1080) {
        videoHeight = 1080
    }
    if (videoWidth > 1920) {
        videoWidth = 1920
    }
    if(small == false) {
        windowWidth = videoWidth * 0.75
        windowHeight = videoHeight * .70
    } else {
        windowWidth = videoWidth
        windowHeight = videoHeight * 1.1
    
    }

    console.log("Width:"+windowWidth + " Height:" + windowHeight);

    window.resizeTo(windowWidth, windowHeight)
}

resizeWindow(false)


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



// var newOutletOptions = [
//   ['option name 1', 'firstValue'],
//   ['option name 2', 'secondValue']
// ]
// console.log(newOutletOptions)