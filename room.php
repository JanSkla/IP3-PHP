<?php
require_once('inc/dbconnection.php');

//database//

$id = filter_input(INPUT_GET,
    'roomId',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range'=> 1]]
);


if ($id === null || $id === false) {
    http_response_code(400);
    $status = "bad_request";
} else {
    $rowTitles = ['Číslo',
'Název',
'Telefon'];

    $stmt = $pdo->query('SELECT `no`, `name`, phone FROM room WHERE room_id = '.$id);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        $status = "not_found";
    } else { //OK
        $page='';
        $employee = $stmt->fetch();
        $status = "OK";

        $page .= '<h1>Místnost č. '.$employee->no.'</h1>';
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

        $stmt = $pdo->query('SELECT CONCAT(`name`," ",surname) AS fullname, employee_id FROM employee WHERE room ='.$id);

        $page .= '<dt>Lidé</dt>';
        
        while ($row = $stmt->fetch()) {
            $page .= '<a href=employee.php?employeeId='.$row->employee_id.'><dd>'.$row->fullname.'</dd></a>';
        }

        $stmt = $pdo->query('SELECT AVG(wage) AS wage FROM employee WHERE room ='.$id);
        
        $employee = $stmt->fetch();
        $page .= '<dt>Průměrná mzda</dt>';
        $page .= '<dd>'.number_format($employee->wage, 2, '.', ',').'</dd>';
        
        $stmt = $pdo->query('SELECT CONCAT(e.name," ", e.surname) AS fullname, e.employee_id FROM employee e
        JOIN `key` k ON e.employee_id = k.employee WHERE k.room = '.$id);

        $page .= '<dt>Klíče</dt>';
        while ($row = $stmt->fetch()) {
            $page .= '<a href=employee.php?employeeId='.$row->employee_id.'><dd>'.$row->fullname.'</dd></a>';
        }

        $page .= '</d1><br/>';
        $page .= '<a href="rooms.php"><button class="btn-primary">Vrátit se zpět</button></a>';
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