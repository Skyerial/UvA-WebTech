function searchbutton() {
    var x = document.getElementById("title");
    var z = document.getElementById("titletext");
    var e = document.getElementById("contentID");

    if (x.style.display === "none") {

    } else {
        x.style.height = '0px'
        z.style.fontSize = '0px'
        e.style.background = '#6c848c'
        var delayInMilliseconds = 1500; //1 second

        setTimeout(function() {
            x.style.display = "none";

        }, delayInMilliseconds);
    }

    var y = document.getElementById("navdiv");
    y.style.paddingBottom = '2vh';
    //y.style.paddingBottom = parseFloat(getComputedStyle(y).paddingBottom) - 5 + 'px';
}
