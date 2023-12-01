<?php
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

require_once "framework.php";
global $db;
global $datetime;
?>
    <script type="text/javascript" src="../js/tableToExcel.js"></script>
<script type="text/javascript">
    function convertAndSaveExcel(button) {
        TableToExcel.save(document.getElementById("agenda"), {
            name: "table1.xlsx",
            sheet: {
                name: "Sheet 1"
            }
        });

    }
    function exportF(elem) {
        const table = document.getElementById("agenda");
        const html = table.outerHTML;
        const url = 'data:application/vnd.ms-excel,' + escape(html); // Set your html table into url
        elem.setAttribute("href", url);
        elem.style.display = "visible"
        elem.setAttribute("download", "export.xls"); // Choose the file name
        return false;
    }
</script><!--<button onclick="exportF(document.getElementById('buttonTab'))" value="Ouvrir dans Tableur" >
    <a href="#" id='buttonTab' style="display: none;">Télécharger</a>
</button>-->

<?php
echo "<button onclick='tableToExcel();'>Télécharger feuille de calcul</button>";
echo "<table class='agenda' id='agenda'>";

global $db;

global $userData;
if($userData==NULL) {
    echo "<h2>Non connecté</h2>";
    exit(0);
}

$action = $_GET["getvalues"] ?? "get";

$i = 0;

require_once "getdata_2.php";
$newGetData = new getdata_2($id_hospitalise??-1, $id_hospitalises??array());

global $id_hospitalise;



$newGetData->init();


if($id_hospitalise!=null) {
    ?>
    <script type="text/javascript">

    </script>
        <?php
}



global $id_hospitalise;
$result = joursTaches($id_hospitalise);
checkMultiple("id_hospitalise", $newGetData->retrieveAllPatient("get"), $newGetData->resultPatientsTache ?? array(), "chambre", array("nom", "prenom"),
    "onchange=refreshDataSemaineTaches()");

require_once "footer.php";
?>  </body>
    </html>
<?php
?>