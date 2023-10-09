<?php

function where($field, $equal = null)
{
    return (new db())->where($field, $equal);
}

function getCode($phone)
{
    $result = where("phone_number", $phone)->selectFirst("sms_codes", "code");
    return $result['code'];
}

function getAttempts($phone)
{
    $attempts = where("phone_number", $phone)->selectFirst("sms_codes", "attempts");
    return $attempts['attempts'];
}

function addAttempt($attempts)
{
    where("phone_number", $_SESSION['phone'])->update("sms_codes", ["attempts" => $attempts + 1]);
}

function getUserId($phone)
{
    return where("phone_number", $phone)->selectFirst("users", "id")['id'];
}

function removeSmsEntry($phone)
{
    where("phone_number", $phone)->delete("sms_codes");
}

function userExistByPhone($phone)
{
    $result = where("phone_number", $phone)->selectFirst("users", "id");
    return is_countable($result) && count($result) == 1;
}

function echoForm($id, $label, $buttonText)
{
    echo    "<div class='form-floating mb-3' dir='ltr'>
                <input class='form-control' type='text' name='$id' id='$id' placeholder='0'>
                <label for='$id'>$label</label>
            </div>
            <button type='submit' class='w-100 btn btn-lg btn-primary'>$buttonText</button>";
}

function generateCode($phone)
{
    $code = rand(10000, 99999);

    db::insert('sms_codes', [
        "code" => $code, "phone_number" => $phone,
        "ip" => $_SERVER['REMOTE_ADDR'], "attempts" => 0,
        "timestamp" => "CURRENT_TIMESTAMP"
    ]);

    return $code;
}

function message($msg, $status)
{
    return ["msg" => $msg, "status" => $status];
}

function login()
{
    $_SESSION['id'] = getUserId($_SESSION['phone']);
}

function isCodeValid()
{
    if (!isset($_POST['code']))
        return false;
    $code = trim($_POST['code']);
    return preg_match("/\b\d{5}\b/", $code) && getCode($_SESSION['phone']) == $code;
}

function isPhoneValid($phone)
{
    return preg_match("/^05\d{8}$/", $phone);
}

function getCurrentState()
{
    if (!isset($_SESSION['state'])) return 0;
    if (isset($_POST['phone']) && $_SESSION['state'] == 0) return 1;
    return $_SESSION['state'];
}

function start()
{
    $_SESSION['state'] = 0;
}

function handlePhoneNumber()
{
    $phone = str_replace('-', '', trim($_POST['phone']));
    $_SESSION['state'] = 1;
    if (!isPhoneValid($phone)) {
        return message("מספר הטלפון שגוי, הכנס מספר של 10 ספרות שמתחיל ב'05'", "danger");
    } else if (!userExistByPhone($phone)) {
        return message("הטלפון שלך לא קיים במערכת.", "danger");
    } else {
        $code = generateCode($phone);
        $text = "קוד האימות שלך לממשק הניהול של בוט מדרש הוא: " . $code;
        // facebookApi::sendText($phone, $text);
        $_SESSION['phone'] = $phone;
        $_SESSION['state'] = 2;
        return message("הקוד נשלח בהצלחה!", "success");
    }
}

function handleCode()
{
    $attempts = getAttempts($_SESSION['phone']);
    if ($attempts >= 3) {
        removeSmsEntry($_SESSION['phone']);
        $_SESSION = ['state' => 0];
        return message("ביצעת 3 ניסיונות כושלים להכנסת הקוד, הכנס מספר טלפון מחדש:", "dark");
    } else if (isCodeValid()) {
        login();
        removeSmsEntry($_SESSION['phone']);
        header("Location: home");
    } else {
        addAttempt($attempts);
        return message("הקוד שהוזן שגוי.", "danger");
    }
}

$states = ["start", "handlePhoneNumber", "handleCode"];
$result = $states[getCurrentState()]();

?>

<body>

    <div class="container text-center p-5 w-50">
        <div id="msg" class="text-center m-5"></div>
        <form method="post">
            <h1 class="h5 fw-light">בוט מדרש</h1>
            <h1 class="h3 mb-3 fw-normal">התחבר למערכת</h1>

            <?php
            if (isset($result['msg'])) {
                echo '<div class="alert alert-' . $result["status"] . ' alert-dismissible fade show" role="alert">';
                echo $result['msg'];
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
            }

            $state = getCurrentState();
            if ($state != 2) echoForm("phone", "מספר טלפון", "שלח קוד בWhatsapp");
            else echoForm("code", "קוד אימות", "התחבר");
            ?>
        </form>


    </div>
</body>