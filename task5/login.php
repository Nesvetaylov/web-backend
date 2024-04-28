<?php
// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
$session_started = false;
if ($_COOKIE[session_name()] && session_start()) {
  $session_started = true;
  if (!empty($_GET['exit'])){
    // выход (окончание сессии вызовом session_destroy() при нажатии на кнопку Выход).
    session_destroy();
    header('Location: index.php');
    exit();
  }
  if (!empty($_SESSION['hasLogged']) && $_SESSION['hasLogged'] = true) {
    // Если есть логин в сессии, то пользователь уже авторизован.
    // Делаем перенаправление на форму.
    header('Location: ./');
    exit();
  }
}
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>

<form action="" method="post">
  <input name="login" />
  <input name="pass" />
  <input type="submit" value="Войти" />
</form>

<?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
  include('../SecretData.php');
  $servername = "localhost";
  $dbUsername = user;
  $dbPassword = pass;
  $dbname = user;
  $db = new PDO("mysql:host=localhost;dbname=$dbname", $dbUsername, $dbPassword,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  // проверка наличия логина в базе данных
  $loginFlag = false;
  try {
    $select = "SELECT * FROM Logins";
    $result = $db->query($select);
    if (!$session_started) {
      session_start();
    }
    while($row = $result->fetch()){
      if ($_POST['login'] == $row['login'] && md5($_POST['pass']) == $row['password']) {
        $loginFlag = true;
        break;
      }
    }
  }
  catch(PDOException $e){
    setcookie('DBERROR', 'Error : ' . $e->getMessage());
    exit();
  }
  // Выдать сообщение об ошибках.
  
  // Если все ок, то авторизуем пользователя.
  if ($loginFlag) {
    $_SESSION['hasLogged'] = true;
  }
  else {
    $_SESSION['hasLogged'] = false;
    setcookie('AUTHERROR', 'Ошибка входа (Неверный логин или пароль)');
  }
  // Делаем перенаправление.
  header('Location: ./');
}
