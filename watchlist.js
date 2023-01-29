function openWatch(evt, watchName) {
    var i, tabcontent, tablinks;
    //document.location.reload()
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(watchName).style.display = "block";
    evt.currentTarget.className += " active";
}


document.getElementById("defaultOpen").click();