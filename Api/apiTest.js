const options = {
    method: "GET",
    headers: {
        "apiKey": "822d9746853668f907a5cd20eba5215d3a6b72c4a9452dfac502bc44e3951b2f"
    }
};

function ownApiCall() {
    fetch("https://webtech-in01.webtech-uva.nl/~danielo/v1/spiderman", options)
    // .then(response => response.json)
    .then(response => console.log(response))
    .catch(err => console.error(err));
}