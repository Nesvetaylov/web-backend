<html>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="formStyle.css">
  <title>task 4</title>
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
    <form action="index.php" method="POST" id="svyaz">
      <h3> Для обратной связи оставьте свои данные: </h3>
      <label>
          <strong> Фамилия имя отчество:</strong>
          <br>
          <input name="fio" type="text"
          <?php if ($errors['fio']) {print 'class="error"';} ?> value="<?php print $values['fio']; ?>" placeholder="ФИО" />
      </label>
      <br>
      <label>
          <strong>Номер телефона: </strong>
          <br>
          <input name="phone" type="tel" pattern="\+7\-[0-9]{3}\-[0-9]{3}\-[0-9]{2}\-[0-9]{2}"
          <?php if ($errors['phone']) {print 'class="error"';} ?> value="<?php print $values['phone']; ?>" placeholder="+7(___)___-__-__" />
      </label>
      <br>
      <label>
          <strong> Введите вашу почту:</strong>
          <br>
          <input name="email" type="email"
          <?php if ($errors['email']) {print 'class="error"';} ?> value="<?php print $values['email']; ?>" placeholder="email" />
      </label>
      <br>
      <label>
          <strong> Дата рождения:</strong>
          <br>
          <input name="birthdate" value="" type="date"
          <?php if ($errors['birthdate']) {print 'class="error"';} ?> value="<?php print $values['birthdate']; ?>" />
      </label>
      <br>
      <strong> Пол:</strong>
      <label>
          <input type="radio" name="gender" required value="male">
          Мужской
      </label>
      <label>
          <input type="radio" name="gender" required value="female">
          Женский
      </label>
      <br>
      <label>
          <strong>Любимый язык программирования:</strong>
          <br />
          <select name="selections[]" multiple="multiple" width="400"
          <?php if ($errors['selections']) {print 'class="error"';} ?> value="<?php print $values['selections']; ?>">
              <option value="lua"> Lua</option>
              <option value="c"> C</option>
              <option value="c++"> C++</option>
              <option value="c#"> C#</option>
              <option value="php"> PHP</option>
              <option value="python"> Python</option>
              <option value="java"> Java</option>
              <option value="js"> JavaScript</option>
              <option value="ruby"> Ruby</option>
              <option value="go"> Go</option>
          </select>
      </label>
      <br>
      <label>
          <strong> Биография:</strong>
          <br>
          <textarea name="biography" placeholder="Я был писателем, пока не... " 
          <?php if ($errors['biography']) {print 'class="error"';} ?> value="<?php print $values['biography']; ?>"></textarea>
      </label>
      <br>
      <label>
          <input type="checkbox" name="check" 
          <?php if ($errors['checkbox']) {print 'class="error"';} ?> value="<?php print $values['checkbox']; ?>"/>
          c контрактом ознакомлен(а)
      </label>
      <br>
      <input type="submit" value="Сохранить" />
  </form>
  </body>
</html>
