<?php

$name = $_POST['name'];
$place  = $_POST['place'];
$pass1  = $_POST['pass1'];
$pass2  = $_POST['pass2'];

if (empty($name) || empty($place) || empty($pass1) || empty($pass2) )
{
    die('Details Missing');
}

$id="";
$p="";
$id_num = (string)random_int ( 100 , 999 );

$n = strtoupper(substr($name,0,1));
$p = strtoupper(substr($place,0,1));
$id = $n.$p.$id_num;

$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "bio";

// Create connection
$conn = new mysqli ($host, $dbusername, $dbpassword, $dbname);

if (mysqli_connect_error()){
  die('Connect Error ('. mysqli_connect_errno() .') '
    . mysqli_connect_error());
}

else{
    $SELECT = "SELECT name From users Where name = ? Limit 1";
    $INSERT = "INSERT Into users (id, name , place)values(?,?,?)";
    $INSERT1 = "INSERT Into cregister (id, pass1 , pass2)values(?,?,?)";
    $ID_CHECK = "SELECT id From users Where id = ?";

    $stmt = $conn->prepare($ID_CHECK);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->store_result();
    $idrow = $stmt->num_rows;
    $stmt->close();

    while($idrow>1){
        $id_num = (string)random_int ( 100 , 999 );
        $id = $n.$p.$id_num;
        $stmt = $conn->prepare($ID_CHECK);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->store_result();
        $idrow = $stmt->num_rows;
        $stmt->close();
    }

    $stmt = $conn->prepare($SELECT);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->store_result();
    $rnum = $stmt->num_rows;
    $stmt->close();

    if ($rnum==0) {
        if ($pass1==$pass2) {
            $stmt = $conn->prepare($INSERT);
            $stmt->bind_param("sss", $id, $name , $place);
            $stmt->execute();

            $pass1=$pass2=md5($pass1);
            $stmt = $conn->prepare($INSERT1);
            $stmt->bind_param("sss", $id, $pass1 , $pass2);
            $stmt->execute();
            $m = "User added";
            $l = "../html/fregister.html";
            $t = "success";
            pop($l,$m,$t);
        }
        else{
            $m = "Password mismatch";
            $l = "../html/newuser.html";
            $t = "error";
            pop($l,$m,$t);
        }
    }
    else{
        $m = "User exists already";
        $l = "../html/cindex.html";
        $t = "error";
        pop($l,$m,$t);
    }
}

function pop ($l,$m,$t){
    echo '<script src="../js/jquery-3.6.0.min.js"></script>';
    echo '<script src="../js/sweetalert2.all.min.js"></script>';
    echo '<script type="text/javascript">';
    echo "setTimeout(function () { Swal.fire('','$m','$t').then(function (result) {if (result.value) {window.location = '$l';}})";
    echo '},100);</script>';
}

?>