
<?php
session_name("YE_UPDATE");
session_start();
//$users = json_decode(file_get_contents(BOT_MIDRASH_INCLUDE_DIR . "/data/users.json"), true);
//$options = json_decode(file_get_contents(BOT_MIDRASH_INCLUDE_DIR . "/data/permissions.json"), true);

if(isset($_GET['logout'])){
  echo 'logout';
    session_unset();
    session_destroy();
    header('refrash: 0');
}

if(isset($_SESSION['YE_UPDATE_User']['Status'])&& 
$_SESSION['YE_UPDATE_User']['Status']==='In'){
  if(!isset($_GET['act']) || !is_string($_GET['act'])){
    printHome();
  }
  elseif($_GET['act'] == "33"){
    echo '3333';
}
}

else if (isset($_POST['code'])&& $_POST['code']===$_SESSION['YE_UPDATE_User']['Code']){
  $_SESSION['YE_UPDATE_User']['Status']='In';
  header('refresh: 0');

}

else if (array_key_exists('phone', $_POST)) {
  $phone=$_POST['phone'];
    if (checkPhone($phone)){
      $phone=intval($phone);
      $user='נריה';//db::query()
      //אימות
      $code='1111';//rand(1000,10000);
      $_SESSION['YE_UPDATE_User']['Code']=$code;
      //to do: send code to user
      $_SESSION['YE_UPDATE_User']['Status']='auth';
      $_SESSION['YE_UPDATE_User']['Logged']=$user;
      printAuth();
    }
}

else {
  if (isset($_SESSION['YE_UPDATE_User']['Status'])){
    echo $_SESSION['YE_UPDATE_User']['Status'];
  }
  //unset($_SESSION['YE_UPDATE_User']['Logged']);
    printLogin();
}

function printHome(){
$myfile = fopen("C:\\Users\\neria\\Documents\\GitHub\\Bot-Midrash\\include\\index.html", "r") or die("Unable to open file!");
echo fread($myfile,filesize("C:\\Users\\neria\\Documents\\GitHub\\Bot-Midrash\\include\\index.html"));
fclose($myfile);
}

function printLogin(){
  echo '<form method="POST">
          <input type="text" name="phone" placeholder="מספר טלפון" autofocus="autofocus"><br><br>
          <button type="submit">כניסה</button>
      </form>
  </div>';
}

function checkPhone($phone){
  return (!empty($phone)&& strlen($phone)===9 && intval($phone)===999999999);
}

function printAuth(){

  // else{
  echo $_SESSION['YE_UPDATE_User']['Code'];
  echo '<form method="POST">
    <input type="text" name="code" placeholder="קוד אימות" autofocus="autofocus"><br><br>
    <button type="submit">כניסה</button>
    </form>
    <a href="?logout" style="color:blue;">התנתק...</a>
    <!--a href="?back" style="color:blue;">חזור</a-->
    </div>';
  // }
}
?>