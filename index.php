<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $messages = array();
  if (!empty($_COOKIE['save'])) {
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    $messages[] = 'Спасибо, результаты сохранены.';
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('Войдите <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> чтобы измененить данные.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass']));
    }
    setcookie('name_value', '', 100000);
    setcookie('email_value', '', 100000);
    setcookie('year_value', '', 100000);
    setcookie('pol_value', '', 100000);
    setcookie('limb_value', '', 100000);
    setcookie('bio_value', '', 100000);
    setcookie('power_value', '', 100000);
    setcookie('telepat_value', '', 100000);
    setcookie('noclip_value', '', 100000);
    setcookie('immortal_value', '', 100000);
    setcookie('check_value', '', 100000);
  }

  $errors = array();
  $error=FALSE;
  $errors['field-name'] = !empty($_COOKIE['name_error']);
  $errors['field-email'] = !empty($_COOKIE['email_error']);
  $errors['year'] = !empty($_COOKIE['year_error']);
  $errors['radio-pol'] = !empty($_COOKIE['pol_error']);
  $errors['radio-limb'] = !empty($_COOKIE['limb_error']);
  $errors['field-super'] = !empty($_COOKIE['super_error']);
  $errors['field-bio'] = !empty($_COOKIE['bio_error']);
  $errors['checkbox'] = !empty($_COOKIE['check_error']);
  if ($errors['field-name']) {
    setcookie('name_error', '', 100000);
    $messages[] = '<div class="error">Заполните имя или у него неверный формат (only English)</div>';
    $error=TRUE;
  }
  if ($errors['field-email']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div class="error">Заполните имейл или у него неверный формат</div>';
    $error=TRUE;
  }
  if ($errors['year']) {
    setcookie('year_error', '', 100000);
    $messages[] = '<div class="error">Выберите год.</div>';
    $error=TRUE;
  }
  if ($errors['radio-pol']) {
    setcookie('pol_error', '', 100000);
    $messages[] = '<div class="error">Выберите пол.</div>';
    $error=TRUE;
  }
  if ($errors['radio-limb']) {
    setcookie('limb_error', '', 100000);
    $messages[] = '<div class="error">Укажите кол-во конечностей.</div>';
    $error=TRUE;
  }
  if ($errors['field-super']) {
    setcookie('super_error', '', 100000);
    $messages[] = '<div class="error">Выберите суперспособности(хотя бы одну).</div>';
    $error=TRUE;
  }
  if ($errors['field-bio']) {
    setcookie('bio_error', '', 100000);
    $messages[] = '<div class="error">Заполните биографию или у неё неверный формат (only English)</div>';
    $error=TRUE;
  }
  if ($errors['checkbox']) {
    setcookie('check_error', '', 100000);
    $messages[] = '<div class="error">Вы должны болеть за Red Bull Racing.</div>';
    $error=TRUE;
  }
  $values = array();
  $values['field-name'] = empty($_COOKIE['name_value']) ? '' : strip_tags($_COOKIE['name_value']);
  $values['field-email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
  $values['year'] = empty($_COOKIE['year_value']) ? 0 : $_COOKIE['year_value'];
  $values['radio-pol'] = empty($_COOKIE['pol_value']) ? '' : $_COOKIE['pol_value'];
  $values['radio-limb'] = empty($_COOKIE['limb_value']) ? '' : $_COOKIE['limb_value'];
  $values['immortal'] = empty($_COOKIE['immortal_value']) ? 0 : $_COOKIE['immortal_value'];
  $values['noclip'] = empty($_COOKIE['noclip_value']) ? 0 : $_COOKIE['noclip_value'];
  $values['power'] = empty($_COOKIE['power_value']) ? 0 : $_COOKIE['power_value'];
  $values['telepat'] = empty($_COOKIE['telepat_value']) ? 0 : $_COOKIE['telepat_value'];
  $values['field-bio'] = empty($_COOKIE['bio_value']) ? '' : strip_tags($_COOKIE['bio_value']);
  $values['checkbox'] = empty($_COOKIE['check_value']) ? FALSE : $_COOKIE['check_value'];

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (empty($errors) && !empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
    // TODO: загрузить данные пользователя из БД
    // и заполнить переменную $values,
    // предварительно санитизовав.
    printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
  }

  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода 
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  // Проверяем ошибки.
  $errors = FALSE;
  if (empty($_POST['fio'])) {
    // Выдаем куку на день с флажком об ошибке в поле fio.
    setcookie('fio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('fio_value', $_POST['fio'], time() + 30 * 24 * 60 * 60);
  }

// *************
// TODO: тут необходимо проверить правильность заполнения всех остальных полей.
// Сохранить в Cookie признаки ошибок и значения полей.
// *************

  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    // Удаляем Cookies с признаками ошибок.
    setcookie('fio_error', '', 100000);
    // TODO: тут необходимо удалить остальные Cookies.
  }

  // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if (!empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
    // TODO: перезаписать данные в БД новыми данными,
    // кроме логина и пароля.
  }
  else {
    // Генерируем уникальный логин и пароль.
    // TODO: сделать механизм генерации, например функциями rand(), uniquid(), md5(), substr().
    $login = '123';
    $pass = '123';
    // Сохраняем в Cookies.
    setcookie('login', $login);
    setcookie('pass', $pass);

    // TODO: Сохранение данных формы, логина и хеш md5() пароля в базу данных.
    // ...
  }

  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: ./');
}
