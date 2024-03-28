<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {
        print('Спасибо, результаты сохранены.');
    }
    include('form.php');
    exit();
}

$errors = false;
$fio = $_POST['name'];
$phone = $_POST['phone'];
$mail = $_POST['email'];
$year = $_POST['year'];
$month = $_POST['month'];
$day = $_POST['day'];
$pol = $_POST['pol'];
$langg = $_POST['select-field'];
$biog = $_POST['biography'];
$V = isset($_POST['check']);

if (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s-]{1,150}$/u', $fio)) {
    print('Заполните имя.<br/>');
    $errors = true;
}

if (!preg_match('/^\+[0-9]{11}$/', $phone)) {
    print('Заполните телефон.<br/>');
    $errors = true;
}

if (!preg_match('/^([a-z0-9_-]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i', $mail)) {
    print('Заполните почту.<br/>');
    $errors = true;
}

if (!is_numeric($year) || !preg_match('/^\d+$/', $year)) {
    print('Заполните год.<br/>');
    $errors = true;
}

if (!is_numeric($month) || $month < 0 || $month > 12) {
    print('Заполните месяц.<br/>');
    $errors = true;
}

if (!is_numeric($day) || $day < 0 || $day > 31) {
    print('Заполните день.<br/>');
    $errors = true;
}

if (!$pol) {
    print('Выберите пол.<br/>');
    $errors = true;
}

$user = 'u67286';
$pass = '9883045';
$db = new PDO(
    'mysql:host=localhost;dbname=u67286',
    $user,
    $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sth = $db->prepare("SELECT id FROM Lang");
$sth->execute();
$langs = $sth->fetchAll();

if (!$langg) {
    print('Выберите язык программирования.<br/>');
    $errors = true;
} else {
    foreach ($langg as $lang) {
        $flag = true;
        foreach ($langs as $index) {
            if ($index[0] == $lang) {
                $flag = false;
                break;
            }
        }
        if ($flag) {
            print('Error: no valid language.<br/>');
            $errors = true;
            break;
        }
    }
}

if (!$biog) {
    print('Заполните биографию.<br/>');
    $errors = true;
}

if (!$V) {
    print('Подвердите согласие.<br/>');
    $errors = true;
}

if ($errors) {
    exit();
}

$stmt = $db->prepare("INSERT INTO Person (fio, phone, mail, birthday, pol, biog, V) VALUES (:fio, :phone, :mail, :birthday, :pol, :biog, :V)");
$birthday = $day . '.' . $month . '.' . $year;
$stmt->bindParam(':birthday', $birthday);
$stmt->bindParam(':name', $fio);
$stmt->bindParam(':phone', $phone);
$stmt->bindParam(':email', $mail);
$stmt->bindParam(':pol', $pol);
$stmt->bindParam(':biography', $biog);
$stmt->bindParam(':check', $V);
$stmt->execute();
$id = $db->lastInsertId();

foreach ($langg as $lang) {
    $stmt = $db->prepare("INSERT INTO person_and_lang (id, id_lang) VALUES (:id,:id_lang)");
    $stmt->bindParam(':id', $id_u);
    $stmt->bindParam(':id_lang', $lang);
    $id_u = $id;
    $stmt->execute();
}

header('Location: ?save=1');
