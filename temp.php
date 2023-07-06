
<?php
session_name("YE_UPDATE");
session_start();
require_once(__DIR__ . "/html.php");
//$users = json_decode(file_get_contents(BOT_MIDRASH_INCLUDE_DIR . "/data/users.json"), true);
//$options = json_decode(file_get_contents(BOT_MIDRASH_INCLUDE_DIR . "/data/permissions.json"), true);

if(isset($_GET['logout'])){
    session_unset();
    session_destroy();
    header('refrash: 0');
}

if(isset($_SESSION['YE_UPDATE_User']['Status'])&& 
$_SESSION['YE_UPDATE_User']['Status']==='In'){
  if(!isset($_GET['act']) || !is_string($_GET['act'])){
    printHome();
  }
  //
  elseif($_GET['act'] == "update-prays"){
    admin__updatePrays();
  }
  elseif($_GET['act'] == "update-books"){
      admin__updateBooks();
  }
  elseif($_GET['act'] == "shabat-menu"){
      admin__shabatMenu();
  }
  elseif($_GET['act'] == "block-users"){
      admin__blockAndFreeUsers();
  }
  elseif($_GET['act'] == "send-one-message"){
      admin__sendMessageToUser();
  }
  elseif($_GET['act'] == "update-contacts"){
      admin__updateContacts();
  }
  elseif($_GET['act'] == "shifts-table"){
      admin__updateShiftsTable();
  }
  elseif($_GET['act'] == "guards-table"){
      admin__updateGuardsTable();
  }
  elseif($_GET['act'] == "send-message-to-all-users"){
      admin__sendMessageToAllUsers();
  }
  elseif($_GET['act'] == "send-special-reminde"){
      admin__sendSpecialRegister();
  }
  elseif($_GET['act'] == "chats-log-messages"){
      admin__printLastChatsHtml();
  }
  elseif($_GET['act'] == "user-messages-log"){
      admin__printLogMessagesUserHtml();
  }
  elseif($_GET['act'] == "show-json"){
      admin__printJson();
  }
  elseif($_GET['act'] == "upload-image"){
      admin__uploadPhotos();
  }
  elseif($_GET['act'] == "view-images"){
      admin__printImagesTable();
  }
  elseif($_GET['act'] == "delete-image"){
      admin__deletePhoto();
  }
  elseif($_GET['act'] == "manage-users"){
      admin__manageUsers();
  }
  elseif($_GET['act'] == "manage-users-permissions"){
      admin__manageUsersPermissions();
  }
  else{
      http_response_code(404);
      $title = "הפעולה לא נמצאה";
      $message = '<h2 style="color:red;">הפעולה לא נמצאה</h2>';
      //printHeadWithDiv($title, $message);
      
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
$myfile = fopen("C:\\Users\\neria\\Documents\\GitHub\\Bot-Midrash\\index.html", "r") or die("Unable to open file!");
echo fread($myfile,filesize("C:\\Users\\neria\\Documents\\GitHub\\Bot-Midrash\\index.html"));
fclose($myfile);
if (isset($_SESSION['YE_UPDATE_Mes'])){
  echo $_SESSION['YE_UPDATE_Mes'];
  $_SESSION['YE_UPDATE_Mes']='';
}
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