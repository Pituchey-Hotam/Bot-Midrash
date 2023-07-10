<?php
define('MASTER_USER','master');
function admin__shabatMenu(){

}
function admin__blockAndFreeUsers(){
    if (isset($_POST["phone"])){
        $phone=$_POST["phone"];
        //select yashivaID from user where phone=&phone
        //$t=select * from users where yeshivaID=yeshivaID
        $t=[$phone];
        $users = json_decode(file_get_contents(__DIR__ . "/data/blockedUsers.json"), true);
        if (!empty($t)){
            if (in_array($phone,$users)){
                unset($users[array_search($phone,$users)]);
            file_put_contents(__DIR__ . "/data/blockedUsers.json", json_encode($users));
            $_SESSION["YE_UPDATE_Mes"]="המשתמש שוחרר בהצלחה";
            }
            else{
            array_push($users,$phone);
            file_put_contents(__DIR__ . "/data/blockedUsers.json", json_encode($users));
            $_SESSION["YE_UPDATE_Mes"]="המשתמש נחסם בהצלחה";
        }
        }
        else {
            $_SESSION["YE_UPDATE_Mes"]="המשתמש אינו קיים";
        }
        headerHome();
    }
    
    else{
    echo '<div align="center" dir="rtl">
        <!--a onclick="addButton()"><button>הוסף משתמש</button></a-->
        <br><br>
        <form method="POST">
            <div id="input-container" dir="rtl"></div>
            <input name="phone" type="number" placeholder="מספר טלפון">
            <br><br>
            <button type="submit">עדכן</button>
        </form>
        </div>
        
        <script>
        //const inputContainer = document.getElementById("input-container");
            
        //     // function addButton() { 
        //     //     var myParent = inputContainer;
        //     //     myParent.innerHTML+=`<input type="number" placeholder="מספר טלפון">
        //     //     <br><br>`;
        //     // }
        </script>'
        ;}
}
function admin__sendMessageToUser(){
    global $from;
    global $messageId;
    
    if(isset($_POST['send-message-one-user'], $_POST['phone']) && is_string($_POST['send-message-one-user']) && is_string($_POST['phone']) && !empty($_POST['send-message-one-user']) && !empty($_POST['phone'])){
        $adminPanelMesColor = "darkturquoise";

            $from = $_POST['phone'];
            $messageId = "adminPanel_send_one_message__" . uniqid();
            //sendMessage($_POST['phone'], "text", $_POST['send-message-one-user']);
            $adminPanelMes = "במידה והמשתמש פעיל בבוט, ההודעה נשלחה בהצלחה";
        
        if(isset($_GET['close-after-send'])){
            echo "<script>alert('" . $adminPanelMes . "');window.close();</script>";
        }
        elseif(isset($_GET['return-to-messages-log'])){
            header("Location: ?act=user-messages-log&phone=" . $from);
        }
        else{
            $_SESSION['YE_BM_UPDATE_Mes'] = "<h2 style='color:" . $adminPanelMesColor . ";'>" . $adminPanelMes . "</h2>";
            headerHome();
        }
    }
    else{
        $title = "שליחת הודעה למשתמש";
        
        $phone = "";
        $name = "";
        
        if(isset($_GET['phone']) && !empty(intval($_GET['phone'])) && strlen(intval($_GET['phone'])) == 12)
            $phone = htmlspecialchars($_GET['phone']);
            
        if(isset($_GET['name']) && !empty($_GET['name']) && mb_strlen($_GET['name']) > 4 && mb_strlen($_GET['name']) < 30)
            $name = htmlspecialchars($_GET['name']);
        
        echo '
        <div align=center>
        <form method="POST">
            מספר טלפון: 
            <input name="phone" dir="ltr" value="' . $phone . '" required/>
            <br><br>
            לשלוח כתבנית? <input type="checkbox" name="send-as-template" onclick="onCheckTemplate();"/>
            <br><br>
            <div id="template-name-div">
                לטובת התבנית, יש להזין את שם הנמען <input type="text" value="' . $name . '" name="send-as-template-name"/>
            </div>
            <br><br>
            <div id="content-div"></div>
            <script>
                function onCheckTemplate(){
                    var templateCheck = document.getElementsByName("send-as-template")[0];
                    var contentDiv = document.getElementById("content-div");
                    var templateName = document.getElementsByName("send-as-template-name")[0];
                    var templateNameDiv = document.getElementById("template-name-div");
                    if(templateCheck.checked){
                        contentDiv.innerHTML = \'תוכן התבנית: <input name="send-message-one-user" style="width: 500px;" required>\';
                        templateName.setAttribute("required", "required");
                        templateNameDiv.style.display = "block";
                    }
                    else{
                        contentDiv.innerHTML = \'תוכן ההודעה: <br><textarea rows="15" cols="35" name="send-message-one-user"></textarea>\';
                        templateName.removeAttribute("required");
                        templateNameDiv.style.display = "none";
                    }
                }
                onCheckTemplate();
            </script>
            <br><br>
            <button type="submit">שלח עכשיו</button>
        </form></div>';
    }
}
function admin__updateContacts(){

}
function admin__updateShiftsTable(){}
function admin__updateGuardsTable(){}
function admin__sendMessageToAllUsers(){}
function admin__sendSpecialRegister(){}
function admin__printLastChatsHtml(){}
function admin__printLogMessagesUserHtml(){}
function admin__printJson(){}
function admin__uploadPhotos(){}
function admin__printImagesTable(){}
function admin__deletePhoto(){}
function admin__manageUsers(){
    if (isset($_POST["phone"])){
        $phone1=$_POST["phone"];
        //$name=select name from users where phone=$phone
        $name='###';
        $phone=array($phone1=>$name);
        //select yashivaID from user where phone=&phone
        //$t=select * from users where yeshivaID=yeshivaID
        $t=[$phone];
        $users = json_decode(file_get_contents(__DIR__ . "/data/users.json"), true);
        if (!empty($t)){
            if (isset($users[$phone1])){
                $_SESSION["YE_UPDATE_Mes"]="המשתמש הינו מנהל, לא ניתן להסיר מנהלים בעלי הרשאות";
                $perm=json_decode(file_get_contents(__DIR__ . "/data/permissions.json"), true);
                $flag=false;
                foreach ($perm as $i){
                    if (in_array($phone1,$i)){
                        $flag=true;
                        break;
                    }
                }
                if (!$flag){
                    unset($users[$phone1]);
                    $_SESSION["YE_UPDATE_Mes"]='המשתמש הוסר מניהול';
                }
                file_put_contents(__DIR__ . "/data/users.json", json_encode($users));
            }
            else{
            $users=$users+$phone;
            file_put_contents(__DIR__ . "/data/users.json", json_encode($users));
            $_SESSION["YE_UPDATE_Mes"]=(string)($users).'\n\n'.$phone[$phone1]."המשתמש הוגדר כמנהל בהצלחה";
        }
        }
        else {
            $_SESSION["YE_UPDATE_Mes"]="המשתמש אינו קיים";
        }
        headerHome();
    }

    else{
        echo '<div align="center" dir="rtl">
            <br><br>
            <form method="POST">
                <div id="input-container" dir="rtl"></div>
                <input name="phone" type="number" placeholder="מספר טלפון">
                <br><br>
                <button type="submit">עדכן</button>
            </form>
            </div>';
    }
}
function admin__manageUsersPermissions(){
    global $options;
    global $users;
    
    if(isset($_POST['update-users-permissions-post']) && isset($_POST['permissions']) && is_array($_POST['permissions'])){
        $newPermissions = array();
        
        // Set all options in the arr.
        foreach($_POST['permissions'] as $user => $userPermissions){
            foreach ($userPermissions as $permission => $mode){
                $newPermissions[$permission] = array();
            }
            break;
        }
        
        // Fill the options by users.
        foreach($_POST['permissions'] as $user => $userPermissions){
            foreach ($userPermissions as $permission => $mode){
                if($mode === "1"){
                    $newPermissions[$permission][] = $user;
                }
            }
        }
        
        file_put_contents(__DIR__ . "/data/permissions.json", json_encode($newPermissions));
        
        $_SESSION['YE_UPDATE_Mes'] = "<h2 style='color:darkturquoise;'>עדכון ההרשאות בוצע בהצלחה! שכוייעח 😘</h2>";
        headerHome();
    }
    else{
        $users = json_decode(file_get_contents(__DIR__ . "/data/users.json"), true);
        echo '
        <div align="center" dir="rtl">
            ניהול הרשאות משתמשים
            <style>.yes{color:black;background-color:limegreen;font-weight:bold;} .no{color:black;background-color:lightcoral;}</style>
            <script>
                function changeClass(select){
                    if(select.value == "1"){
                        select.setAttribute("class", "yes");
                    }
                    else{
                        select.setAttribute("class", "no");
                    }
                }
            </script>
            <form method="POST">
            <table border=1>';
        
            echo "\n\t\t\t\t\t" . '<tr>';
            echo '<th>שם הרשאה</th>';
            foreach ($users as $user => $notInUse){
                if ($users[$user] == MASTER_USER) continue;
                
                echo '<th>' . htmlspecialchars($users[$user]) . '</th>';
            }
            echo '</tr>';
            
            foreach ($options as $optionName => $allowUsers){
                echo "\n\t\t\t\t\t" . '<tr>';
                echo '<td>' . htmlspecialchars($optionName) . '</td>';
                foreach ($users as $user => $notInUse_2){
                    if ($users[$user] == MASTER_USER) continue;
                    
                    echo '<td>';
                    echo '<select onchange="changeClass(this)" class="' . (in_array($user, $allowUsers) ? "yes" : "no") . '" name="permissions[' . htmlspecialchars($user) . '][' . htmlspecialchars($optionName) . ']">';
                    echo '<option class="yes" value="1" ' . (in_array($user, $allowUsers) ? "selected" : "") . '>כן</option><option class="no" value="0" ' . (in_array($user, $allowUsers) ? "" : "selected") . '>לא</option>';
                    echo '</select>';
                    echo '</td>';
                }
                echo '</tr>';
            }
        
        echo '
            </table>
            <br><br>
            <button type="submit" name="update-users-permissions-post">עדכן</button>
        </form>
        </div>
        ';
    }
}

function admin__settings(){
    $myfile = fopen("C:\\Users\\neria\\Documents\\GitHub\\Bot-Midrash\\settings.html", "r") or die("Unable to open file!");
echo fread($myfile,filesize("C:\\Users\\neria\\Documents\\GitHub\\Bot-Midrash\\settings.html"));
fclose($myfile);
}
?>