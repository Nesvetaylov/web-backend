<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    session_set_cookie_params(time() + 24 * 60 * 60);
    $isStarted = session_start();
    if($isStarted && !empty($_COOKIE[session_name()])) {
      session_regenerate_id();
    }
    $messages = array(); //массив сообщений для пользователя

  //вывод ошибок из куков
  if (!empty($_COOKIE['DBERROR'])) {
    $messages[] = $_COOKIE['DBERROR'] . '<br><br>';
    setcookie('DBERROR', '', time() - 3600);
  }
  if (!empty($_COOKIE['AUTHERROR'])) {
    $messages[] = $_COOKIE['AUTHERROR'] . '<br><br>';
    setcookie('AUTHERROR', '', time() - 3600);
  }
  if (!empty($_COOKIE['save'])) {
    setcookie('save', '', 100000);
    $messages[] = 'Спасибо, результаты сохранены.';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['password'])) {
      $messages[] = sprintf(
        'Вы можете войти с логином <strong>%s</strong> паролем <strong>%s</strong> для повторного входа.<br>',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['password'])
      );
    }
    setcookie('save', '', time() - 3600);
    setcookie('login', '', time() - 3600);
    setcookie('password', '', time() - 3600);
  }

  //если куки пустые
  $hasErrors = false;
  $errors = array();
  $errors['fio'] = !empty($_COOKIE['fio_error']);
  $errors['phone'] = !empty($_COOKIE['phone_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['birthdate'] = !empty($_COOKIE['birthdate_error']);
  $errors['gender'] = !empty($_COOKIE['gender_error']);
  $errors['langg'] = !empty($_COOKIE['langg_error']);
  $errors['biografy'] = !empty($_COOKIE['biografy_error']);
  $errors['V'] = !empty($_COOKIE['V_error']);

  if ($errors['fio']) {
    setcookie('fio_error', '', 100000);
    setcookie('fio_value', '', 100000);
    $messages[] = '<div class="error">Заполните имя.</div>';
    $hasErrors = true;
  }
  if ($errors['phone']) {
    setcookie('phone_error', '', 100000);
    setcookie('phone_value', '', 100000);
    $messages[] = '<div class="error">Заполните телефон.</div>';
    $hasErrors = true;
  }
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    setcookie('email_value', '', 100000);
    $messages[] = '<div class="error">Заполните почту.</div>';
    $hasErrors = true;
  }
  if ($errors['birthdate']) {
    setcookie('birthdate_error', '', 100000);
    setcookie('birthdate_value', '', 100000);
    $messages[] = '<div class="error">Заполните дату рождения.</div>';
    $hasErrors = true;
  }
  if ($errors['gender']) {
    setcookie('gender_error', '', 100000);
    setcookie('gender_value', '', 100000);
    $messages[] = '<div class="error">Выберите пол.</div>';
    $hasErrors = true;
  }
  if ($errors['langg']) {
    setcookie('langg_error', '', 100000);
    setcookie('langg_value', '', 100000);
    $messages[] = '<div class="error">Что-то не так с языком программирования!.</div>';
    $hasErrors = true;
  }
  if ($errors['biografy']) {
    setcookie('biografy_error', '', 100000);
    setcookie('biografy_value', '', 100000);
    $messages[] = '<div class="error">Заполните биографию.</div>';
    $hasErrors = true;
  }
  if ($errors['V']) {
    setcookie('V_error', '', 100000);
    setcookie('V_value', '', 100000);
    $messages[] = '<div class="error">Подтвердите согласие.</div>';
    $hasErrors = true;
  }


  $values = array(); // если куки не пустые то массив заполняется данными из куки, иначе ''
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
  $values['phone'] = empty($_COOKIE['phone_value']) ? '' : $_COOKIE['phone_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['birthdate'] = empty($_COOKIE['birthdate_value']) ? '' : $_COOKIE['birthdate_value'];
  $values['gender'] = empty($_COOKIE['gender_value']) ? '' : $_COOKIE['gender_value'];
  $values['selections'] = empty($_COOKIE['selections_value']) ? array() : unserialize($_COOKIE['selections_value']);
  $values['langg'] = empty($_COOKIE['langg_value']) ? array() : unserialize($_COOKIE['langg_value']);
  $values['biografy'] = empty($_COOKIE['biografy_value']) ? '' : $_COOKIE['biografy_value'];
  $values['V'] = empty($_COOKIE['V_value']) ? '' : $_COOKIE['V_value'];


  if ($isStarted && !empty($_COOKIE[session_name()]) && !empty($_SESSION['hasLogged']) && $_SESSION['hasLogged']) {
    include ('../Secret.php');
    $username = username;
    $password = password;
    $db = new PDO("mysql:host=localhost;dbname=$username",$username,$password,[PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    try {
      $select = "SELECT * FROM LogPerson WHERE login = ?"; //текстр запроса
      $result = $db->prepare($select); //подготовка запроса 
      $result->execute([$_SESSION['login']]); //подстановка значения в ?
      $row = $result->fetch(); //из результата запроса выбирает 1 строку и сохран в row 
      // выписывает из строки значения в values
      $formID = $row['id'];
      $values['fio'] = $row['fio'];
      $values['phone'] = $row['phone'];
      $values['email'] = $row['email'];
      $values['birthdate'] = $row['birthdate'];
      $values['gender'] = $row['gender'];
      $values['biografy'] = $row['biografy'];
      $select = "SELECT id_l FROM person_and_lang WHERE id_u = ?";
      $result = $db->prepare($select);
      $result->execute([$formID]);
      $list = array();
      while ($row = $result->fetch()) {
        $list[] = $row['id_l'];
      }
      $values['langg'] = $list;
    } catch (PDOException $e) {
      $messages[] = 'Ошибка при загрузке формы из базы данных:<br>' . $e->getMessage();
    }
    $messages[] = "Выполнен вход с логином: <strong>" . $_SESSION['login'] . '</strong><br>';
    $messages[] = '<a href="login.php?exit=1">Выход из аккаунта</a>'; // вывод ссылки для выхода
  }
  // если не вошел, то вывести ссылку для входа
  elseif ($isStarted && !empty($_COOKIE[session_name()])) {
    $messages[] = '<a href="login.php">Войти в аккаунт</a><br>.';
  }

  include ('form.php');

}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
elseif ($_SERVER["REQUEST_METHOD"] == "POST")
{
  $errors = FALSE; // Проверяем ошибки.

  // fio
  if (empty($_POST['fio'])) {
    setcookie('fio_error', '1', time() + 24 * 60 * 60); // Выдаем куку на день с флажком об ошибке в поле fio.
    $errors = TRUE;
  } else {
    setcookie('fio_value', htmlspecialchars($_POST['fio']), time() + 30 * 24 * 60 * 60); // Сохраняем ранее введенное в форму значение на месяц с экранированием HTML-символов.
  }
  // phone
  if (empty($_POST['phone']) || !preg_match('/^(\+\d{11})$/', $_POST['phone'])) {
    setcookie('phone_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('phone_value', htmlspecialchars($_POST['phone']), time() + 30 * 24 * 60 * 60); // Сохраняем значение с экранированием HTML-символов.
  }
  // eemail
  if (empty($_POST['eemail']) || !filter_var($_POST['eemail'], FILTER_VALIDATE_Eemail)) {
    setcookie('eemail_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('eemail_value', htmlspecialchars($_POST['eemail']), time() + 30 * 24 * 60 * 60); // Сохраняем значение с экранированием HTML-символов.
  }
  // birthdate
  if (empty($_POST['birthdate']) || !preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $_POST['birthdate'])) {
    setcookie('birthdate_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('birthdate_value', htmlspecialchars($_POST['birthdate']), time() + 30 * 24 * 60 * 60); // Сохраняем значение с экранированием HTML-символов.
  }
  // gender
  $genderCheck = $_POST['gender'] == "male" || $_POST['gender'] == "female";
  if (empty($_POST['gender']) || !$genderCheck) {
    setcookie('gender_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('gender_value', htmlspecialchars($_POST['gender']), time() + 30 * 24 * 60 * 60); // Сохраняем значение с экранированием HTML-символов.
  }
  // biografyraphy
  if (empty($_POST['biografyraphy'])) {
    setcookie('biografyraphy_error', '1', time() + 24 * 60 * 60); 
    $errors = TRUE;
  } else {
    setcookie('biografyraphy_value', htmlspecialchars($_POST['biografyraphy']), time() + 30 * 24 * 60 * 60); // Сохраняем значение с экранированием HTML-символов.
  }
  if (empty($_POST['langg'])) {
    setcookie('langg_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    $sth = $db->prepare("SELECT id FROM Lang");
    $sth->execute();
    $langs = $sth->fetchAll();
    $has_incorrect_lang = false;
    foreach ($_POST['langg'] as $lang) {
      $flag = true;
      foreach ($langs as $index)
        if ($index[0] == $lang) {
          $flag = false;
          break;
        }
      if ($flag == true) {
        $has_incorrect_lang = true;
        $errors = true;
        break;
      }
    }
    if (!$has_incorrect_lang) {
      setcookie('langg_value', serialize($_POST['langg']), time() + 30 * 24 * 60 * 60);
    }
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
    setcookie('eemail_error', '', 100000);
    setcookie('birthdate_error', '', 100000);
    setcookie('gender_error', '', 100000);
    setcookie('selections_error', '', 100000);
    setcookie('biografyraphy_error', '', 100000);
    setcookie('check_error', '', 100000);
  }

  if ($isStarted && !empty($_COOKIE[session_name()]) && !empty($_SESSION['hasLogged']) && $_SESSION['hasLogged']) {
    include ('../Secret.php');
    $username = username;
    $password = password;
    $db = new PDO("mysql:host=localhost;dbname=$username",$username,$password,[PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  $isStarted = session_start();
  if ($isStarted && !empty($_COOKIE[session_name()]) && !empty($_SESSION['hasLogged'])) {
    // перезапись данных в бд
    try {
      // получаем форму для данного логина
      $login = $_SESSION['login'];
      $select = "SELECT f.id FROM LogPerson f, Logi l WHERE l.login = '$login' AND f.login = l.login";
      $result = $db->query($select);
      $row = $result->fetch();
      $formID = $row['id'];
      // изменение данных в форме
      $updateForm = "UPDATE LogPerson SET fio = ?, phone = ?, email = ?, birthdate = ?, gender = ?, biografy = ? WHERE id = '$formID'";
      $formReq = $db->prepare($updateForm);
      $formReq->execute([$_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['birthdate'], $_POST['gender'], $_POST['biografy']]);
      // удаляем прошлые языки
      $deleteLangs = "DELETE FROM person_and_lang WHERE id = '$formID'";
      $delReq = $db->query($deleteLangs);
      // заполняем заново языки
      $lang = "SELECT id FROM Lang WHERE id = ?";
      $feed = "INSERT INTO person_and_lang (id_u, id_l) VALUES (?, ?)";
      $langPrep = $db->prepare($lang);
      $feedPrep = $db->prepare($feed);
      foreach ($_POST['langg'] as $selection) {
        $langPrep->execute([$selection]);
        $langID = $langPrep->fetchColumn();
        $feedPrep->execute([$formID, $langID]);
      }
    } catch (PDOException $e) {
      setcookie('DBERROR', 'Error : ' . $e->getMessage());
      exit();
    }
  } else {
    // генерируем логин и пароль
    $login = substr(uniqid(), 3);
    $password = rand(1000000, 9999999);
    // сохраняем в куки
    setcookie('login', $login);
    setcookie('password', $password);
    $_SESSION['hasLogged'] = false;

    try {
      $newUser = "INSERT INTO Logi (login, password) VALUES (?, ?)";
      $request = $db->prepare($newUser);
      $request->execute([$login, md5($password)]); // сохранил логин и хеш пароля
      //добавляем данные формы нового пользователя  в бд
      $newForm = "INSERT INTO LogPerson (login, fio, phone, email, birthdate, gender, biografy) VALUES (?, ?, ?, ?, ?, ?, ?)";
      $formReq = $db->prepare($newForm);
      $formReq->execute([$login, $_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['birthdate'], $_POST['gender'], $_POST['biografy']]);
      $userID = $db->lastInsertId();
      //и заполняет языки
      $lang = "SELECT id FROM Lang WHERE id = ?";
      $feed = "INSERT INTO person_and_lang (id_u, id_l) VALUES (?, ?)";
      $langPrep = $db->prepare($lang);
      $feedPrep = $db->prepare($feed);
      foreach ($_POST['selections'] as $selection) {
        $langPrep->execute([$selection]);
        $langID = $langPrep->fetchColumn();
        $feedPrep->execute([$userID, $langID]);
      }
    } catch (PDOException $e) {
      setcookie('DBERROR', 'Error : ' . $e->getMessage());
      exit();
    }
  }

  setcookie('save', '1');//сохранили куку о сохранении
  header('Location: index.php'); //перезагрузка

}
?>