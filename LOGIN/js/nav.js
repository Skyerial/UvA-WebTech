function switch_mode() {
    document.body.classList.toggle("dark-mode");
}

function change_logo() {
    let logo = document.getElementById("logo");
    if (logo.src.match("images/logo1.png")) {
        logo.src = "images/logo2.png"
    } else {
        logo.src = "images/logo1.png"
    }
}