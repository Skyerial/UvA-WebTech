// This script is used to show the menu if the website is used on a phone or tablet
// It also makes sure that the footer is shown after a valid search
document.getElementById('toggleButtonID').addEventListener('click', showMenu);

function showMenu() {
    document.getElementById('footerID').classList.toggle('open');
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('mainID').classList.toggle('open');
    document.getElementById('logoID').classList.toggle('open');
}

function showFooter() {
    document.getElementById('footerID').style.visibility = 'visible';
    document.getElementById('footerID').style.opacity = '1';
}

function showLogo() {
    document.getElementById('logoID').classList.toggle('open');
}