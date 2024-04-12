<?php
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $messages = array();   // Массив для временного хранения сообщений пользователю.

  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    setcookie('save', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    $messages[] = 'Спасибо, результаты сохранены.'; // Если есть параметр save, то выводим сообщение пользователю.
  }

  // Складываем признак ошибок в массив.
  $errors = array();
  $errors['fio'] = !empty($_COOKIE['fio_error']);
  $errors['phone'] = !empty($_COOKIE['phone_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['birthdate'] = !empty($_COOKIE['birthdate_error']);
  $errors['selections'] = !empty($_COOKIE['selections_error']);
  $errors['biography'] = !empty($_COOKIE['biography_error']);
  $errors['checkbox'] = !empty($_COOKIE['checkbox_error']);

  // Выдаем сообщения об ошибках.
  if ($errors['fio']) {
    setcookie('fio_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('fio_value', '', 100000);
    $messages[] = '<div class="error">Заполните имя.</div>';
  }
  if ($errors['phone']) {
    setcookie('phone_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('phone_value', '', 100000); 
    $messages[] = '<div class="error">Введите номер телефона.</div>';
  }
  if ($errors['email']) {
    setcookie('email_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('email_value', '', 100000);
    $messages[] = '<div class="error">Заполните email.</div>';
  }
  if ($errors['birthdate']) {
    setcookie('birthdate_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('birthdate_value', '', 100000);
    $messages[] = '<div class="error">Заполните дату рождения.</div>';
  }
  if ($errors['selections']) {
    setcookie('selections_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('selections_value', '', 100000);
    $messages[] = '<div class="error">Выберете интересующие вас языки программирования.</div>';
  }
  if ($errors['biography']) {
    setcookie('biography_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('biography_value', '', 100000);
    $messages[] = '<div class="error">Заполните биографию.</div>';
  }
  if ($errors['checkbox']) {
    setcookie('checkbox_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('checkbox_value', '', 100000);
    $messages[] = '<div class="error">Укажите согласие на обработку и хранение персональных данных.</div>';
  }

  $values = array(); // Складываем предыдущие значения полей в массив, если есть.
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
  $values['phone'] = empty($_COOKIE['phone_value']) ? '' : $_COOKIE['phone_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['birthdate'] = empty($_COOKIE['birthdate_value']) ? '' : $_COOKIE['birthdate_value'];
  $values['selections'] = empty($_COOKIE['selections_value']) ? '' : $_COOKIE['selections_value'];
  $values['biography'] = empty($_COOKIE['biography_value']) ? '' : $_COOKIE['biography_value'];
  $values['checkbox'] = empty($_COOKIE['checkbox_value']) ? '' : $_COOKIE['checkbox_value'];

  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else 
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
  if (empty($_POST['phone'])) {
    setcookie('phone_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('phone_value', $_POST['phone'], time() + 30 * 24 * 60 * 60);
  }
  // email
  if (empty($_POST['email'])) {
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
  // biography
  if (empty($_POST['biography'])) {
    setcookie('biography_error', '1', time() + 24 * 60 * 60); 
    $errors = TRUE;
  }
  else {
    setcookie('biography_value', $_POST['biography'], time() + 30 * 24 * 60 * 60);
  }
  // checkbox
  if (empty($_POST['checkbox'])) {
    setcookie('checkbox_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('checkbox_value', $_POST['checkbox'], time() + 30 * 24 * 60 * 60);
  }
  // selections
  if (empty($_POST['checkbox'])) {
    setcookie('checkbox_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('checkbox_value', $_POST['checkbox'], time() + 30 * 24 * 60 * 60);
  }
  // TODO: selection, radio

  if ($errors) {
    header('Location: index.php'); // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    exit();
  }
  else {
    setcookie('fio_error', '', 100000); // Удаляем Cookies с признаками ошибок.
    setcookie('phone_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('birthdate_error', '', 100000);
    setcookie('selections_error', '', 100000);
    setcookie('biography_error', '', 100000);
    setcookie('checkbox_error', '', 100000);
  }

  // Сохранение в БД.
  // ...

  setcookie('save', '1'); // Сохраняем куку с признаком успешного сохранения.

  header('Location: index.php'); // Делаем перенаправление.
}
