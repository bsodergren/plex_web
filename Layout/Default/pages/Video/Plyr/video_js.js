function calculateAspectRatio(width, height) {
    function gcd(a, b) {
        return b == 0 ? a : gcd(b, a % b);
    }

    var divisor = gcd(width, height);

    return width / divisor + ":" + height / divisor;
}

function calculateApectHeight(width, ratio) {
    var t_arr = ratio.split(":");
    var div = width / t_arr[0];
    return div * t_arr[1];
}

function resizeWindow(large, width = 1280, height = 720) {
    if (large == null) {
        large = false;
    }

    if (typeof large == "object") {
        large = true;
    }

    videoWidth = width;
    videoHeight = height;

    text = "Origintal Video W:" + videoWidth + " H:" + videoHeight;
    // console.log(text)

    ratio = calculateAspectRatio(videoWidth, videoHeight);

    if (videoWidth > 1921) {
        videoWidth = 1280;
    }

    windowWidth = videoWidth * 0.75;
    videoHeight = calculateApectHeight(videoWidth, ratio);

    text = "Video to W:" + videoWidth + " H:" + videoHeight + " Ratio:" + ratio;
    console.log(text)
    heightMulti = 0.95;
    if (large == true) {
        // heightMulti = heightMulti + .13
    }
    windowHeight = videoHeight * heightMulti;

    text =
        "Resising Window to W:" +
        windowWidth +
        " H:" +
        windowHeight +
        ", x" +
        heightMulti +
        " Playlist:" +
        large;
     console.log(text)

    window.resizeTo(windowWidth, windowHeight);
}

resizeWindow(document.querySelector(".playlist-icon"), { $width }, { $height });
