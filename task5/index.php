<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $isStarted = session_start();

  $messages = array();   // Массив для временного хранения сообщений пользователю.
  // Выдаем сообщения о различных ошибках
  if (!empty($_COOKIE['DBERROR'])) {
    $messages[] = $_COOKIE['DBERROR'] . '<br><br>';
    setcookie('DBERROR', '', time() - 3600);
  }
  if (!empty($_COOKIE['AUTHERROR'])) {
    $messages[] = $_COOKIE['AUTHERROR'] . '<br><br>';
    setcookie('AUTHERROR', '', time() - 3600);
  }
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    $messages[] = 'Спасибо, результаты сохранены.<br>';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('Вы можете войти с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.<br>',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass']));
    }
    setcookie('save', '', time() - 3600);
    setcookie('login', '', time() - 3600);
    setcookie('pass', '', time() - 3600);
  }

  // Складываем признак ошибок в массив.
  $hasErrors = false;
  $errors = array();
  $errors['fio'] = !empty($_COOKIE['fio_error']);
  $errors['phone'] = !empty($_COOKIE['phone_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['birthdate'] = !empty($_COOKIE['birthdate_error']);
  $errors['gender'] = !empty($_COOKIE['gender_error']);
  $errors['selections'] = !empty($_COOKIE['selections_error']);
  $errors['biography'] = !empty($_COOKIE['biography_error']);
  $errors['check'] = !empty($_COOKIE['check_error']);

  // Выдаем сообщения об ошибках.
  if ($errors['fio']) {
    setcookie('fio_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('fio_value', '', 100000);
    $messages[] = '<div class="error">Заполните имя.</div>';
    $hasErrors = true;
  }
  if ($errors['phone']) {
    setcookie('phone_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('phone_value', '', 100000); 
    $messages[] = '<div class="error">Введите номер телефона.</div>';
    $hasErrors = true;
  }
  if ($errors['email']) {
    setcookie('email_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('email_value', '', 100000);
    $messages[] = '<div class="error">Заполните email.</div>';
    $hasErrors = true;
  }
  if ($errors['birthdate']) {
    setcookie('birthdate_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('birthdate_value', '', 100000);
    $messages[] = '<div class="error">Заполните дату рождения.</div>';
    $hasErrors = true;
  }
  if ($errors['gender']) {
    setcookie('gender_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('gender_value', '', 100000);
    $messages[] = '<div class="error">Выберете пол.</div>';
    $hasErrors = true;
  }
  if ($errors['selections']) {
    setcookie('selections_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('selections_value', '', 100000);
    $messages[] = '<div class="error">Выберете интересующие вас языки программирования.</div>';
    $hasErrors = true;
  }
  if ($errors['biography']) {
    setcookie('biography_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('biography_value', '', 100000);
    $messages[] = '<div class="error">Заполните биографию.</div>';
    $hasErrors = true;
  }
  if ($errors['check']) {
    setcookie('check_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('check_value', '', 100000);
    $messages[] = '<div class="error">Укажите согласие на обработку и хранение персональных данных.</div>';
    $hasErrors = true;
  }

  $values = array(); // Складываем предыдущие значения полей в массив, если есть.
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
  $values['phone'] = empty($_COOKIE['phone_value']) ? '' : $_COOKIE['phone_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['birthdate'] = empty($_COOKIE['birthdate_value']) ? '' : $_COOKIE['birthdate_value'];
  $values['gender'] = empty($_COOKIE['gender_value']) ? '' : $_COOKIE['gender_value'];
  $values['selections'] = empty($_COOKIE['selections_value']) ? array() : unserialize($_COOKIE['selections_value']);
  $values['biography'] = empty($_COOKIE['biography_value']) ? '' : $_COOKIE['biography_value'];
  $values['check'] = empty($_COOKIE['check_value']) ? '' : $_COOKIE['check_value'];

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (!$hasErrors && $isStarted && !empty($_COOKIE[session_name()]) && !empty($_SESSION['hasLogged']) && $_SESSION['hasLogged']) {
    // TODO: загрузить данные пользователя из БД
    // и заполнить переменную $values,
    // предварительно санитизовав.
    include('../SecretData.php');
    $servername = "localhost";
    $dbUsername = user;
    $dbPassword = pass;
    $dbname = user;
    // $db = new PDO("mysql:host=localhost;dbname=$dbname", $dbUsername, $dbPassword,
    // [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    // try {      
    //   $select = "SELECT * FROM Forms WHERE login = ?";
    //   $result = $db->prepare($select);
    //   $result->execute([$_SESSION['login']]);
    //   $row = $result->fetch();
    //   $values['fio'] = $row['fio'];
    //   $values['phone'] = $row['phone'];
    //   $values['email'] = $row['email'];
    //   $values['birthdate'] = $row['birthdate'];
    //   $values['gender'] = $row['gender'];
    //   $values['biography'] = $row['biography'];

    //   // достать выбранные языки
    // }
    // catch(PDOException $e){
    //   $messages[] = 'Ошибка при загрузке формы из базы данных:<br>' . $e->getMessage();
    // }

    $messages[] = "Выполнен вход с логином: <strong>" . $_SESSION['login'] . '</strong><br>';
    // вывод ссылки для выхода
    $messages[] = '<a href="login.php?exit=1">Выход</a>';
  }
  elseif(!$hasErrors && $isStarted && !empty($_COOKIE[session_name()]) && !empty($_SESSION['hasLogged']) && !$_SESSION['hasLogged']) {
    // если не вошел, то вывести ссылку для входа
    $messages[] = '<a href="login.php">Войти</a> для изменения данных ранее отправленных форм<br>.';
  }

  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
elseif ($_SERVER["REQUEST_METHOD"] == "POST")
{
  $errors = FALSE; // Проверяем ошибки.

  // fio
  if (empty($_POST['fio'])) {
    setcookie('fio_error', '1', time() + 24 * 60 * 60); // Выдаем куку на день с флажком об ошибке в поле fio.
    $errors = TRUE;
  }
  else {
    setcookie('fio_value', $_POST['fio'], time() + 30 * 24 * 60 * 60); // Сохраняем ранее введенное в форму значение на месяц.
  }
  // phone
  if (empty($_POST['phone']) || !preg_match('/^[0-9+]+$/', $_POST['phone'])) {
    setcookie('phone_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('phone_value', $_POST['phone'], time() + 30 * 24 * 60 * 60);
  }
  // email
  if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
  }
  // birthdate
  if (empty($_POST['birthdate'])) {
    setcookie('birthdate_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('birthdate_value', $_POST['birthdate'], time() + 30 * 24 * 60 * 60);
  }
  // gender
  $genderCheck = $_POST['gender'] == "male" || $_POST['gender'] == "female";
  if (empty($_POST['gender']) || !$genderCheck) {
    setcookie('gender_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('gender_value', $_POST['gender'], time() + 30 * 24 * 60 * 60);
  }
  // biography
  if (empty($_POST['biography'])) {
    setcookie('biography_error', '1', time() + 24 * 60 * 60); 
    $errors = TRUE;
  }
  else {
    setcookie('biography_value', $_POST['biography'], time() + 30 * 24 * 60 * 60);
  }
  // selections
  if (empty($_POST['selections'])) {
    setcookie('selections_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('selections_value', serialize($_POST['selections']), time() + 30 * 24 * 60 * 60);
  }
  // check
  if (!isset($_POST['check'])) {
    setcookie('check_error', '1', time() + 24 * 60 * 60); 
    $errors = TRUE;
  }
  else {
    setcookie('check_value', $_POST['check'], time() + 30 * 24 * 60 * 60);
  }

  if ($errors) {
    header('Location: index.php'); // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    exit();
  }
  else {
    setcookie('fio_error', '', 100000); // Удаляем Cookies с признаками ошибок.
    setcookie('phone_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('birthdate_error', '', 100000);
    setcookie('gender_error', '', 100000);
    setcookie('selections_error', '', 100000);
    setcookie('biography_error', '', 100000);
    setcookie('check_error', '', 100000);
  }

  $isStarted = session_start();
  include('../SecretData.php');
  $servername = "localhost";
  $dbUsername = user;
  $dbPassword = pass;
  $dbname = user;
  $db = new PDO("mysql:host=localhost;dbname=$dbname", $dbUsername, $dbPassword,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if ($isStarted && !empty($_COOKIE[session_name()]) && !empty($_SESSION['hasLogged'])) {
    // TODO: перезаписать данные в БД новыми данными,
    // кроме логина и пароля.
  }
  else {
    // Генерируем уникальный логин и пароль.
    $login = substr(uniqid(), 3);
    $pass = rand(100000, 999999);
    // Сохраняем в Cookies.
    setcookie('login', $login);
    setcookie('pass', $pass);
    $_SESSION['login'] = $login;
    $_SESSION['pass'] = $pass;
    $_SESSION['hasLogged'] = false;
    
    try {      
      $newUser = "INSERT INTO Logins (login, password) VALUES (?, ?)";
      $request = $db->prepare($newUser);
      $request->execute([$login, md5($pass)]); // сохранил логин и хеш пароля

      $newForm = "INSERT INTO Forms (login, fio, phone, email, birthdate, gender, biography) VALUES (?, ?, ?, ?, ?, ?, ?)";
      $formReq = $db->prepare($newForm);
      $formReq->execute([$login, $_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['birthdate'], $_POST['gender'], $_POST['biography']]);
      $userId = $db->lastInsertId();
  
      $lang = "SELECT id_lang FROM ProgLangs WHERE id_lang = ?";
      $feed = "INSERT INTO LangsInForm (id, id_lang) VALUES (?, ?)";
      $langPrep = $db->prepare($lang);
      $feedPrep = $db->prepare($feed);
      foreach ($_POST['selections'] as $selection){
        $langPrep->execute([$selection]);
        $langId = $langPrep->fetchColumn();
        $feedPrep->execute([$userId, $langId]);
      }
    }
    catch(PDOException $e){
      setcookie('DBERROR', 'Error : ' . $e->getMessage());
      exit();
    }
  }

  setcookie('save', '1'); // Сохраняем куку с признаком успешного сохранения.

  header('Location: index.php'); // Делаем перенаправление.
}
