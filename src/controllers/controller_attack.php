<?php
class AttackController
{
    public function __construct()
    {
    }

    public function submission()
    {
        global $security;
        global $language;
        if($security->uploadVerify() && $security->idRequestVerify('anonymisationID')){
        if($_SESSION['user']->getNumberOfAttackSubmission($_POST['anonymisationID'])[0] < 20){
          if(Uploader::attack()){
              echo('<div class="bs-component">
            <div class="alert alert-dismissible alert-success">
              <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_21'].'</div>
          </div>');
          } else {
            echo('<div class="bs-component">
          <div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_22'].'</div>
        </div>');
          }
        }else{
          echo('<div class="bs-component">
        <div class="alert alert-dismissible alert-danger">
          <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_23'].'</div>
      </div>');
        }
        }

        // Submission start number
        $users = new User();
        $users=$users->getAllUsers();
        require_once('views/attack/submission.php');
    }
}
