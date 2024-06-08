var curVolume = getCookie("playerVolCookie");
if (curVolume == null) {
    curVolume = 0.5;
} else {
    curVolume = curVolume / 100;
}
console.log("current volume " + curVolume);

const PlayerApp = {
    playlist: null,
    playerContainer: null,
    playlistItems: [],
    tracks: [],
    player: null,

    /**
     * Initializes the player application.
     */
    initialize() {
        // Get the DOM element
        this.playlist = document.getElementById("playlist");
        this.playerContainer = document.getElementById("player_container");
        // prepare objects
        this.createPlayer();

        if (this.playlist != null) {
            this.initializePlaylistItems();
            this.populateAccordionContent();
            this.initializeEventListeners();
            this.showAllPlaylistItems();
            this.initializePlPlayerEventListeners();
            this.initializePlaylistIcon();
            this.initializeSearch();
            this.playVisibleItem();
            this.initialiseSimpleBar();
            this.selectFirstVisibleItem();
            this.initialiseChapter();
        } else {
            this.initializePlayerEventListeners();
            this.initialiseChapter();
            const leftColumn = document.querySelector(".col-sm-12.left");
            leftColumn.classList.toggle("expanded");
        }
    },

    /**
     * Creates the Plyr player with the specified controls.
     */
    createPlayer() {
        const controls = `
    <!-- Player controls HTML -->
    <button type="button" class="plyr__control plyr__control--overlaid" data-plyr="play">
        <svg aria-hidden="true" focusable="false">
          <use xlink:href="#plyr-play"></use>
        </svg>
        <span class="plyr__sr-only">Play</span>
    </button>
    <div class="plyr__controls">
        <button class="plyr__controls__item plyr__control" type="button" data-plyr="restart">
          <svg aria-hidden="true" focusable="false">
              <use xlink:href="#plyr-restart"></use>
          </svg>
          <span class="plyr__tooltip">Restart</span>
        </button>
        <button class="plyr__controls__item plyr__control" data-plyr="prev">
          <svg fill="#ffffff" viewBox="-0.5 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
              <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
              <g id="SVGRepo_iconCarrier">
                <path d="m21.108 23.855-18.708-10.494v10.038.002c0 .331-.268.599-.599.599-.001 0-.001 0-.002 0h-1.2c-.001 0-.001 0-.002 0-.331 0-.599-.268-.599-.599 0-.001 0-.001 0-.002v-22.799-.001c0-.331.268-.599.599-.599h.001 1.2.001c.331 0 .599.268.599.599v.001 10.038l18.708-10.493c.159-.089.348-.141.549-.141.631 0 1.142.511 1.142 1.142v.024-.001 21.665.015c0 .634-.511 1.149-1.143 1.155h-.001c-.202-.002-.39-.057-.552-.152l.005.003z"></path>
              </g>
          </svg>
          <span class="plyr__tooltip">Prev</span>
        </button>
        <button class="plyr__controls__item plyr__control" type="button" data-plyr="play">
          <svg class="icon--pressed" aria-hidden="true" focusable="false">
              <use xlink:href="#plyr-pause"></use>
          </svg>
          <svg class="icon--not-pressed" aria-hidden="true" focusable="false">
              <use xlink:href="#plyr-play"></use>
          </svg>
          <span class="label--pressed plyr__tooltip">Pause</span>
          <span class="label--not-pressed plyr__tooltip">Play</span>
        </button>
        <button type="button" class="plyr__control" data-plyr="fast-forward">
        <svg role="presentation"><use xlink:href="#plyr-fast-forward"></use></svg>
        <span class="plyr__tooltip" role="tooltip">Forward {seektime} secs</span>
    </button>
        <button class="plyr__controls__item plyr__control" data-plyr="next">
          <svg fill="#ffffff" viewBox="-0.5 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
              <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
              <g id="SVGRepo_iconCarrier">
                <path d="m0 22.835v-21.665c0-.007 0-.015 0-.024 0-.63.511-1.141 1.141-1.141.201 0 .391.052.555.144l-.006-.003 18.71 10.493v-10.038c0-.331.268-.6.599-.6h1.2c.332 0 .6.269.6.6v22.799.001c0 .331-.268.599-.599.599h-.001-1.2c-.331 0-.599-.268-.599-.599v-.001-10.038l-18.71 10.494c-.158.091-.347.145-.548.145-.632-.007-1.142-.521-1.142-1.155 0-.004 0-.008 0-.011v.001z"></path>
              </g>
          </svg>
          <span class="plyr__tooltip">Next</span>
        </button>
        <div class="plyr__controls__item plyr__progress__container">
          <div class="plyr__progress">
              <input data-plyr="seek" type="range" min="0" max="100" step="0.01" value="0" aria-label="Seek">
              <progress class="plyr__progress__buffer" min="0" max="100" value="0">% buffered</progress>
              <span role="tooltip" class="plyr__tooltip">00:00</span>
          </div>
        </div>
        <div class="plyr__controls__item plyr__time plyr__time--current" aria-label="Current time">00:00</div>
        <div class="plyr__controls__item plyr__time plyr__time--duration" aria-label="Duration">00:00</div>
        <button type="button" plyr__controls__item class="plyr__control"
        aria-label="Mute" data-plyr="mute">
          <svg class="icon--pressed" role="presentation">
              <use xlink:href="#plyr-muted"></use>
          </svg>
          <svg class="icon--not-pressed" role="presentation">
              <use xlink:href="#plyr-volume"></use>
          </svg>
          <span class="label--pressed plyr__tooltip" role="tooltip">Unmute</span>
          <span class="label--not-pressed plyr__tooltip" role="tooltip">Mute</span>
        </button>
        <div class="plyr__controls__item plyr__volume">
          <input data-plyr="volume" type="range" min="0" max="1" step="0.05" value="1" autocomplete="off" aria-label="Volume">
        </div>

        <button type="button" class="plyr__controls__item plyr__control" data-plyr="fullscreen">
          <svg class="icon--pressed" role="presentation">
              <use xlink:href="#plyr-exit-fullscreen"></use>
          </svg>
          <svg class="icon--not-pressed" role="presentation">
              <use xlink:href="#plyr-enter-fullscreen"></use>
          </svg>
          <span class="label--pressed plyr__tooltip" role="tooltip">Exit fullscreen</span>
          <span class="label--not-pressed plyr__tooltip" role="tooltip">Enter fullscreen</span>
        </button>
      </div>
    `;

        /**
         * Initializes the playlist items by extracting data from the DOM.
         */
        this.player = new Plyr(".js-playerx", {
            // Plyr options
            controls: controls,
            ratio: "16:9",
            // iconUrl: "https://cdn.plyr.io/3.7.8/plyr.svg",
            blankVideo: "https://cdn.plyr.io/static/blank.mp4",
            loadSprite: true,
            debug: false,
            keyboard: {
                focused: true,
                global: true,
            },
            i18n: {
                restart: "Restart",
            },
            // tooltips: {
            //   controls: true,
            //   seek: true
            // },
            autoplay: false,
            seekTime: 30,
            volume: curVolume,
            clickToPlay: true,
            hideControlsOnPause: false,
            hideControls: true,
            disableContextMenu: true,
            storage: {
                enabled: false,
            },

            playlist: {
                enabled: true,
                scroll: {
                    container: this.playlist,
                    enabled: true,
                },
            },
        });
    },

    /**
     * Initializes the playlist items by extracting data from the DOM.
     */
    initializePlaylistItems() {
        this.playlistItems = Array.from(
            this.playlist.getElementsByClassName("playlist_item")
        );
        this.tracks = this.playlistItems.map((item) =>
            this.createTrackFromPlaylistItem(item)
        );

        // Add 'hidden' class to each playlist item
        this.playlistItems.forEach((item) => {
            item.classList.add("hidden");
        });
    },

    /**
     * Creates a track object from a playlist item.
     * @param {HTMLElement} item - The playlist item element.
     * @returns {Object} - The created track object.
     */
    createTrackFromPlaylistItem(item) {
        const type = item.getAttribute("data-type");
        const videoUrl = item.getAttribute("data-video");
        const category = item.getAttribute("data-category");

        let source;
        if (type === "youtube") {
            source = { src: videoUrl, provider: "youtube" };
        } else if (type === "vimeo") {
            source = { src: videoUrl, provider: "vimeo" };
        } else {
            source = { src: videoUrl, provider: "html5" };
        }

        return {
            title: item.getAttribute("data-title"),
            genre: item.getAttribute("data-genre"),
            artist: item.getAttribute("data-artist"),
            studio: item.getAttribute("data-studio"),
            videoid: item.getAttribute("data-videoid"),
            width: item.getAttribute("data-width"),
            height: item.getAttribute("data-height"),
            sources: [source],
            poster: item.getAttribute("data-poster"),
            category,
        };
    },

    /**
     * Initializes event listeners for filter buttons and accordion button.
     */
    initializeEventListeners() {
        const filterButtons =
            this.playlist.getElementsByClassName("filter-button");
        Array.from(filterButtons).forEach((button) => {
            button.addEventListener("click", (event) => {
                const selectedCategory =
                    event.target.getAttribute("data-category");
                this.updateAccordionButtonText(selectedCategory);
                this.toggleAccordion();
                this.filterPlaylist(selectedCategory);
            });
        });

        const accordionButton = document.querySelector(".accordion-button");
        accordionButton.addEventListener("click", this.toggleAccordion);
    },
    /**
     * Initializes event listeners for the Plyr player.
     */
    initializePlPlayerEventListeners() {
        // begin player Ready Count
        var readyCount = 0;

        // Get the currently playing track

        this.player.on("ready", () => {
            this.player.play();
        });
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const videoid = urlParams.get("id");

        this.player.on("ready", () => {
            itemIndex = 0;
            if (readyCount == 0) {
                if (videoid != null) {
                    this.playlistItems.forEach((item) => {
                        const tindex = this.playlistItems.indexOf(item);
                        const playlistItem = this.playlistItems[tindex];
                        var plVideoId =
                            playlistItem.getAttribute("data-videoid");
                        if (plVideoId == videoid) {
                            itemIndex = tindex;
                            playlistItem.classList.add("active");
                        }
                    });
                }
                const firstPlaylistItem = this.playlistItems[itemIndex];
                const firstTrack = this.tracks[itemIndex];
                this.handlePlaylistItemClick(firstPlaylistItem, firstTrack);
                // this.handlePlaylistItemClick(firstPlaylistItem, firstTrack)
            }
            readyCount++;
        });

        const playlistIcon = document.querySelector(".playlist-icon");

        var playlerText = document.querySelector(
            ".player_container .player_text"
        );

        this.player.on("controlshidden", () => {
            //this.player.play();
            if (playlerText != null) {
                playlistIcon.classList.add("hidden");
                playlerText.classList.add("hidden");
            }
        });
        this.player.on("controlsshown", () => {
            //this.player.play();
            if (playlerText != null) {
                // playlistIcon.classList.remove("hidden");
                // playlerText.classList.remove("hidden");
            }
        });

        this.player.on("ended", () => {
            this.playVisibleItem("next");
            resize();
        });

        this.player.on("progress", () => {
            document
                .querySelector(".player_container")
                .classList.add("loading");
        });

        this.player.off("canplaythrough", () => {
            document
                .querySelector(".player_container")
                .classList.remove("loading");
        });

        this.player.off("canplay", () => {
            document
                .querySelector(".player_container")
                .classList.remove("loading");
        });

        this.playlistItems.forEach((item) => {
            item.addEventListener("click", () => {
                const index = this.playlistItems.indexOf(item);
                const track = this.tracks[index];
                this.handlePlaylistItemClick(item, track);
            });
        });

        const parentElement = document.querySelector(".player_container");

        parentElement.addEventListener("click", (event) => {
            console.log(event);

            if (event.target.matches('button[data-plyr="next"]')) {
                this.playVisibleItem("next");
            }

            if (event.target.matches('button[data-plyr="prev"]')) {
                this.playVisibleItem("prev");
            }
        });

        document.addEventListener("keydown", (event) => {
            // console.log(event.key);
            if (event.key === "n") {
                this.playVisibleItem("next");
            }
            if (event.key === "p") {
                this.playVisibleItem("prev");
            }
        });
    },

    /**
     * Initializes event listeners for the Plyr player.
     */
    initializePlayerEventListeners() {
        // Get the currently playing track
        var playlerText = document.querySelector(
            ".player_container .player_text"
        );

        this.player.on("ready", () => {
            this.player.play();
        });

        this.player.on("controlshidden", () => {
            //this.player.play();
            if (playlerText != null) {
                playlerText.classList.add("hidden");
            }
        });
        this.player.on("controlsshown", () => {
            //this.player.play();
            if (playlerText != null) {
                //  playlerText.classList.remove("hidden");
            }
        });

        this.player.on("progress", () => {
            document
                .querySelector(".player_container")
                .classList.add("loading");
        });

        this.player.off("canplaythrough", () => {
            document
                .querySelector(".player_container")
                .classList.remove("loading");
        });

        this.player.off("canplay", () => {
            document
                .querySelector(".player_container")
                .classList.remove("loading");
        });
        this.player.on("ended", () => {
            this.playNewVideo(playlerText.getAttribute("data-videoid"));
            resize();
        });

        const parentElement = document.querySelector(".player_container");

        parentElement.addEventListener("click", (event) => {
            if (event.target.matches('button[data-plyr="next"]')) {
                this.playNewVideo(playlerText.getAttribute("data-videoid"));
            }

            if (event.target.matches('button[data-plyr="prev"]')) {
                this.playVisibleItem("prev");
            }
        });

        document.addEventListener("keydown", (event) => {
            // console.log(event.key);
            if (event.key === "n") {
                this.playNewVideo(playlerText.getAttribute("data-videoid"));
            }
        });
    },

    /**
     * Creates a track object from a playlist item.
     * @param {HTMLElement} item track - The playlist item element.
     * @returns {Object} - The created track object.
     */
    handlePlaylistItemClick(item, track) {
        this.playlistItems.forEach((item) => {
            if (item.classList.contains("active")) {
                item.classList.remove("active");
                item.classList.add("watched");
            }
        });

        item.classList.add("active");
        // Get the text-title span element

        setInfoText("text", item);
        setInfoText("video", item);

        updateOptions(item.getAttribute("data-videoid"));
        this.player.source = {
            type: "video",
            title: track.title,
            sources: track.sources,
            poster: track.poster,
        };
        var isPlaying = this.player.play();

        var playerControl = document.querySelector(".plyr__volume");
        var inputNode = playerControl.children[0];
        //    console.log(inputNode.attributes)//,
        setCookie(
            "playerVolCookie",
            inputNode.getAttribute("aria-valuenow"),
            7
        );
        //    console.log(inputNode.getAttribute("aria-valuenow"))

        // console.log(track.width , track.height)
        // resizeWindow(true,track.width , track.height)
        // resize();
        updateFavVideo(item.getAttribute("data-videoid"));
        updateVideoChapters(item.getAttribute("data-videoid"));

    },

    /**
     * Fetch Categories from plalsit items and add them to Accordian
     */
    populateAccordionContent() {
        const accordionContent = document.querySelector(".accordion-content");
        accordionContent.innerHTML = ""; // Clear the content before populating

        // Create <div> element for the "All" category
        const allDiv = document.createElement("div");
        allDiv.classList.add("filter-button");
        allDiv.setAttribute("data-category", "All");
        allDiv.textContent = "All";

        accordionContent.appendChild(allDiv);

        // Create <div> elements for each category
        const categories = new Set();
        this.playlistItems.forEach((item) => {
            const category = item.getAttribute("data-category");
            if (category && category.trim() !== "") {
                // Check if category is not empty or null
                const categoryList = category.split(";");

                categoryList.forEach((categoryItem) => {
                    const trimmedCategoryItem = categoryItem.trim();
                    if (
                        trimmedCategoryItem !== "" &&
                        !categories.has(trimmedCategoryItem)
                    ) {
                        // Check if categoryItem is not empty and not already added
                        categories.add(trimmedCategoryItem);

                        const div = document.createElement("div");
                        div.classList.add("filter-button");
                        div.setAttribute("data-category", trimmedCategoryItem);
                        div.textContent = trimmedCategoryItem;

                        accordionContent.appendChild(div);
                    }
                });
            }
        });

        // Add click event listeners to the new filter buttons
        const filterButtons =
            accordionContent.getElementsByClassName("filter-button");
        Array.from(filterButtons).forEach((button) => {
            button.addEventListener("click", (event) => {
                const selectedCategory =
                    event.target.getAttribute("data-category");
                this.updateAccordionButtonText(selectedCategory);
                this.toggleAccordion();
                this.filterPlaylist(selectedCategory);
            });
        });
    },

    /**
     * Filters the playlist by the selected category.
     * @param {string} category - The selected category.
     */
    filterPlaylist(category) {
        // Remove "active" class from all playlist items
        this.playlistItems.forEach((item) => {
            item.classList.remove("active");
        });

        this.playlistItems.forEach((item) => {
            const itemCategories = item.getAttribute("data-category");
            if (category === "All" || itemCategories.includes(category)) {
                item.classList.remove("hidden"); // Remove 'hidden' class
            } else {
                item.classList.add("hidden"); // Add 'hidden' class
            }
        });

        const visibleItems = this.playlist.querySelectorAll(
            ".playlist_item:not(.hidden)[data-category]"
        );
        if (visibleItems.length > 0) {
            const firstVisibleItem = visibleItems[0];
            const index = this.playlistItems.indexOf(firstVisibleItem);
            const selectedTrack = this.tracks[index];
            // Add "active" class to the first visible playlist item
            //firstVisibleItem.classList.add('active')

            this.player.source = {
                type: "video",
                title: selectedTrack.title,
                sources: selectedTrack.sources,
                poster: selectedTrack.poster,
            };
            this.player.play();
        }
    },

    showAllPlaylistItems() {
        this.playlistItems.forEach((item) => {
            item.classList.remove("hidden");
        });
    },

    /**
     * Toggles the accordion to show/hide the category list
     */
    toggleAccordion() {
        const accordion = document.querySelector(".accordion");
        accordion.classList.toggle("active");
    },

    /**
     * Updates the text of the accordion button.
     * @param {string} category - The selected category.
     */
    updateAccordionButtonText(category) {
        const accordionButton = document.querySelector(".accordion-button");
        accordionButton.textContent = category;
    },

    /**
     * Initializes the playlist icon and sets up the click event listener.
     */
    initializePlaylistIcon() {
        // Get playlist icon and columns
        const playlistIcon = document.querySelector(".playlist-icon");
        const leftColumn = document.querySelector(".col-sm-12.left");
        const rightColumn = document.querySelector(".col-sm-12.right");
        playlistIcon.addEventListener("click", () => {
            leftColumn.classList.toggle("expanded");
            rightColumn.classList.toggle("hidden");
            // console.log(item.getAttribute("data-width"));
            // resizeWindow(!leftColumn.classList.contains("expanded"));
        });
    },

    /**
     * Initializes the search functionality and sets up the input event listener.
     */
    initializeSearch() {
        const searchInput = document.getElementById("searchInput");
        const playlistItems =
            this.playlist.getElementsByClassName("playlist_item");

        searchInput.addEventListener("input", function (event) {
            const searchString = event.target.value.toLowerCase();

            Array.from(playlistItems).forEach(function (item) {
                const title = item
                    .querySelector(".item_title")
                    .textContent.toLowerCase();
                const studio = item
                    .querySelector(".item_studio")
                    .textContent.toLowerCase();
                const artist = item
                    .querySelector(".item_artist")
                    .textContent.toLowerCase();

                if (
                    title.includes(searchString) ||
                    artist.includes(searchString) ||
                    studio.includes(searchString)
                ) {
                    item.classList.remove("hidden");
                } else {
                    item.classList.add("hidden");
                }
            });
        });
    },

    playNewVideo(videoid) {
        $.ajax({
            url: "process.php",
            type: "POST",
            data: {
                submit: "nextVideo",
                videoid: videoid,
            },
            cache: false,
            success: function (data) {
                console.log(data);
                window.location.href = data;
            },
        });
    },
    /**
     * Plays the visible playlist item.
     */
    playVisibleItem(direction) {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const videoid = urlParams.get("id");

        const visibleItems = Array.from(
            this.playlist.querySelectorAll(
                ".playlist_item:not(.hidden)[data-category]"
            )
        );

        const activeVisibleItem = visibleItems.find((item) =>
            item.classList.contains("active")
        );

        if (visibleItems.length > 0) {
            let visibleIndex;
            if (activeVisibleItem) {
                const activeVisibleIndex =
                    visibleItems.indexOf(activeVisibleItem);
                if (direction === "next") {
                    visibleIndex =
                        (activeVisibleIndex + 1) % visibleItems.length;
                } else {
                    visibleIndex =
                        (activeVisibleIndex - 1 + visibleItems.length) %
                        visibleItems.length;
                }
            } else {
                visibleIndex = 0;

                if (videoid != null) {
                    visibleItems.forEach((item) => {
                        const tindex = visibleItems.indexOf(item);
                        const playlistItem = visibleItems[tindex];
                        var plVideoId =
                            playlistItem.getAttribute("data-videoid");
                        if (plVideoId == videoid) {
                            visibleIndex = tindex;
                        }
                    });
                }
            }

            const visibleItem = visibleItems[visibleIndex];
            const index = this.playlistItems.indexOf(visibleItem);
            const track = this.tracks[index];
            this.handlePlaylistItemClick(visibleItem, track);
            // Scroll to the active visible item
            visibleItem.scrollIntoView({
                behavior: "smooth",
                block: "nearest",
            });
        }
    },

    initialiseChapter() {
        var playlerText = document.querySelector(
            ".player_container .player_text"
        );
        const parentElement = document.querySelector(".player_container");

        parentElement.addEventListener("contextmenu", (event) => {
            event.preventDefault();

            var timeCode = event.target.getAttribute("aria-valuenow");
            var videoid = playlerText.getAttribute("data-videoid");

            togglePopup(event.pageX, event.pageY, videoid, timeCode);
            console.log("line 744", timeCode, videoid);
        });
    },
    /**
     * Initializes the SimpleBar for the playlist.
     */
    initialiseSimpleBar() {
        const simpleBar_1 = document.getElementById("playlist");
        new SimpleBar(simpleBar_1, { autoHide: true });
        const simpleBar_2 = document.getElementById("acc-content");
        new SimpleBar(simpleBar_2, { autoHide: true });
    },

    /**
     * Selects the first visible playlist item.
     */
    selectFirstVisibleItem() {
        const activeItem = this.playlist.querySelector(".playlist_item.active");

        if (!activeItem) {
            const visibleItems = Array.from(
                this.playlist.querySelectorAll(
                    ".playlist_item:not(.hidden)[data-category]"
                )
            );

            if (visibleItems.length > 0) {
                const firstVisibleItem = visibleItems[0];
                const index = this.playlistItems.indexOf(firstVisibleItem);
                const track = this.tracks[index];

                this.player.on("ready", () => {
                    this.handlePlaylistItemClick(firstVisibleItem, track);
                });
            }
        }
    },
};

document.addEventListener("DOMContentLoaded", () => {
    PlayerApp.initialize();

    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0;
    // factory(window.jQuery);

    // resizeWindow(false)
    resize();
});

window.addEventListener("resize", resize, false);

// video.height = 100; /* to get an initial width to work with*/
function togglePopup(x_pos, y_pos, videoId, timeCode) {
    y_pos = y_pos - 100;
    const overlay = document.getElementById("popupOverlay");
    overlay.classList.toggle("show");
    overlay.style.position = "absolute";
    overlay.style.left = x_pos + "px";
    overlay.style.top = y_pos + "px";

    videoIdInput = document.getElementById("chapterVideoid");
    videoIdInput.value = videoId;

    IdInput = document.getElementById("chapterId");
    IdInput.value = videoId;

    timeCodeInput = document.getElementById("chapterTimeCode");
    timeCodeInput.value = timeCode;

    console.log(x_pos, y_pos, videoId, timeCode);
}

function resize() {
    var videoTag = document.getElementsByTagName("video");
    const video = videoTag[0];

    //     videoRatio = video.height / video.width;
    // windowRatio = window.innerHeight / window.innerWidth; /* browser size */

    //     if (windowRatio < videoRatio) {
    //         if (window.innerHeight > 50) { /* smallest video height */
    //         video.height = window.innerHeight;

    //         } else {
    //             video.height = 50;
    //     }
    //     } else {
    //         video.width = window.innerWidth;
    //     }
    //     text= "Resising Window to W:"+video.width + " H:" + video.height;
    //     console.log(text)
}
function updateOptions(id) {
    $.ajax({
        url: "process.php",
        type: "POST",
        data: {
            submit: "jquery",
            id: id,
        },
        success: function (data) {
            var outletOptions = document.querySelector(".videoPlaylistButton");
            Array.from(outletOptions).forEach((option) => {
                outletOptions.removeChild(option);
            });

            var myArray = JSON.parse(data);
            var opt = document.createElement("option");

            opt.appendChild(document.createTextNode("Select from List"));
            opt.classList.add("filter-option");
            opt.disabled = true;
            outletOptions.appendChild(opt);

            myArray.map((optionData) => {
                var opt = document.createElement("option");

                opt.appendChild(document.createTextNode(optionData[1]));
                opt.classList.add("filter-option");
                opt.value = optionData[0];
                if (optionData[2] == true) {
                    opt.classList.add("selected");
                }
                if (optionData[3] == true) {
                    opt.classList.add("disabled");
                }

                opt.selected = optionData[2];
                opt.disabled = optionData[3];
                outletOptions.appendChild(opt);
            });
        },
    });
}

function setInfoText(className, item) {
    // console.log("setInfoText" , className, item.getAttribute("data-title"))
    var TitleId = "." + className + "_title";
    const textTitle = document.querySelector(TitleId);
    if (textTitle != null) {
        textTitle.textContent = item.getAttribute("data-title");
    }

    var artistId = "." + className + "_artist";
    const textartist = document.querySelector(artistId);
    if (textartist != null) {
        if (item.getAttribute("data-artist") == "") {
            textartist.classList.add("hidden"); // Add 'hidden' class
            textartist.textContent = "";
        } else {
            textartist.textContent = item.getAttribute("data-artist");
        }
    }

    var genreId = "." + className + "_genre";
    const textgenre = document.querySelector(genreId);
    if (textgenre != null) {
        textgenre.textContent = item.getAttribute("data-genre");
    }

    var studioId = "." + className + "_studio";
    const textstudio = document.querySelector(studioId);
    if (textstudio != null) {
        textstudio.textContent = item.getAttribute("data-studio");
    }

    const textvideoid = document.querySelector("#videoPlaylistVideoId");

    const playerTextInfo = document.querySelector(".player_" + className);
    if (playerTextInfo != null) {
        playerTextInfo.setAttribute("href", item.getAttribute("data-pUrl"));

        textvideoid.value = item.getAttribute("data-videoid");
        playerTextInfo.setAttribute(
            "data-videoid",
            item.getAttribute("data-videoid")
        );
        playerTextInfo.setAttribute(
            "onclick",
            "videoCard(" + textvideoid.value + ")"
        );
    }
}

function seektoTime(timeCode) {
    PlayerApp.player.currentTime = timeCode;
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function formatTime(timeInSeconds) {
    const result = new Date(timeInSeconds * 1000).toISOString().substr(11, 8);

    return {
        minutes: result.substr(3, 2),
        seconds: result.substr(6, 2),
    };
}

function addChapter() {
    var action = document.getElementById("chapterAction").value;

    var timeCode = document.getElementById("chapterTimeCode").value;
    var videoid = document.getElementById("chapterVideoid").value;
    var playlistid = document.getElementById("chapterPlaylistId").value;
    var name = document.getElementById("name").value;

    //console.log(timeCode)

    //chapterVideoid
    $.ajax({
        url: "process.php",
        type: "POST",
        data: {
            action: action,
            videoId: videoid,
            id: videoid,
            timeCode: timeCode,
            playlist_id: playlistid,
            name: name,
            exit: true,
        },
        cache: false,
        success: function (data) {
            console.log(data);
        },
    });
}
