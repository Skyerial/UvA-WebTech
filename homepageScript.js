let htmlStringVisible = `<div class="loader" id="loaderID"></div>`;
let htmlString = `<div class="loader" id="loaderID" style="visibility: hidden;"></div>`;

function showLoadingAnimation() {
    document.getElementById('cardcontainerID').classList.toggle('open');
    var containerLoading = document.getElementById('cardcontainerID');
    containerLoading.innerHTML = htmlStringVisible;
    for (var i = 0; i < 6; i++) {
        containerLoading.innerHTML += htmlString;
    }
}

function removeLoadingAnimation() {
    for (var i = 0; i < 7; i++) {
        var animationCard = document.getElementById('loaderID');
        animationCard.remove();
    }
}