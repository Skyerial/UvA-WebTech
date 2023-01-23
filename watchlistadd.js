// // The function getCookie is not written by myself but by jac. The code is
// // retrieved from https://stackoverflow.com/questions/5968196/
// // how-do-i-check-if-a-cookie-exists. The page was last visited on 18-01-2023.
// function getCookie(name) {
//     var dc = document.cookie;
//     var prefix = name + "=";
//     var begin = dc.indexOf("; " + prefix);
//     if (begin == -1) {
//         begin = dc.indexOf(prefix);
//         if (begin != 0) return null;
//     }
//     else {
//         begin += 2;
//         var end = document.cookie.indexOf(";", begin);
//         if (end == -1) {
//             end = dc.length;
//         }
//     }
//     // because unescape has been deprecated, replaced with decodeURI
//     //return unescape(dc.substring(begin + prefix.length, end));
//     return decodeURI(dc.substring(begin + prefix.length, end));
// }

// function destroy_cookies() {
//     document.cookie = "filmTitle=; expires=Thu, 01 Jan 1970 00:00:00 UTC;secure";
//     document.cookie = "filmPoster=; expires=Thu, 01 Jan 1970 00:00:00 UTC;secure";
//     document.cookie = "filmService=; expires=Thu, 01 Jan 1970 00:00:00 UTC; secure";
//     document.cookie = "playlist=; expires=Thu, 01 Jan 1970 00:00:00 UTC; secure";
// }

// const filmTitle = "Test";
// const filmPoster = "https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Placeholder_view_vector.svg/681px-Placeholder_view_vector.svg.png";
// const filmServiceImg = "https://www.tekstmaatje.nl/wp-content/uploads/2020/06/placeholder.png";
// const filmService = "netflix"; // No capitals!!! Output needs to match DB!!!

// function sendRequest() {
//     const xhttp = new XMLHttpRequest(); // Create object to exchange data.
//     // Open connection to add.php (GET means retrieve data):
//     xhttp.open("GET", "playlist.php");
//     xhttp.send(); // Send request to the server.
// }

// function to_watch() {
//     document.cookie = "filmTitle=" + filmTitle + ";secure";
//     document.cookie = "filmPoster=" + filmPoster + ";secure";
//     document.cookie = "filmService=" + filmService + ";secure";
//     document.cookie = "playlist=" + "future watching" + ";secure";

//     sendRequest();

//     // Destroy the cookies after using them, so that the DB cannot be
//     // overloaded. There is a delay because JS is too fast in deleting the
//     // cookies.
//     setTimeout(destroy_cookies, 1000);
// }

// function cur_watching() {
//     document.cookie = "filmTitle=" + filmTitle + ";secure";
//     document.cookie = "filmPoster=" + filmPoster + ";secure";
//     document.cookie = "filmService=" + filmService + ";secure";
//     document.cookie = "playlist=" + "currently watching" + ";secure";

//     sendRequest();
//     setTimeout(destroy_cookies, 1000);
// }

// function watched() {
//     document.cookie = "filmTitle=" + filmTitle + ";secure";
//     document.cookie = "filmPoster=" + filmPoster + ";secure";
//     document.cookie = "filmService=" + filmService + ";secure";
//     document.cookie = "playlist=" + "finished watching" + ";secure";

//     sendRequest();
//     setTimeout(destroy_cookies, 1000);
// }
