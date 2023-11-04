/*
 * Copyright (c) 2023. Manuel Daniel Dahmen
 *
 *
 *    Copyright 2012-2023 Manuel Daniel Dahmen
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */


let xmlHttpRequest;

function chkbox(this1, w_or_d) {
    var s = this1.value;
    if (this1.checked) {
        d.push(s);
    } else {
        var index = d.indexOf(s);
        if (index > -1) {
            d.splice(index, 1);
        }
    }
    let url = "agenda2.php?data=" + w_or_d;
    xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onload = reqListener;
    xmlHttpRequest.withCredentials = true;
    xmlHttpRequest.open("get", url, true);
    xmlHttpRequest.send();
}

function reqListenerDivEditTache() {
    let divFloat = document.getElementById("float-windows");
    console.log(this.responseText);
    divFloat.innerHTML = xmlHttpRequest.responseText;
}

function addActivite(param) {

}


// Get all the table headers
var ths = document.getElementsByTagName("th");

var selected = null;

// Loop through each header and add a click event listener
for (var i = 0; i < ths.length; i++) {
    ths[i].addEventListener("click", function () {

        // Get the index of the clicked header
        var index = Array.prototype.indexOf.call(ths, this);

        // Get all the rows in the table
        var trs = document.getElementsByTagName("tr");

        // Loop through each row and toggle the visibility of the column
        for (var j = 0; j < trs.length; j++) {
            var td = trs[j].getElementsByTagName("td")[index];
            if (td) {
                td.style.backgroundColor = "#FFFFFF";
                selected = td.innerText;
            }
        }
    });
}

function drag(event) {
    event.dataTransfer.setData("text", event.target.id);
}

function allowDrop(event) {
    event.preventDefault();
}

function drop(event) {
    event.preventDefault();
    var data = event.dataTransfer.getData("text");
    event.target.appendChild(document.getElementById(data));
}

function popupForm(day, hour, quart) {
    let url = "?page=popup&day="+day+"&hour="+hour+"&quart="+quart;


}
temp = "";
tempForm = false;
// When the user clicks on <div>, open the popup
function TableForm(td) {
    let form = document.getElementById("add_groupe_personnel");
    let html = form.innerHTML;
    if(tempForm) {
        temp = td.innerHTML;
        td.innerHTML = form.innerHTML;
    } else {
        form.innerHTML = temp;
        form.innerHTML = td.innerHTML;
    }
    tempForm = !tempForm;
}