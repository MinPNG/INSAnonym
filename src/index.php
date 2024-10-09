<?php
// Default language

if (isset($_POST['change_language'])) {
    $lang = $_COOKIE['lang'];
    if ($lang == "fr") {
        $lang = "en";
    }
    else {
        $lang = "fr";
    }
    setcookie("lang", $lang, time()+60*60*24*30, '/');
}
else if (isset($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'];
}
else {
    $locale = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    switch ($locale) {
        case "fr-fr":
            $lang = "fr";
            break;
        case "en_US":
            $lang = "en";
            break;
        default:
            $lang = "en";
    }
    setcookie("lang", $lang, time()+60*60*24*30, '/');
}

//}

require_once("locales/lang.".$lang.".php");

// Config requirement
require_once('config.php');

// Request layout
require_once('routes/router.php');
