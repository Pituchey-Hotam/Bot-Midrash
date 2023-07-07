<?php
function admin__updatePrays(){
    if(isset($_POST['prays-txt']) && is_string($_POST['prays-txt'])){
        file_put_contents(__DIR__ . "/data/prays.txt", $_POST['prays-txt']);
        $_SESSION['YE_UPDATE_Mes'] = "<h2 style='color:darkturquoise;'>עדכון זמני התפילות בוצע בהצלחה! תבורך 😘</h2>";
        headerHome();
        }
    else{
        $praysTxt = file_get_contents(__DIR__. "/data/prays.txt");
        
        $title = "עדכון זמני תפילות";

        echo '<div align=center>
        <i style="color:deeppink;">זמני התפילות נערכו בפעם האחרונה בתאריך: ' . date("H:i d/m/Y", filemtime(__DIR__. "/data/prays.txt")) . 
        ' [הודעה זו מופיעה ל' . htmlspecialchars($_SESSION['YE_UPDATE_User']['Logged'][0]) . ']</i><br><br>
        <form method="POST">
            <span><b>זמני תפילות במניינים הקרובים לישיבה:</b><br><br><b>🔴 הערה חשובה: השימוש במניינים האלו הוא רק בשעת הדחק!</b><br><b>🔴 הערה חשובה: השימוש בזמנים אלו על אחריות המשתמש בלבד ט.ל.ח.</b><span>
            <br>
            <textarea cols="55" rows="35" name="prays-txt">' . htmlspecialchars($praysTxt) . '</textarea>
            <br><br>
            <button type="submit">עדכן</button>
        </form>
        </div>';
    }
}