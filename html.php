<?php
define('MAX_SIZE',5000000);
function printHead(){
    echo'<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
  integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
  integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
  integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
  integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <html dir="rtl">';
}
function printHeadTitle($title){
    printHead();
    echo'<title>'.$title.'</title>
    <div align="center"><br>
    <h1>'.$title.'</h1><br>';
    if (isset($_SESSION['YE_UPDATE_Mes'])){
        echo '<h4>'.$_SESSION['YE_UPDATE_Mes'].'</h4>';
        $_SESSION['YE_UPDATE_Mes']='';
    }
}

function checkComment($comment){
    return !(strpos($comment, "\n")!==false || strpos($comment, "    ")!==false || strpos($comment, "\t")!==false);
}

function admin__shabatMenu(){
    if (isset($_POST['update'])){
        if (isset($_POST['shabat-comment'])&&checkComment($_POST['shabat-comment'])){
            //set to DB
        }
        elseif (isset($_POST['shabat-comment'])&&!checkComment($_POST['shabat-comment'])){
            $_SESSION['YE_UPDATE_Mes']="השתמשת בתווים לא חוקיים";
            header('refresh: 0');
            return;
        }
        for ($i=0,$saturdays=iterator_to_array(getSaturdays(date("Y"), 1),true);
            $i < sizeof($saturdays); ++$i) {
            if (isset($_POST['cb'.$i])&&$_POST['cb'.$i]=='on'){
            //set to DB
            }
        }
        $_SESSION['YE_UPDATE_Mes']="העדכון בוצע בהצלחה";
        headerHome();
    }
    else if (isset($_POST['send-shabat-reminde'])){
        //send to all
    }
    else{
        $title = "ניהול תזכורות לשבת";
        printHeadTitle($title);

        echo '<hr>
        <h3>עדכון הערה בתזכורת לשבת</h3>
        <form method="POST">
            <input name="shabat-comment" style="width: 550px;">
            <br><br><hr>
            <h3>עדכון תאריכי תזכורות לשבת</h3>
            <br><br>
            <table class="table" dir="rtl" >
            <tbody>';
        $row=7;
        $saturdays=iterator_to_array(getSaturdays(date("Y"),date("Y")+1,8, 7),true);
        for ($i=0; $i<$row; ++$i) {
            echo'<tr>
            <th scope="row">'.$i.'</th>';
            for ($j=0; $j < sizeof($saturdays)/$row-1; ++$j) {
                $d=intval($saturdays[$j*$row+$i]->format("d\n"));
                $m=intval($saturdays[$j*$row+$i]->format("m\n"));
                $y=intval($saturdays[$j*$row+$i]->format("Y\n"));
                $jd = gregoriantojd($m, $d, $y);
                $h=jdtojewish($jd, true, CAL_JEWISH_ADD_GERESHAYIM);
                $h=iconv('ISO-8859-8', 'UTF-8', $h);
                echo 
                '<td>'.$saturdays[$j*$row+$i]->format("d/m/Y\n").' '.$h.'
                <input name="cb'.$j*$row+$i.'" type="checkbox"></td>';
            }
            if ($i<sizeof($saturdays)-$row*$row){
                $d=intval($saturdays[$row*$row+$i]->format("d\n"));
                $m=intval($saturdays[$row*$row+$i]->format("m\n"));
                $y=intval($saturdays[$row*$row+$i]->format("Y\n"));
                $jd = gregoriantojd($m, $d, $y);
                $h=jdtojewish($jd, true, CAL_JEWISH_ADD_GERESHAYIM);
                $h=iconv('ISO-8859-8', 'UTF-8', $h);
                    echo '<td>'.$saturdays[$row*$row+$i]->format("d/m/Y\n").' '.$h.'
                    <input name="cb'.$j*$row+$i.'" type="checkbox"></td>';
                }
            echo'</tr>';
        }
        echo'</tbody></table>
            <button name="update" type="submit">עדכן</button>
        </form>
        <hr>
        <h3>שליחת תזכורת לשבת עכשיו<h3>
        <form method="POST">
        <button type="button" class="btn btn-primary" 
        data-toggle="modal" data-target="#exampleModal">שלח עכשיו</button>';
        printModal('send-shabat-reminde');
    }
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
        printHeadTitle('חסימת/שחרור משתמש');
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
            
            <script>';
    }
}
function admin__sendMessageToUser(){
    global $from;
    global $messageId;
    print_r($_POST);
    if(isset($_POST['send-message-one-user'], $_POST['phone'])){
        $adminPanelMesColor = "darkturquoise";

            $from = $_POST['phone'];
            $messageId = "adminPanel_send_one_message__" . uniqid();
            //sendMessage($_POST['phone'], "text", $_POST['send-message-one-user']);
            $adminPanelMes = "במידה והמשתמש פעיל בבוט, ההודעה נשלחה בהצלחה";
        

            $_SESSION['YE_UPDATE_Mes'] = "<h2 style='color:" . $adminPanelMesColor . ";'>" . $adminPanelMes . "</h2>";
            headerHome();
    }
    else{            
        printHeadTitle('שליחת הודעה למשתמש');
        echo '
        <div align=center dir="rtl">
        <form method="POST">
            <br><br>
            מספר טלפון: 
            <input name="phone" type="number" dir="ltr" required/>
            <br><br>
            תוכן ההודעה: <br><textarea rows="12" cols="50" name="send-message-one-user" required></textarea>
            <br><br>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">שלח עכשיו</button>';
            printModal('message');
    }
}
function admin__updateContacts(){
    //$link=getContactsLink(yeshivaID);
    $link='https://google.com';
    $count=3;

    if(isset($_POST["update-contacts-request"])){
        //initSheets();
        //updateAllContactsFromGoogleSheets();
        
        //$count = getCountOfContactsUsers();
        
        $_SESSION['YE_UPDATE_Mes'] = "<h2 style='color:darkturquoise;'>" . $count . " אנשי הקשר עודכנו מהגוגל שיטס בהצלחה! תבורך 😘</h2>";
    
        headerHome();
    }
    else{
        //$yID=select yeshivaID from users where phone=$_SESSION['YE_UPDATE_User']['Logged'];
        //$count=select count(id) from users where yeshivaId=
        echo '<div align="center">
        <form method="POST">
            <h2>מספר המשתמשים הקיים '.$count.'</h2>
            <button name="update-contacts-request">עדכן</button>
            <br><br>
            <a href='.$link.' target="_blank">קובץ המשתמשים</a>
            </form>
            </div>
        ';
    }
}
function admin__updateShiftsTable(){}
function admin__updateGuardsTable(){}
function admin__sendMessageToAllUsers(){
    if(isset($_POST['message'])){
        //send message
        $_SESSION['YE_UPDATE_Mes']='ההודעה נשלחה בהצלחה';
        headerHome();
    }
    else{
        printHeadTitle('שליחת הודעת תפוצה');
        echo '<form method="POST"><br>
        <div align="center">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                שלח
            </button>
            <textarea cols="50" rows="10" name="message" placeholder="הכנס את תוכן ההודעה כאן"></textarea>
        </div>';
    }
}
function admin__sendSpecialRegister(){
    global $speicalRegisterData;
    global $DBConn;
    global $messageId;
    global $from;

    if(isset($_POST['send-reminde-name'], $_POST['send-reminde-longCommand'], $_POST['send-reminde-shortCommand'], $_POST['send-reminde-comment']) && 
        is_string($_POST['send-reminde-name']) && is_string($_POST['send-reminde-longCommand']) && is_string($_POST['send-reminde-shortCommand']) && is_string($_POST['send-reminde-comment']) &&
        !empty($_POST['send-reminde-name']) && !empty($_POST['send-reminde-longCommand']) && !empty($_POST['send-reminde-shortCommand'])
        ){
            if(!checkComment($_POST['send-reminde-name']) ||
            !checkComment($_POST['send-reminde-longCommand'])
                || !checkComment($_POST['send-reminde-shortCommand'])
                || !checkComment($_POST['send-reminde-comment'])
                ){
                    $_SESSION['YE_UPDATE_Mes']="השתמשת בתווים לא חוקיים";
                    header('refresh: 0');
            }
            else{
                $_SESSION['YE_UPDATE_Reminde'] = array(
                    "name" => $_POST['send-reminde-name'],
                    "longCommand" => $_POST['send-reminde-longCommand'],
                    "longCommand2" => $_POST['send-reminde-longCommand-2'],
                    "shortCommand" => $_POST['send-reminde-shortCommand'],
                    "shortCommand2" => $_POST['send-reminde-shortCommand-2'],
                    "comment" => $_POST['send-reminde-comment']
                );
                
                $title = "שליחת תזכורת לרישום מיוחד [תצוגה מקדימה]";
                $message = '<h2 style="color:red;">זכור! זוהי פעולה שאי אפשר לבטלה! חשוב שנית על הניסוח וחשיבות התזכורת</h2>';
                //printHeadWithDiv($title, $message);
                printHeadTitle($title);
                echo '
                <form method="POST">
                    <pre dir="rtl" style="text-align: right; margin-right: 20%;">
                        <b>🔔 תזכורת להירשם ל' . htmlspecialchars($_POST['send-reminde-name']) . '</b>
                        
                        באפשרותך להירשם באמצעות הפקודה "' . htmlspecialchars($_POST['send-reminde-longCommand']) . '" (' . htmlspecialchars($_POST['send-reminde-shortCommand']) . ') או באמצעות הלחצנים המופיעים בתחתית ההודעה.
                        ' . htmlspecialchars($_POST['send-reminde-comment']) . '
        
                        <i>לאחר שנרשמת, תזכיר למי שנמצא מימינך ושמאלך להירשם...</i>
                    </pre>
                    <br><br>
                    <button onclick="history.back();return false;">חזור אחורה</button>
                    <input type="submit" name="only-save-special-reminde-data" onclick="this.value=1" value="שמירה של הפקודה ללא שליחה"/>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" onclick="this.value=1">שליחה</button>';
                    printModal('send-reminde-done');
                //printEndWithDiv();
            }
    }
    elseif(isset($_POST['only-save-special-reminde-data']) && $_POST['only-save-special-reminde-data'] == 1){
        $remindeData = $_SESSION['YE_UPDATE_Reminde'];
        //file_put_contents(__DIR__ . "/data/specialReg.json", json_encode($remindeData, true));
        unset($_SESSION['YE_UPDATE_Reminde']);
        $_SESSION['YE_UPDATE_Mes'] = "<h2 style='color:darkturquoise;'>הפקודה נשמרה בהצלחה! שכוייעח עצום 😉</h2>";
        headerHome();
    }
    elseif(isset($_POST['send-reminde-done']) && !empty($_SESSION['YE_UPDATE_Reminde'])){
        $remindeData = $_SESSION['YE_UPDATE_Reminde'];
        //file_put_contents(BOT_MIDRASH_INCLUDE_DIR . "/data/specialReg.json", json_encode($remindeData, true));
        unset($_SESSION['YE_UPDATE_Reminde']);
        
        /*$selectUsers = $DBConn->prepare("SELECT `phone`,`last_name` FROM `YBM_ShabatReminder` WHERE `mode` = 1");
        //$selectUsers->execute();
        //$usersShabatArr = $selectUsers->get_result()->fetch_all(MYSQLI_ASSOC) ?? false;
    
        //$template = createTemplateArrForRemindeMessage($remindeData['name'], $remindeData['longCommand'], $remindeData['shortCommand'], $remindeData['comment'], true);
        
        //dontKillMe();
        
        //initSheets();

        //$allUsersFromExcel = getAllStudents("SPECIAL");

        foreach ($usersShabatArr as $user){
            if(!empty($user['last_name']) && searchUserRegisterStatus($allUsersFromExcel, $user['last_name'])){
                continue;
            }
            
            $messageId = "schedule_cron_send_reminde_special__" . uniqid();
            $from = $user['phone'];
            sendMessage($user['phone'], "template", $template);
        }*/

        $_SESSION['YE_UPDATE_Mes'] = "<h2 style='color:darkturquoise;'>ההודעה נשלחה לכל משתמשי הבוט</h2>";
        
        headerHome();
    }
    else{
        $title = "שליחת תזכורת לרישום מיוחד";
        //printHeadWithDiv($title);
        printHeadTitle($title);

        echo '
        <form method="POST">
            שם התזכורת: <input name="send-reminde-name" onkeyup="updateLongCommand();updateShortCommand();" value="" required/><br><br>
            פקודת רישום ארוכה: <input name="send-reminde-longCommand" onkeyup="updateShortCommand();" value="" required/><br><br>
            פקודת רישום קצרה: <input name="send-reminde-shortCommand" value="" required/><br><br>
            פקודת רישום ארוכה נוספת: <input name="send-reminde-longCommand-2" value=""/><br><br>
            פקודת רישום קצרה נוספת: <input name="send-reminde-shortCommand-2" value=""/><br><br>
            הערה (לא חובה): <br><textarea cols="150" rows="2" name="send-reminde-comment"></textarea>
            <br><br>
            <button type="submit">עבור לתצוגה מקדימה</button>
        </form>
        <script>
            function updateLongCommand(){
                document.getElementsByName(\'send-reminde-longCommand\')[0].value = 
                    \'רישום ל\' + document.getElementsByName(\'send-reminde-name\')[0].value;
            }
            function updateShortCommand(){
                var command = document.getElementsByName(\'send-reminde-longCommand\')[0].value.split(\' \');
                document.getElementsByName(\'send-reminde-shortCommand\')[0].value = 
                    command[0].charAt(0) + (command[1] ? command[1].charAt(0) : \'\') + 
                    (command[2] ? command[2].charAt(0) : \'\') + 
                    (command[3] ? command[3].charAt(0) : \'\') +
                    (command[4] ? command[4].charAt(0) : \'\')
                ;
            }
        </script>';
        //printEndWithDiv();
    }
}
function admin__printLastChatsHtml(){}
function admin__printLogMessagesUserHtml(){}
function admin__printJson(){}
function admin__uploadPhotos(){
    $target='C:\\Users\\neria\\Documents\\GitHub\\Bot-Midrash\\data\\';

    if(isset($_POST['upload'])){
        echo '<br><br>';
        print_r($_FILES);
        echo'<br><br>';
        print_r($_POST);
        
        for ($i=0;$i<sizeof($_FILES['img']['name']);++$i) {
            echo '<br>'.sizeof($_FILES['img']['name']);
            echo '<br>'.$_FILES['img']['name'][$i].'<br>';
            if ($_FILES['img']['name'][$i]==''){
                continue;
            }
            echo'<br><br>'.$i;
            print_r($_FILES["img"]["tmp_name"][$i]);
            saveP($_FILES,$i/*["img"]["tmp_name"][$i], $target.$i.'.jpg'*/);
        }
        headerHome();
    }
    
    else{
        $_SESSION['YE_UPDATE_Array']=array();
        printHead();
        echo '<form method="POST" enctype="multipart/form-data"><br>
        <button class="btn btn-success" name="upload">עדכן</button>
        <br>
        <br>
        <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">שם</th>
            <th scope="col">טלפון</th>
            <th scope="col">תמונה</th>
            </tr>
        </thead>
        <tbody>';
        $i=0;
        $users = json_decode(file_get_contents(__DIR__ . "/data/users.json"), true);
        foreach ($users as $key=>$value) {
            array_push($_SESSION['YE_UPDATE_Array'],$key);
            echo'
                <tr>
                <th scope="row">'.$i.'</th>
                <td>'.$value.'</td>
                <td>'.$key.'</td>
                <td>
                    <label for="img">בחר קובץ</label>
                    <input type="file" id="img['.$i.']" name="img['.$i.']" accept="image/*">
                </td>
                </tr>
            ';
            $i++;
        }
        echo '</tbody></table></form>';
    }
}
function admin__printImagesTable(){}
function admin__deletePhoto(){}
function admin__manageUsers(){
    print_r($_POST);
    if (isset($_POST["update"])){
        foreach ($_POST as $key=>$phone) {
            $_SESSION["YE_UPDATE_Mes"].='<br>';
            if ($phone==='') continue;
            //$name=select name from users where phone=$phone
            $name='###';
            $phone1=array($phone=>$name);
            //select yashivaID from user where phone=$phone
            //$t=select * from users where yeshivaID=yeshivaID
            $t=[$phone1];
            $users = json_decode(file_get_contents(__DIR__ . "/data/users.json"), true);
            if (!empty($t)){
                if (isset($users[$phone])){
                    $perm=json_decode(file_get_contents(__DIR__ . "/data/permissions.json"), true);
                    $flag=false;
                    foreach ($perm as $i){
                        if (in_array($phone,$i)){
                            $flag=true;
                            $_SESSION["YE_UPDATE_Mes"].="המשתמש ".$name." (".$phone.") הינו מנהל, לא ניתן להסיר מנהלים בעלי הרשאות";
                            break;
                        }
                    }
                    if (!$flag){
                        unset($users[$phone]);
                        $_SESSION["YE_UPDATE_Mes"].='המשתמש '.$name.' ('.$phone.') הוסר מניהול';
                    }
                    file_put_contents(__DIR__ . "/data/users.json", json_encode($users));
                }
                else{
                    $users=$users+$phone1;
                    file_put_contents(__DIR__ . "/data/users.json", json_encode($users));
                    $_SESSION["YE_UPDATE_Mes"].="המשתמש ".$name." (".$phone.") הוגדר כמנהל בהצלחה";
                }
            }
            else {
                $_SESSION["YE_UPDATE_Mes"].="המשתמש ".$name." (".$phone.") אינו קיים";
            }
        }
        headerHome();
    }

    else{
        printHeadTitle('הוספת/הסרת מנהל');
        $users = json_decode(file_get_contents(__DIR__ . "/data/users.json"), true);
        echo '<form method="POST">
            <button name="update" type="submit">עדכן</button>
            <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">הסר?</th>
                    <th scope="col">מספר טלפון</th>
                    <th scope="col">שם</th>
                </tr>
            </thead>
            <tbody>
            ';
            $i=0;
            foreach ($users as $phone => $name) {
                echo '<tr><th scoope="row">'.$i.'</th>
                <td><select name="phone' . $i.'">
                    <option class="no" value="">לא</option>
                    <option class="yes" value="'.$phone.'">כן</option>
                </select></td>
                <td>'.$phone.'</td><td> ('.$name.') </td>
                </tr>';
                ++$i;
            }
            echo '</tbody></table>
                <div id="lineContainer" dir="rtl"></div>
                <button onclick="addLine()" type="button">הוסף מנהל</button>
                <!--input name="phone" type="number" placeholder="מספר טלפון"-->
                <br><br>

            <script>
                const lineContainer = document.getElementById("lineContainer");
                var i = '.$i.';
                function addLine() {
                    lineContainer.innerHTML += ` <div id="`+i+`">
                    <button type="button" onclick="delLine(`+i+`)">הסר</button>

                    <input name="phone`+i+`" placeholder="מספר טלפון">
                    <br><br>
                    </div>
                    `;
                    ++i;
                }
                function delLine(index){
                    const element=document.getElementById(index.toString());
                    element.remove();
                }
            </script>
            </form>';
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
        $ad = json_decode(file_get_contents(__DIR__ . "/data/adapter.json"), true);
        printHeadTitle('ניהול הרשאות');
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
                echo '<th>' . htmlspecialchars($users[$user]) . '</th>';
            }
            echo '</tr>';
            
            foreach ($options as $optionName => $allowUsers){
                echo "\n\t\t\t\t\t" . '<tr>';
                echo '<td>' . htmlspecialchars($ad[$optionName]) . '</td>';
                foreach ($users as $user => $notInUse_2){
                    
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
    if (isset($_POST['update'])){
        print_r($_POST);
        print_r($_FILES);
        if ($_POST['shabat']!=''){
            echo'<br>s<br><br>';
            //set in DB
        }
        if($_POST['calls']!=''){
            echo'<br>c<br><br>';
            //set in DB
        }
        if($_POST['guards']!=''){
            echo'<br>g<br><br>';
            //set in DB
        }
        if(isset($_FILES['logo'])){
            //set in DB
            echo 'sssssssssss';
        }

        for ($i=0; $i < (sizeof($_POST)-5)/3; ++$i) { 
            echo $_POST['code'.$i];
            echo'<br>';
            echo $_POST['desc'.$i];
            echo'<br>';
            echo $_POST['text'.$i];
            //db..
        }
        headerHome();
    }
    else {
    printHead();
    echo '
        <form method="POST" enctype="multipart/form-data">
            <div align="center"><br>
                <button name="update">עדכן</button><br><br>
                <input type="url" name="shabat" placeholder="קישור לרישום שבתות">
                <input type="url" name="calls" placeholder="קישור לקובץ אנשי קשר">
                <input type="url" name="guards" placeholder="קישור לרישום שמירות"><br><br>
                <textarea name="general" placeholder="הודעת אודות כאשר לא נמצאה פקודה מתאימה"></textarea><br><br>
                תמונת הישיבה
                <input type="file" name="logo" accept="image/*"><br><br>';
    //$commands=getCommands(yeshivaID);
    $commands=array("zr"=>["mjgd","bfggbghyjtnhfbdv"]);
    $i=0;
    $commandsInput='';
    foreach ($commands as $key => $value) {
        $commandsInput.='<div id="'.$i.'">
        <button type="button" onclick="delLine('.$i.')">הסר</button>
        <input name="code'.$i.'" value="'.htmlspecialchars($key).'">
        <input name="desc'.$i.'" value="'.htmlspecialchars($value[0]).'">
        <input name="text'.$i.'" value="'.htmlspecialchars($value[1]).'" width="50" height="300">
        <br><br>
        </div>';
        ++$i;
    }
    echo'
                <div id="lineContainer">'.$commandsInput.' </div>
                <button onclick="addLine()" type="button">הוסף פקודה</button>
                <script>
                    const lineContainer = document.getElementById("lineContainer");
                    var i = '.$i.';
                    function addLine() {
                        lineContainer.innerHTML += ` <div id="`+i+`">
                        <button type="button" onclick="delLine(`+i+`)">הסר</button>
                        <!--input placeholder="שם פקודה"-->
                        <input name="code`+i+`" placeholder="קוד פקודה">
                        <input name="desc`+i+`" placeholder="תיאור פקודה">
                        <input name="text`+i+`" placeholder="טקסט פקודה" width="50" height="300">
                        <br><br>
                        </div>
                        `;
                        ++i;
                    }
                    function delLine(index){
                        const element=document.getElementById(index.toString());
                        element.remove();
                    }
                </script>
            </div>
        </form>
        </html>';
    }
}

function saveP($files,$index){
    $target_dir = "data/";
    //print_r($_FILES);
    $target_file = $target_dir . basename($_FILES["img"]["name"][$index]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    if (!isset($_SESSION['YE_UPDATE_Mes'])){
        $_SESSION['YE_UPDATE_Mes']='';
    }

    // Check if image file is a actual image or fake image
    echo '<br><br>'.$_FILES["img"]["tmp_name"][$index];
    // $check = getimagesize($_FILES["img"]["tmp_name"][$index]);
    // if($check == false) {
    //     $_SESSION['YE_UPDATE_Mes'].= "File is not an image.";
    // }
        
    // Check file size
    if ($_FILES["img"]["size"][$index] > MAX_SIZE) {
        $_SESSION['YE_UPDATE_Mes'].='<br>התמונה גדולה מדי'.$index;
    }
    // Allow certain file formats
    else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
    $_SESSION['YE_UPDATE_Mes'].= "<br>Sorry, only JPG, JPEG & PNG files are allowed.".$index;
    }
    
    else {
        if (move_uploaded_file($_FILES["img"]["tmp_name"][$index], $target_file)) {
        $_SESSION['YE_UPDATE_Mes'].="<br>התמונה של ". $_SESSION['YE_UPDATE_Array'][$index]. " הועלתה בהצלחה.";
        } else {
        $_SESSION['YE_UPDATE_Mes'].= "<br>Sorry, there was an error uploading your file.";
        }
      }
}

function printModal($name){
    echo '<!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document" align="right" dir="rtl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">שליחת הודעה</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            שים לב לאחר לחיצה על אישור לא ניתן לעשות ביטול, בדוק היטב לפני שליחת ההודעה
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">חזור</button>
                            <p>&nbsp</p>
                            <button type="submit" name="'.$name.'" class="btn btn-primary">אישור ושליחה</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>';
}

function getSaturdays($y1,$y2, $m1,$m2){
    return new DatePeriod(
        new DateTime("first saturday of $y1-$m1"),
        DateInterval::createFromDateString('next saturday'),
        new DateTime("last day of $y2-$m2 23:59:59")
    );
}

?>