<?php

require_once "Yubico.php";

$CFG['__CLIENT_ID__'] = 22359;
$CFG['__CLIENT_KEY__'] = '/JmRDI7vqrDog/VtPcFdWujMHWw=';

$username = $_REQUEST["username"];
$password = $_REQUEST["password"];
$key = $_REQUEST["key"];

$passwordkey = $password . ':' . $key;

$yubi = new Auth_Yubico($CFG['__CLIENT_ID__'], $CFG['__CLIENT_KEY__']);
$ret = $yubi->parsePasswordOTP($passwordkey);

$identity = $ret['prefix'];
$key = $ret['otp'];
$auth = $yubi->verify($key);
$dbconn = mysqli_connect("localhost", "root", "", "demoserver");
if (!$dbconn) {
    die('Could not connect: ' . mysql_error());
    $authenticated = 2;
    return;
}
$query = "SELECT username FROM demoserver WHERE id='" . $identity . "'";
$result = mysqli_query($dbconn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $realname = $row;
    }
} else {
    $db_password['password'] = "";
    $ret['password'] = "";
    $realname['username'] = "";
    echo "0 results";
}

$query = "SELECT password FROM demoserver WHERE id='" . $identity . "'";
$result = mysqli_query($dbconn, $query);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $db_password = $row;
    }
} else {
    $db_password['password'] = "";
    $ret['password'] = "";
    $realname['username'] = "";
    echo "0 results";
}
if ($db_password['password'] == $ret['password'] && $username == $realname['username'] && $auth == 1) {
    $url='http://etg-iran.com';
    header( "Location: $url" );
} else {
    echo "Authentication failure please try again!";
}

echo "<br/>";
echo "<a href='index.php'>Go Back</a>";
?>