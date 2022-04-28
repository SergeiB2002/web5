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
  if (!$error && !empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])) {
    $user = 'u41028';
    $pass = '2356452';
    $db = new PDO('mysql:host=localhost;dbname=u41028', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    try{
      $get=$db->prepare("SELECT * FROM application WHERE id=?");
      $get->bindParam(1,$_SESSION['uid']);
      $get->execute();
      $inf=$get->fetchALL();
      $values['field-name']=$inf[0]['name'];
      $values['field-email']=$inf[0]['email'];
      $values['year']=$inf[0]['year'];
      $values['radio-pol']=$inf[0]['pol'];
      $values['radio-limb']=$inf[0]['konech'];
      $values['field-bio']=$inf[0]['biogr'];

      $get2=$db->prepare("SELECT name FROM superp WHERE per_id=?");
      $get2->bindParam(1,$_SESSION['uid']);
      $get2->execute();
      $inf2=$get2->fetchALL();
      for($i=0;$i<count($inf2);$i++){
        if($inf2[$i]['field-super']=='power'){
          $values['power']=1;
        }
        if($inf2[$i]['field-super']=='telepat'){
          $values['telepat']=1;
        }
        if($inf2[$i]['field-super']=='immortal'){
          $values['immortal']=1;
        }
        if($inf2[$i]['field-super']=='noclip'){
          $values['noclip']=1;
        }
      }
    }
    catch(PDOException $e){
      print('Error: '.$e->getMessage());
      exit();
    }
    printf('Произведен вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
  }

  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода 
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  if(!empty($_POST['logout'])){
    session_destroy();
    header('Location: index.php');
  }
  else{
    $name = $_POST['field-name'];
    $email = $_POST['field-email'];
    $year = $_POST['year'];
    $pol=$_POST['radio-pol'];
    $limbs=$_POST['radio-limb'];
    $powers=$_POST['field-super'];
    $bio=$_POST['field-bio'];
    if(empty($_SESSION['login'])){
      $check=$_POST['check'];
    }
    //Регулярные выражения
    $bioregex = "/^\s*\w+[\w\s\.,-]*$/";
    $nameregex = "/^\w+[\w\s-]*$/";
    $mailregex = "/^[\w\.-]+@([\w-]+\.)+[\w-]{2,4}$/";
    $errors = FALSE;

    if (empty($name) || (!preg_match($nameregex,$name))) {
      setcookie('name_error', '1', time() + 24*60 * 60);
      setcookie('name_value', '', 100000);
      $errors = TRUE;
    }
    else {
      setcookie('name_value', $fio, time() + 60 * 60);
      setcookie('name_error','',100000);
    }

    if (empty($email) || !filter_var($email,FILTER_VALIDATE_EMAIL) ||
     (!preg_match($mailregex,$email))) {
      setcookie('email_error', '1', time() + 24*60 * 60);
      setcookie('email_value', '', 100000);
      $errors = TRUE;
    }
    else {
      setcookie('email_value', $email, time() + 60 * 60);
      setcookie('email_error','',100000);
    }

    if ($year=='Год') {
      setcookie('year_error', '1', time() + 24 * 60 * 60);
      setcookie('year_value', '', 100000);
      $errors = TRUE;
    }
    else {
      setcookie('year_value', intval($year), time() + 60 * 60);
      setcookie('year_error','',100000);
    }

    if (!isset($pol)) {
      setcookie('pol_error', '1', time() + 24 * 60 * 60);
      setcookie('pol_value', '', 100000);
      $errors = TRUE;
    }
    else {
      setcookie('pol_value', $pol, time() + 60 * 60);
      setcookie('pol_error','',100000);
    }

    if (!isset($limbs)) {
      setcookie('limb_error', '1', time() + 24 * 60 * 60);
      setcookie('limb_value', '', 100000);
      $errors = TRUE;
    }
    else {
      setcookie('limb_value', $limbs, time() + 60 * 60);
      setcookie('limb_error','',100000);
    }

    if (!isset($powers)) {
      setcookie('super_error', '1', time() + 24 * 60 * 60);
      setcookie('immortal_value', '', 100000);
      setcookie('noclip_value', '', 100000);
      setcookie('power_value', '', 100000);
      setcookie('telepat_value', '', 100000);
      $errors = TRUE;
    }
    else {
      $apw=array(
        "immortal_value"=>0,
        "noclip_value"=>0,
        "power_value"=>0,
        "telepat_value"=>0
      );
    foreach($powers as $pwer){
      if($pwer=='immortal'){setcookie('immortal_value', 1, time() + 12 * 30 * 24 * 60 * 60); $apw['immortal_value']=1;} 
      if($pwer=='noclip'){setcookie('noclip_value', 1, time() + 12*30 * 24 * 60 * 60);$apw['noclip_value']=1;} 
      if($pwer=='power'){setcookie('power_value', 1, time() + 12*30 * 24 * 60 * 60);$apw['power_value']=1;} 
      if($pwer=='telepat'){setcookie('telepat_value', 1, time() + 12*30 * 24 * 60 * 60);$apw['telepat_value']=1;}
      }
    foreach($apw as $c=>$val){
      if($val==0){
        setcookie($c,'',100000);
        }
      }
    }
    
    if ((empty($bio)) || (!preg_match($bioregex,$bio))) {
      setcookie('bio_error', '1', time() + 24 * 60 * 60);
      setcookie('bio_value', '', 100000);
      $errors = TRUE;
    }
    else {
      setcookie('bio_value', $bio, time() + 12 * 30 * 24 * 60 * 60);
      setcookie('bio_error', '', 100000);
    }
    
    if(empty($_SESSION['login'])){
      if(!isset($check)){
        setcookie('check_error','1',time()+ 24*60*60);
        setcookie('check_value', '', 100000);
        $errors=TRUE;
      }
      else{
        setcookie('check_value',TRUE,time()+ 60*60);
        setcookie('check_error','',100000);
      }
    }
    if ($errors) {
      setcookie('save','',100000);
      header('Location: login.php');
    }
    else {
      setcookie('name_error', '', 100000);
      setcookie('email_error', '', 100000);
      setcookie('year_error', '', 100000);
      setcookie('pol_error', '', 100000);
      setcookie('limb_error', '', 100000);
      setcookie('super_error', '', 100000);
      setcookie('bio_error', '', 100000);
      setcookie('check_error', '', 100000);
    }
