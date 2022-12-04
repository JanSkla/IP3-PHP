<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap-->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Připojení k DB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="container">
<?php
require_once('inc/dbconnection.php');

//database//
$sortInput = filter_input(INPUT_GET, 'sort');
$sort = null;
$sortType = null;

switch (substr($sortInput, 0, strlen($sortInput)-3)) {
    case 'name':
        $sort = 'name';
        break;
    case 'no':
        $sort = 'no';
        break;
    case 'phone':
        $sort = 'phone';
        break;
}

switch (substr($sortInput, strlen($sortInput)-2)) {
    case 'up':
        $sortType = 'DESC';
        break;
    case 'dw':
        $sortType = 'ASC';
        break;
}


$stmt = $pdo->query('SELECT `name`, `no`, phone, room_id FROM room'.
(($sort && $sortType)?' ORDER BY '.$sort.' '.$sortType:''));

if ($stmt->rowCount() == 0) {
    echo "Záznam neobsahuje žádná data";
} else {
    echo "<table class='table table-striped'>";
    echo "<tr>";
    echoColumnTitle('Jméno','name');
    echoColumnTitle('číslo','no');
    echoColumnTitle('Telefon','phone');
    echo "</tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td><a href=room.php?roomId={$row->room_id}>{$row->name}</a></td><td>{$row->no}</td><td>{$row->phone}</td>"   ;
        echo "</tr>";
    }
    echo "</table>";
    echo '<a href="index.php"><button class="btn-primary">Vrátit se zpět</button></a> ';
    echo '<a href="rooms.php"><button class="btn-primary">Zrušit filtr</button></a>';
}
unset($stmt);

function echoColumnTitle($title,$rowKey){
    global $sort, $sortType;

    $colorDESC = '';
    $colorASC = '';
    
    if($sort == $rowKey){
        if($sortType == 'DESC'){
            $colorDESC = 'yellow';
        }else{
            $colorASC = 'yellow';
        }
    }
    echo '<th>'.$title.'<a href=rooms.php?sort='.$rowKey.'_up><span class="glyphicon glyphicon-arrow-up '.$colorDESC.'"/></a><a href=rooms.php?sort='.$rowKey.'_dw><span class="glyphicon glyphicon-arrow-down '.$colorASC.'"/></a></th>';
}
?>
</body>
</html>