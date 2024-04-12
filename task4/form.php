<html>
  <head>
    <style>
/* Сообщения об ошибках и поля с ошибками выводим с красным бордюром. */
.error {
  border: 2px solid red;
}
    </style>
  </head>
  <body>

<?php
if (!empty($messages)) {
  print('<div id="messages">');
  // Выводим все сообщения.
  foreach ($messages as $message) {
    print($message);
  }
  print('</div>');
}
?>

    <form action="" method="POST">
      <label>
        <strong> Фамилия имя отчество:</strong>
        <br>
        <input name="fio" type="text" <?php if ($errors['fio']) {print 'class="error"';} ?> value="<?php print $values['fio']; ?>" placeholder="ФИО" />
      </label>
      <label>
        <strong> Введите вашу почту:</strong>
        <br>
        <input name="email" type="email" <?php if ($errors['email']) {print 'class="error"';} ?> value="<?php print $values['email']; ?>" placeholder="email" />
      </label>
      <input type="submit" value="ok" />
    </form>
  </body>
</html>
