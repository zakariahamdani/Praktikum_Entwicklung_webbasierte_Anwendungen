let request = new XMLHttpRequest();
window.onload = function () {
    window.setInterval(requestData, 2000);
}


function requestData() { // fordert die Daten asynchron an
    "use strict";
    //ToDo - vervollständigen **************

    //let str  = document.getElementById("urlInput").value;
    request.open("GET", "KundenStatus.php", true);
    request.onreadystatechange = processData;
    request.send(null);
}

function processData() {
    if (request.readyState == 4) { // Uebertragung = DONE
        if (request.status == 200) {   // HTTP-Status = OK
            if (request.responseText != null)
                process(request.responseText);// Daten verarbeiten
            else console.error("Dokument ist leer");
        }
        else console.error("Uebertragung fehlgeschlagen");
    } else;          // Uebertragung laeuft noch
}

function process(data) {
    var dataObject = JSON.parse(data);

    if (dataObject.length == 0) {
        console.log(dataObject);
        var element = document.getElementById("statusContainer");
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }
        var tag = document.createElement("p");
        var text = document.createTextNode("es gibt kein Bestellung");
        tag.appendChild(text);
        element = document.getElementById("statusContainer");
        element.appendChild(tag);
    } else {
        console.log(dataObject);

        var element = document.getElementById("statusContainer");
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }

        for (i = 0; i < dataObject.length; i++) {
            var tag = document.createElement("p");
            var text = document.createTextNode(dataObject[i].name + ": " + dataObject[i].status);
            tag.appendChild(text);
            element = document.getElementById("statusContainer");
            element.appendChild(tag);
        }
    }

}