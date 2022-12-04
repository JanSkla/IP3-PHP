<?php
require_once('inc/dbconnection.php');

//database//

$id = filter_input(INPUT_GET,
    'employeeId',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range'=> 1]]
);


if ($id === null || $id === false) {
    http_response_code(400);
    $status = "bad_request";
} else {
    $rowTitles = ['Jméno',
'Příjmení',
'Pozice',
'Mzda',
'Místnost'];

    $stmt = $pdo->prepare('SELECT e.name, e.surname, e.job, e.wage, r.name AS roomname, e.room FROM employee e
    JOIN room r ON r.room_id = e.room AND e.employee_id = :id');
    
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        $status = "not_found";
    } else { //OK
        $page='';
        $employee = $stmt->fetch();
        $status = "OK";

        $page .= '<h1>Karta osoby: '.$employee->name.' '.$employee->surname.'</h1>';
        $page .= '<d1 class="dl-horizontal">';
        $i = 0;
        foreach ($employee as $key => $value) {
            $a1 = '';
            $a2 = '';
            if($key == "roomname"){
                $a1 = '<a href=room.php?roomId='.$employee->room.'>';
                $a2 = '</a>';
            }
            elseif($key == "room"){
                break;
            }
            $page .= '<dt>'.(isset($rowTitles[$i])?$rowTitles[$i]:$key).'</dt>'.$a1.'<dd>'.$value.'</dd>'.$a2;
            $i++;
        }

        $stmt = $pdo->prepare('SELECT r.name, r.room_id FROM room r
        JOIN `key` k ON r.room_id = k.room AND k.employee = :id');
        
        $stmt->execute(['id' => $id]);

        $page .= '<dt>Klíče</dt>';
        while ($row = $stmt->fetch()) {
            $page .= '<a href=room.php?roomId='.$row->room_id.'><dd>'.$row->name.'</dd></a>';
        }
        $page .= '</d1>';
        $page .= '<a href="employees.php"><button class="btn-primary">Vrátit se zpět</button></a>';
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <title>Document</title>
</head>
<body class="container">
<?php
switch ($status) {
    case "bad_request":
        echo "<h1>Error 400: Bad request</h1>";
        break;
    case "not_found":
        echo "<h1>Error 404: Not found</h1>";
        break;
    default:
        echo $page;
        break;
}
?>
</body>
</html>