<?php
class UserController
{
    public function __construct()
    {
    }

    public function register()
    {
        global $security;
        global $language;
        if ($security->nameRequestVerify('username') && $security->passwordRequestVerify('password') && $security->nameRequestVerify('team')) {
            if (!$security->passwordIsStrong('password')){
                echo('<div class="bs-component">
                    <div class="alert alert-dismissible alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ADMIN_4'].'</div>
                    </div>');
            } else{
                $user = new User();
                $result = $user->register($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['team']);

                if($result){
                    header('location: /user/login?registered');
                    exit();
                } else {
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ADMIN_2'].'</div>
                        </div>');
                }
            }
        }
        if (isset($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['team'])) {
            echo('<div class="bs-component">
                <div class="alert alert-dismissible alert-danger">
                <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ADMIN_7'].'</div>
                </div>'); 
        }
        require_once('views/user/register.php');
    }

    public function login()
    {
        global $security;
        global $language;
        if ($security->nameRequestVerify('username') && $security->passwordRequestVerify('password')){
            $user = new User();
            $user = $user->login($_REQUEST['username'], $_REQUEST['password']);
            if($user){ // Checks user is valid
                if (!$user->registered) {

                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_USER_4'].'</div>
                        </div>');
                }
                else {
                    session_unset();
                    $_SESSION['user']=$user;
                    if ($user->isAdmin()) $_SESSION['admin']=new Admin($user);
                    header("Location: /user/profile");
                    exit();
                }
            } else {
                echo('<div class="bs-component">
                    <div class="alert alert-dismissible alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_USER_2'].'</div>
                    </div>');
            }
        }
        if (isset($_REQUEST['registered'])){
            echo('<div class="bs-component">
                <div class="alert alert-dismissible alert-success">
                <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_USER_3'].'</div>
                </div>');
        }
        require_once('views/user/login.php');
    }

    public function profile()
    {
        global $security;
        global $language;
        global $admin_connected;
        if(isset($_POST['newPasswd']) && isset($_POST['newPasswdConfirm'])){
            if($security->passwordRequestVerify('newPasswd') && $security->passwordRequestVerify('newPasswdConfirm')){
                if(strcmp($_POST['newPasswd'],$_POST['newPasswdConfirm'])==0){
                    if (!$security->passwordIsStrong('newPasswd')){
                        echo('<div class="bs-component">
                            <div class="alert alert-dismissible alert-danger">
                            <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ADMIN_4'].'</div>
                            </div>');
                    } else{
                        if($_SESSION['user']->changeUserPasswd($_POST['newPasswd'])){
                            echo('<div class="bs-component">
                                <div class="alert alert-dismissible alert-success">
                                <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ADMIN_5'].'</div>
                                </div>');
                        }else{
                            echo('<div class="bs-component">
                                <div class="alert alert-dismissible alert-danger">
                                <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ADMIN_6'].'</div>
                                </div>');
                        }
                    }
                }else{
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ADMIN_6'].'</div>
                        </div>');
                }


            }else{
                echo('<div class="bs-component">
                    <div class="alert alert-dismissible alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ADMIN_6'].'</div>
                    </div>');
            }

        }
        require_once('views/user/profile.php');
    }

    public function loggout()
    {
        session_unset();
        header("Location: /");
        exit();
    }


}
