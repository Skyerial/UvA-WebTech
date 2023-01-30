// var mainid = document.getElementById('mainID');
document.getElementById('toggleButtonID').addEventListener('click', showMenu);

function showMenu() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('mainID').classList.toggle('open');
}

function showFooter() {
    document.getElementById('footerID').style.visibility = 'visible';
}