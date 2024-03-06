//DEFINES

const player = document.getElementById('playerBox');

//video object
const video = document.getElementById('video');

//buttons
const play = document.getElementById('playButton');
const mute = document.getElementById('muteButton');

//button icons
const playIcon = document.getElementById('playIcon');
const pauseIcon = document.getElementById('pauseIcon');
const muteIcon = document.getElementById('muteIcon');
const unmuteIcon = document.getElementById('unmuteIcon');

//seekbar
const seekProgress = document.getElementById('seekProgress');
const seekHandle = document.getElementById('seekHandle');

//context menu
const contextMenu = document.getElementById('playerContextMenu');
const contextAbout = document.getElementById('contextAbout');
const contextMute = document.getElementById('contextMute');
const contextLoop = document.getElementById('contextLoop');
const muteTick = document.getElementById('muteTick');
const loopTick = document.getElementById('loopTick');

//about box
const aboutBox = document.getElementById('aboutBox');
const aboutCloseBtn = document.getElementById('aboutCloseBtn');

//FUNCTIONS

//disable native controls
video.controls = false;

//replace standart context menu and disallow standard right-click
player.addEventListener("contextmenu", (event) => {

    event.preventDefault();
  
    const { clientX: mouseX, clientY: mouseY } = event;
  
    contextMenu.style.top = `${mouseY}px`;
    contextMenu.style.left = `${mouseX}px`;
  
    contextMenu.style.display = "block";
  
});

//hide context menu on click
document.addEventListener("click", (e) => {
if (e.target.offsetParent != contextMenu) {
    contextMenu.style.display = "none";
}
});

//hide context menu on scroll
document.addEventListener('scroll', function() {
contextMenu.style.display = "none";
});

//play video (crude autoplay workaround)
video.load()
video.play()

function togglePlay() {
    if (video.paused || video.ended) {
      video.play();
      playIcon.classList.add("hidden");
      pauseIcon.classList.remove("hidden");
    } else {
      video.pause();
      playIcon.classList.remove("hidden");
      pauseIcon.classList.add("hidden");
    }
}

function toggleMute() {
    video.muted = !video.muted;
    muteIcon.classList.toggle("hidden");
    unmuteIcon.classList.toggle("hidden");
}

function initializeVideo() {
    const videoDuration = Math.round(video.duration);
    seekHandle.setAttribute('max', videoDuration);
    seekProgress.setAttribute('max', videoDuration);
    updateProgress();
    detectAutoplay();
}

function detectAutoplay() {
    if (video.paused || video.ended) {
        playIcon.classList.remove("hidden");
        pauseIcon.classList.add("hidden");
    } else {
        playIcon.classList.add("hidden");
        pauseIcon.classList.remove("hidden");
    }
}

function skipAhead(event) {
  const skipTo = event.target.dataset.seekHandle ? event.target.dataset.seekHandle : event.target.value;
  video.currentTime = skipTo;
  seekProgress.value = skipTo;
  seekHandle.value = skipTo;
}

function updateProgress() {
  seekHandle.value = Math.floor(video.currentTime);
  seekProgress.value = Math.floor(video.currentTime);
}

//mute option in context menu
function videoContextMute() {
    toggleMute();
    muteTick.classList.toggle("hidden");
    contextMenu.style.display = "none";
}
  
//loop option, in same place
function videoContextLoop() {
    video.loop = !video.loop;
    loopTick.classList.toggle("hidden");
    contextMenu.style.display = "none";
}

//display and close about box
function displayAbout() {
    contextMenu.style.display = "none";
    aboutBox.style.display = "flex";
}
  
aboutCloseBtn.addEventListener('click', function() {
aboutBox.style.display = "none";
})

//LISTENERS
//inits
video.addEventListener('loadedmetadata', initializeVideo);

//updaters
video.addEventListener('timeupdate', updateProgress);


//playback control listeners
playButton.addEventListener('click', togglePlay);
seekHandle.addEventListener('input', skipAhead);

video.addEventListener('ended', detectAutoplay);

//volume control listeners
mute.addEventListener('click', toggleMute);

//context menu listeners
contextAbout.addEventListener('click', displayAbout);
contextMute.addEventListener('click', videoContextMute);
contextLoop.addEventListener('click', videoContextLoop);
