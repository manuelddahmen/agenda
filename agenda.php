<?php
$db = new MyDB();
if (!$db) {
    echo $db->lastErrorMsg();
} else {
    echo "Opened database successfully\n";
}
function findInfoTable($tablename)
{
    global $db;
    $sql = "SELECT m.name as table_name, p.name as column_name FROM sqlite_schema AS m  JOIN pragma_table_info(m.name) AS p ON  m.name ='" . $tablename . "' ORDER BY m.name";
    echo $sql;
    $result = $db->prepare($sql);
    print_r($result->fetchAll());
}

//phpinfo();
if (isset($_GET['action']) && $_GET['action'] != null) {
    passForm($_GET, $_GET['action']);


    $sql = "SELECT * FROM sqlite_schema WHERE type ='table' AND name LIKE 'table_%';";
    printTable("sqlite_schema", array("type", "name", "tbl_name", "sql"),
        array("varchar", "varchar", "tbl_name",
            "integer", "varchar"), array("rootpage"), array("integer"), "schema");;
    if ($_GET['action'] == "save") {
        $id = $_GET['id'];
        $idName = $_GET['idName'];
        $table = $_GET['table'];
        $fields = array();
        foreach ($_GET as $keyName => $fieldValue) {
            if (str_starts_with($keyName, "data/")) {
                $fieldName = substr($keyName, strlen("data/"));
                $fields[$fieldName] = $fieldValue;
            }
        }


        findInfoTable($table);

        $sql = "update " . $table . " set ";
        foreach ($fields as $name => $value) {
            $sql .= $name . "='" . $value . "',";
        }
        $sql = trim($sql, ",");
        $sql .= " where " . $idName . "=" . $id;
    }

    echo $sql;

    $db->prepare($sql);
}

function utilTableLinks($ids, $idsName, $idsType)
{

}

function utilTableLinksIds($ids, $idsName, $idsType): string
{
    $ids2 = "" . $idsName[0] . "=" . $ids[0] . "&";
    for ($id = 1; $id < count($idsName); $id++) {
        $ids2 .= $idsName[$id] . "=" . $ids[$id] . "&";
    }
    return trim($ids2, '&');
}

function utilTableRowWithId(
    $columns, $columnsNames, $columnsType,
    $ids, $idsName, $idsType, $formId, $insert = false)
{

}


class MyDB
{
    private $handle = NULL;

    function __construct()
    {
        $str = "Error opening database";
        $this->handle = new SQLitePDO("database_agenda");
        $this->handle->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function query($query)
    {
        sqlsrv_query($this->handle,
            $query);
    }

    public function execute($array)
    {
        return $this->handle->execute($array);
    }

    public function prepare($sql)
    {
        return $this->handle->prepare($sql);
    }
}

function printTable($tablename, $columnsNames,
                    $columnsType, $idsName, $idsType,
                    $idForm)
{
    $db = new MyDB();
    if (!$db) {
        echo $db->lastErrorMsg();
    } else {
        echo "Opened database successfully\n";
    }
    $id2 = '';
    for ($i = 0; $i < count($idsName) - 1; $i++)
        $id2 = $id2 . ",";
    $id2 = $id2 . "," . $idsName[count($idsName) - 1];
    $id2 = trim($id2, ',');
    $sql = "select " . $id2 . ",* from " . $tablename . ";";
    echo $sql;
    $stmt = $db->prepare($sql);
    $stmt->execute();
    //$stmt->setFetchMode(PDO::FETCH_CLASS);
    $result = $stmt->fetchAll();

    echo "<table>";

    echo "<tr>";
    for ($c = 0; $c < count($columnsNames); $c++) {
        echo "<td>" . $columnsNames[$c] . "</td>";
    }
    echo "</tr>";


    for ($i = 0; $i < count($result); $i++) {
        echo "<tr>";
        for ($c = 0; $c < count($columnsNames); $c++) {
            echo "<td>" . $result[$i][$columnsNames[$c]] . "</td>";
        }

        $ids = "&" . $idsName[0] . "=" . $result[$i][$idsName[0]] . "&" . $result[$i][$idsName[0]];
        $ids1 = array();
        $ids1[0] = $result[$i][$idsName[0]];
        for ($id = 1; $id < count($idsName); $id++) {
            $ids1[$id] = $result[$i][$idsName[$id]];
            $ids .= "?&$idsName[$id]=" . $result[$i][$idsName[$id]];
        }
        echo "<td>Edit row";
        print_edit_link($tablename, $idsName, $idsType,
            $ids1);
        echo "</td>" . "<td>Delete row";
        print_delete_link($tablename, $idsName, $idsType,
            $ids1);
        echo "</td>" . "</tr>";
    }
    echo "</table>";
    print_add_link($tablename, $idsName, $idsType, 0);

}

function print_delete_link($tablename, $idsName, $idsType, $ids)
{
    echo "<a href='?action=delete&table=" . $tablename . "&" . "id=" . ($ids[0]) . "&idName=" . ($idsName[0]) . "'>Delete item from table " . $tablename . "</a>";

}

function print_edit_link($tablename, $idsName, $idsType, $ids)
{
    echo "<a href='?action=edit&table=" . $tablename . "&" . "id=" . ($ids[0]) . "&idName=" . ($idsName[0]) . "'>Edit item from " . $tablename . "</a>";

}


function print_add_link($tablename, $idsName, $idsType)
{
    echo "<a href='?action=add&table=" . $tablename . "&" . "id=" . (0) . "&idName=" . ($idsName[0]) . "'>Add item to table $tablename</a>";
}

function printFormEdit($tablename, $idName, $id)
{
    echo "<form>";
    echo "<table>";
//    $columnsNames = getColumnsName($tablename);
//    $columnsType = getColumnsType($tablename);
//    $idsName = getIdsName($tablename);
//    $id0 = '';
//    for ($i = 0; $i < count($idsName) - 1; $i++)
//        $id0 = $id0 . ",";
//    $id0 = $id0 . "," . $idsName[count($idsName) - 1];
//    for ($i = 0; $i < count($idsName) - 1; $i++)
//        $id2 = $id2 . "=" . $ids[$i] . " and ";
//    $id2 = $id2 . " " . $idsName[count($idsName) - 1] . " = " . $ids[count($idsName) - 1];
//    global $db;
//    if ($ids == NULL) {
//        foreach ($columnsNames as $columnsName) {
//            $result[0][$columnsNames] = "";
//
//        }
//    } else { // EDIT
    $sql = "select " . $idName . ", * from " . $tablename . "
            where " . $idName . "=" . $id . ";";
    echo $sql;
    global $db;
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    foreach ($result[0] as $key => $value) {
        echo "<tr><td>" . $key .
            "</td><td><input type='text' 
                name='data/" . $key . "' "
            . " value='"
            . $value . "'></td></tr>";
    }
    echo "<input type='hidden' name='table' value='" . $_GET['table'] . "' /></table>";
    echo "<input type='hidden' name='idName' value='" . $_GET['idName'] . "' /></table>";
    echo "<input type='hidden' name='id' value='" . $_GET['id'] . "' /></table>";
    echo "<input type='hidden' name='action' value='save' /></table>";
    echo "<input type='submit' value='' /></table>";
    echo "</form>";
}

function passForm($input_get, $idForm)
{
    foreach ($input_get as $name => $value) {
        echo "name=" . $name . ",value=" . $value . "<br/>";
    }
    switch ($idForm) {
        case "edit":
            echo "<p>DEBUG Edit item. Display form</p>";
            printFormEdit($_GET["table"], $_GET['idName'], $_GET['id']);
            break;
        case "add":
            echo "<p>DEBUG Add item. Display form</p>";
            printFormEdit($_GET["table"], $_GET['idName'], 0);
            break;
        case "delete":
            echo "<p>DEBUG delete item. Display confirmation</p>";
            printFormDelete($_GET["table"], $_GET['idName'], $_GET['id']);
            break;
    }
}

function printFormDelete(mixed $table, mixed $idName, mixed $id)
{

}

?>
<html>
<head>
    <title>Agenda</title>
</head>
<body>
<h1>Liste des patients</h1>
<?php

class SQLitePDO extends PDO
{
    private $sem;

    function __construct($filename)
    {
        $filename = realpath($filename);
        parent::__construct('sqlite:' . $filename);

        $key = ftok($filename, 'a');
    }

    function query($statement, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, ...$fetch_mode_args)
    {
        parent::query($statement, $mode, $fetch_mode_args); // TODO: Change the autogenerated stub
    }

    function beginTransaction()
    {
        sem_acquire($this->sem);
        return parent::beginTransaction();
    }

    function commit()
    {
        $success = parent::commit();
        return $success;
    }

    function rollBack()
    {
        $success = parent::rollBack();
        return $success;
    }
}


printTable("table_hospitalises", array('nom', 'prenom', 'sex'),
    array('varchar', 'varchar', 'varchar'),
    array('chambre'), array('integer'), 'AddEditHospi'
);
?>
<h1>Liste des employés</h1>
<?php
printTable("table_animateurs", array('nom', 'prenom'),
    array('varchar', 'varchar', 'varchar'),
    array('no_employe'), array('integer'),
    'AddEditHospi'
);
?>
<h1>Liste des activites</h1>
<?php
printTable("table_event", array('nom', 'recurence'),
    array('varchar', 'varchar'),
    array('id'), array('integer'),
    'AddEditHospi'
);
?>
<h1>Liste des personnes en activité</h1>
<?php
printTable("table_activites", array('nom', 'tache_calendrier', 'no_animateur'),
    array('varchar', 'integer', 'integer'),
    array('id_activite'), array('integer'),
    'AddEditHospi'
);
?>
<a href="/agenda/add.php">Ajouter une activité</a>

</body>
</html>
