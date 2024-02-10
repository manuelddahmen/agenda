/*
 * Copyright (c) 2023-2024. Manuel Daniel Dahmen
 *
 *

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

function myFunction(x) {
    x.classList.toggle("change");
    let elementById = document.getElementById("menu");
    elementById.classList.toggle("menu-visible");

}


halfHour = [
    "05.30", "06.00", "06.30", "07.00", "07.30",
    "08.00", "08.30", "09.00", "09.30", "10.00",
    "10.30", "11.00", "11.30", "12.00", "12.30",
    "13.00", "13.30", "14.00", "14.30", "15.00", "15.30",
    "16.00",
    "16.30", "17.00", "17.30", "18.00", "18.30",
    "19.00", "19.30", "20.00", "20.30", "21.00",
    "21.30", "22.00", "22.30", "23.00", "23.30", "00.00"
];


function myFunctionSearchMenu() {
    // Declare variables
    let input, filter, ul, li, a, i;
    input = document.getElementById("mySearch");
    filter = input.value.toUpperCase();
    ul = document.getElementById("myMenu");
    li = ul.getElementsByTagName("li");


    let nVisibles = 0;

    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
            nVisibles++;
        } else {
            li[i].style.display = "none";
        }
    }
    if (nVisibles === 0)
        alert("Nouvel élément dans table t et champ c1");
}

function mySearchFunctionChooseTheme(anchorId, rowId = -1) {
    let idName = 'id';
    if (rowId == -1) {
        idName = "id";
        if (anchorId === "hospitalises") {
            idName = "chambre";
        }
        let url = "?page=tables&table=table_" + anchorId + "&action=add&idName=" + idName;
        //document.location.href = url;
    }
    let filter = document.getElementById(anchorId);
    if (filter == null)
        alert("Error item Null: " + anchorId)
    let innerHTML = filter.innerText;
    let box = document.getElementById("mySearch");
    let value = box.value;
    value = innerHTML;
    box.value = value;
    /*
        if (rowId == -1)
            value += "<button onClick='addRow(" + rowId + ");'/>"
        else if (rowId > 0)
            value += "<button onClick='modifyRow(" + rowId + ");'/>"
    */
    let span;
    if (anchorId.startsWith("employe")) {
        span = document.getElementById("employes");
        box.value = "";
        setVisibleClassname("employes", false);

    } else if (anchorId.startsWith("hospitalise")) {
        span = document.getElementById("hospitalises");
        box.value = "";
        setVisibleClassname("hospitalises", false);
    } else if (anchorId.startsWith("tache")) {
        span = document.getElementById("taches");
        box.value = "";
        setVisibleClassname("taches", false);
    } else if (anchorId.startsWith("activite")) {
        span = document.getElementById("activites");
        box.value = "";
        setVisibleClassname("activites", false);

    } else if (anchorId.startsWith("jour__semaine_demie__heure_temps_0")) {
        span = document.getElementById("jour__semaine_demie__heure_temps_0");
        box.value = "";
        setVisibleClassname("jour__semaine_demie__heure_temps_0", false);

    } else if (anchorId.startsWith("jour__semaine_demie__heure_temps_1")) {
        span = document.getElementById("jour__semaine_demie__heure_temps_1");
        box.value = "";
        setVisibleClassname("jour__semaine_demie__heure_temps_1", false);


    } else if (anchorId.startsWith("jour__semaine_demie__heure_temps_2")) {
        span = document.getElementById("jour__semaine_demie__heure_temps_2");
        box.value = "";
        setVisibleClassname("jour__semaine_demie__heure_temps_2", false);

    }
    value = "<span id='" + rowId + "'>" + value + "</span><input type='text' name='" + span.id + "' value='" + value + "' />";
    span.innerHTML = value;

}

function clearSpan(spanId, classname = null) {
    if (classname == null)
        classname = spanId;
    let span = document.getElementById(spanId);
    if (span == null)
        alert("Error item Null: " + spanId);
    span.innerText = "";
    setVisibleClassname(spanId, true);

}

function changeListUpdateButtons() {
    let id_hospitalise = document.getElementById("hospitalises").innerHTML;
    let id_activite = document.getElementById("activities").innerHTML;
    let id_tache = document.getElementById("taches").innerHTML;
    let id_employe = document.getElementById("employes").innerHTML;

    alert("p changed");
}

/*    Patient, Employé? Quand patient voit employé?
Patient, Activité? Quand patient va à l'activité?
Patient, tâche? Quand et avec qui patient va à l'activité?
Employé, activité? Quelle activité y a-t-il avec employé? Qui y va?
Employé, tâche? Quand et quelle activité employé exerce-t-il?
Activité, tâche? Quand qui et quel employé donne l'activité tâche?
*/
function complete(id_patient, id_employe, id_tache, id_activite,
                  jour_semaine, heure, duree) {
    if (id_employe != "" && id_activite != "") {
        // Un des deux est superflu

    }

}

function list(id_patient, id_employe, id_tache, id_activite,
              jour_semaine, heure, duree) {

}

function create(id_patient, id_employe, id_tache, id_activite,
                jour_semaine, heure, duree) {
}

function setVisibleClassname(classname, visible) {
    let elementsByClassNameElements = document.getElementsByClassName(classname);
    let elementsByClassNameElement = null;
    for (elementsByClassNameElement of elementsByClassNameElements) {
        elementsByClassNameElement.classList.toggle("invisible");
    }
}

function changeRow(action_type, row_type) {
    let id_hospitalise = document.getElementById("hospitalises").innerHTML;
    let id_activite = document.getElementById("activites").innerHTML;
    let id_tache = document.getElementById("taches").innerHTML;
    let id_employe = document.getElementById("employes").innerHTML;

    alert(id_hospitalise + " " + id_activite);

    if (action_type === 'add') {
        switch (row_type) {
            case "hospitalise":
                break;
            case "tache":
                break;
            case "activite":
                break;
            case "employe":
                break;
        }
    } else if (action_type === 'delete') {
        switch (row_type) {
            case "hospitalise":
                break;
            case "tache":
                break;
            case "activite":
                break;
            case "employe":
                break;
        }

    } else if (action_type === 'edit') {
        switch (row_type) {
            case "hospitalise":
                break;
            case "tache":
                break;
            case "activite":
                break;
            case "employe":
                break;
        }

    }
}

function refreshDataVueSemaine(select) {
    let value = select.value;
    document.location.href = "?id_hospitalise=" + value;
}

let newTask = false;

let hasChanged = false;

let submit = false;

function isAutoDirectSave() {
    return false;
}

function toggleCheckBox(id) {
    let elementById = document.getElementById(id);
    elementById.checked = !elementById.checked;
}

function refreshDataSemaineTaches(select) {
    let elementById = document.getElementById("edition_activite_submitChanges");
    if (isAutoDirectSave()) {
        elementById.click();
    } else {
        hasChanged = true;

    }
    disable_button();
}

function random() {
    return Math.round(Math.random() * 10000000);
}

function newTaskButton() {
    let buttonFormSubmit = document.getElementById("edition_activite_submitChanges");
    let elementById = document.getElementById("id_tache");
    elementById.value = -1;
    document.location.replace("?id_tache=&page=advent&id_tache=-1");
}

function showEmployesAndActivites() {

}

function commitChanges(tdElemChanged) {
    alert("Utiliser le bouton édition à droite ou le bouton ajouter en bas de la table");
}

function include2(url) {
    xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onload = reqListener2;
    xmlHttpRequest.withCredentials = true;
    xmlHttpRequest.open("get", url, true);
    xmlHttpRequest.send();

}

function disable_button() {
    let buttonFormSubmit = document.getElementById("edition_activite_submitChanges");
    if (buttonFormSubmit != null) {
        buttonFormSubmit.disabled = !hasChanged;
    }
    unloadSetActive();

}

function page_onLoad() {
    disable_button();
    let elementById = document.getElementById("table_edit");
    if (elementById != null) {
        hasChanged = true;
    }
    unloadSetActive();

    listPatients = [];
}

function unloadSetActive() {
    let buttonFormSubmit = document.getElementById("edition_activite_submitChanges");
    if (buttonFormSubmit != null) {
        buttonFormSubmit.onclick = ev => {
            hasChanged = false;
        }
    }
    buttonFormSubmit = document.getElementById("submitButtonTable");
    if (buttonFormSubmit != null) {
        buttonFormSubmit.onclick = ev => {
            hasChanged = false;
        }
    }
    buttonFormSubmit = document.getElementById("cancelButtonTable");
    if (buttonFormSubmit != null) {
        buttonFormSubmit.onclick = ev => {
            hasChanged = false;
        }
    }
    let isUnloadActive = hasChanged && false;

    if (isUnloadActive) {
        window.addEventListener('beforeunload', (event) => {
            event.preventDefault();
            // Google Chrome requires returnValue to be set.
            if (hasChanged) {
                event.returnValue = 'You have unfinished changes!';
                return "Êtes-vous sûr de vouloir quitter sans enregistrer?";
            }
        });
    } else {
        window.document.body.unload = null;
    }

}

function requireConfirmOnReload() {
    hasChanged = true;
    //unloadSetActive();

}

function goto(url) {
    hasChanged = false;
    //unloadSetActive();
    document.location.href = url;
}

function dragSelectedSelectOptions(selectOptionId) {

}

function dragStart(ev, id_select) {
    let elementById = document.getElementById(id_select);
    if (elementById != null) {
        let valueSelect = elementById.value;
        ev.dataTransfer.effectAllowed = 'move';
        let s = id_select + "=" + valueSelect + ";";
        ev.dataTransfer.setData("Text", s);
        ev.dataTransfer.setDragImage(ev.target, 0, 0);
        console.debug(id_select);
        return true;
    } else {
        alert("Error element with id : " + id_select + " equals null");
    }
    return false;
}

function dragEnter(ev) {
    event.preventDefault();
    return true;
}

function dragOver(ev) {
    return false;
}

function dragDrop(ev) {
    const src = ev.dataTransfer.getData("Text");
    //ev.target.appendChild(document.getElementById(src));
    ev.target.innerHTML += src;
    ev.stopPropagation();
    return false;
}

let outputList1;

function loadSearchResultTache(elementById, outputList) {
    let value = elementById.value;
    outputList1 = outputList;

    const oReq = new XMLHttpRequest();
    oReq.onload = reqListener;
    oReq.open("get", "getdata_2.php?table=activites&field_nom_activite=" + value, true);
    oReq.send();
}

let listPatients = [];

function objectToQueryString(obj) {
    var str = [];
    i = 0;
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            str.push("id_hospitalise_" + i + "=" + encodeURIComponent(obj[p]));
        }
        i++;
    }
    return str.join("&");
}

function formDataToQueryString(obj) {
    let str = [];
    i = 0;
    let it = obj.values();
    for(const [key, value] of obj.entries()) {
        str[i] = key + "=" + encodeURIComponent(value);
        i++;
    }
    return str.join("&");
}
let xmlHttpRequest;


let nPatients = 0;
function chkbox(this1) {
    var s = document.getElementById("patients");
    var s2patientId;
    if (this1 != null && this1.value != null) {
        s2patientId = this1.value;
    } else {
        alert("Erreur Null");
    }
    var s1 = null;
    if (s == null || s.innerText == null) {
        s1 = 0;
    } else {
        s1 = parseInt(s.innerText);
    }
    if (this1.checked) {
        nPatients =s1+1;
        if (s2patientId != null) {
            listPatients.push(s2patientId);
        } else {
            listPatients.push(this1.value);
        }

    } else {
        nPatients = s1+nPatients-1;
        var index = listPatients.indexOf(s);
        if (index > -1) {
            listPatients.splice(index, 1);
        }
    }
    var sAfter = document.getElementById("patients");
    if (sAfter != null) {
        sAfter.innerText = nPatients;
    }
    let url = "getdata_2.php?" + objectToQueryString(listPatients);
    xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onload = reqListener;
    xmlHttpRequest.withCredentials = true;
    xmlHttpRequest.open("get", url, true);
    xmlHttpRequest.send();
}

function chkboxGoto(this1) {
    var s = this1.value;
    if (this1.checked) {
        listPatients.push(s);
    } else {
        var index = listPatients.indexOf(s);
        if (index > -1) {
            listPatients.splice(index, 1);
        }
    }
    let url = "index.php?page=agenda&" + objectToQueryString(listPatients);
    document.location.href = url;
}
function reqListenerDivEditTache() {
    let divFloat = document.getElementById("float-windows");
    //console.log(this.responseText);
    divFloat.innerHTML = xmlHttpRequest.responseText;
}

function chkboxViewTache(this1) {
    var s = this1;
    let url = s;
    xmlHttpRequest = new XMLHttpRequest();
    xmlHttpRequest.onload = reqListenerDivEditTache;
    xmlHttpRequest.withCredentials = true;
    xmlHttpRequest.open("get", url, true);
    xmlHttpRequest.send();
}
function reqListener(xml) {
    let tableAgenda = document.getElementById("agenda");
    tableAgenda.innerHTML;
    //console.log(this.responseText);
    tableAgenda.innerHTML = xmlHttpRequest.responseText;
}
function reqListener2(xml) {
    let floatwindows = document.getElementById("float-windows");
    floatwindows.innerHTML = xmlHttpRequest.responseText;
}


function signOut() {
//    var auth2 = gapi.auth2.getAuthInstance();
//    auth2.signOut().then(function () {
//    console.log('User signed out.');});
    document.location.href = "?page=logout";
}
function clickZoomEvent(element)
{
    //let zoom = document.getElementById('currentEventSelected');
    //zoom.innerHTML = element.innerHTML;
    //zoom.style.display = "none";
}

function registrerSubmit() {
    document.forms[0].addEventListener('submit', checkTache);

}

function checkTache(button) {
    try {
        let errors = 0;

        let errorsText = "";

        let id_tache = document.getElementById('id_tache');

        if (id_tache != null && id_tache.value > 0) {
            id_tache.classList.toggle("error", false);
        } else {
            //            id_tache.classList.toggle("error", true);
            //errors = errors + 1;

        }
        let patients = document.getElementById("patients");
        if (listPatients === undefined || listPatients.length === 0) {
            patients.classList.toggle("error", true);
            patients.innerText = listPatients.length;
            //errors = errors + 1;
            errorsText += "\nLa liste des patients est incorrecte. Pas de patient(es) choisi(es)"
        } else if (patients.innerText === "0" || patients.innerText === "Valide") {
            patients.classList.toggle("error", false);
            patients.style.backgroundColor = "#00F";
            patients.innerText = "Valide" + listPatients.length;
        }


        let activites = document.getElementById("id_activite");
        if (activites.value > 0) {
            activites.classList.toggle("error", false);
        } else {
            activites.classList.toggle("error", true);
            errorsText += "\nPas de d'activité choisie"
            errors = errors + 1;
        }


        let jour__semaine_demie__heure_temps_0 = document.getElementById("jour__semaine_demie__heure_temps_0");
        if (jour__semaine_demie__heure_temps_0.value != -1) {
            jour__semaine_demie__heure_temps_0.classList.toggle("error", false);
        } else {
            jour__semaine_demie__heure_temps_0.classList.toggle("error", true);
            errorsText += "\nPas de jour choisi (lundi, mardi,...)";
            errors = errors + 1;
        }
        let jour__semaine_demie__heure_temps_1 = document.getElementById("jour__semaine_demie__heure_temps_1");
        if (jour__semaine_demie__heure_temps_1.value != -1) {
            jour__semaine_demie__heure_temps_1.classList.toggle("error", false);
        } else {
            jour__semaine_demie__heure_temps_1.classList.toggle("error", true);
            errorsText += "\nPas d'heure choisie";
            errors = errors + 1;
        }
        let jour__semaine_demie__heure_temps_2 = document.getElementById("jour__semaine_demie__heure_temps_2");
        if (jour__semaine_demie__heure_temps_2.value != -1) {
            jour__semaine_demie__heure_temps_2.classList.toggle("error", false);
        } else {
            jour__semaine_demie__heure_temps_2.classList.toggle("error", true);
            errorsText += "\nPas de durée choisie";
            errors = errors + 1;
        }

        let editionActivite = document.forms.edition_activite;

        let errorsDiv = document.getElementById("errors");
        if (errors === 0 && button === "save") {
            errorsDiv.style.backgroundColor = "#00F";
            errorsDiv.innerHTML = "Tout ok, go\n\n" + errorsText;
            editionActivite.checkValidity();
            //editionActivite.submit();
            return true;
        } else if (errors === 0 && button === "saveAndNew") {
            errorsDiv.style.backgroundColor = "#00F";
            errorsDiv.innerHTML = "Tout ok, go\n\n" + errorsText;
            editionActivite.checkValidity();
            //editionActivite.submit();
            return true;
        } else {
            errorsDiv.innerHTML = "Il y a des erreurs, corrigez :) !!!\n\n" + errorsText;
            errorsDiv.style.backgroundColor = "#F00";
//            ((HTMLFormElement)(formElement)).reportValidity();
            return false;
        }
    } catch (exception) {
        alert(exception);
        return false;
    }
}
/*
let $TABLE = $(".printTable");
let $BTN = $("#export-btn");
let $EXPORT = $("#export");

$(".table-add").click(function () {
    let $clone = $TABLE
        .find("tr.hide")
        .clone(true)
        .removeClass("hide table-line");
    $TABLE.find("table").append($clone + true);
});

$(".table-remove").click(function () {
    $(this).parents("tr").detach();
});

$(".table-up").click(function () {
    var $row = $(this).parents("tr");
    if ($row.index() === 1) return; // Don't go above the header
    $row.prev().before($row.get(0));
});

$(".table-down").click(function () {
    var $row = $(this).parents("tr");
    $row.next().after($row.get(0));
});
// A few jQuery helpers for exporting only
jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;

$BTN.click(function () {
    var $rows = $TABLE.find("tr:not(:hidden)");
    var headers = [];
    var data = [];

    // Get the headers (add special header logic here)
    $($rows.shift())
        .find("th:not(:empty)")
        .each(function () {
            headers.push($(this).text().toLowerCase());
        });

    // Turn all existing rows into a loopable array
    $rows.each(function () {
        var $td = $(this).find("td");
        var h = {};

        // Use the headers from earlier to name our hash keys
        headers.forEach(function (header, i) {
            h[header] = $td.eq(i).text();
        });

        data.push(h);
    });

    // Output the result
    $EXPORT.text(JSON.stringify(data));
});
*/

// Get the form element
let form = document.getElementById("editFormData");

function sendData() {
    const XHR = new XMLHttpRequest();

    if(form===null) {
        form = document.getElementById("editFormData");
    }

    if(form!=null ) {

        let form1 = form;//


        // Bind the FormData object and the form element
        const FD = new FormData(form1);

        // Define what happens on successful data submission
        XHR.addEventListener("load", (event) => {
            //alert("Yeah! Data sent and response loaded.");
        });

        // Define what happens in case of an error
        XHR.addEventListener("error", (event) => {
            //alert("Oops! Something went wrong.");
        });
        const getUrl = "https://empty3.app/agenda/src/ajax/request_form.php?"+formDataToQueryString(FD);

        //alert(getUrl);


        // Set up our request
        XHR.open("GET", getUrl);

        // The data sent is what the user provided in the form
        XHR.send(FD);
    }
}

if (form !== null) {
    // Add submit event handler
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        sendData();
    });
}

function cancelTableEdit() {
    document.location.href = "index.php?page=tables";
}