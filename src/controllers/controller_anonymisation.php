<?php


class AnonymisationController
{
    public $errorMessage;
    public $errorMessages;

    public function __construct()
    {
        global $language;
        $this->errorMessage ="";
        $this->errorMessages = [
            $language['C_ANON_1'],
            $language['C_ANON_2'],
            $language['C_ANON_3'],
            $language['C_ANON_4'],
            $language['C_ANON_5'],
            $language['C_ANON_6'],
            $language['C_ANON_7']
        ];
    }
    public function checkNumberofSubPublished($submissions){
        $numberSubPublished =0;
        foreach($submissions as $submission){
            if($submission->isPublished == 1){
                $numberSubPublished +=1;
            }
        }
        if($numberSubPublished>=3){
            return false;
        }else{
            return true;
        }
    }
    public function publishSub($submissions){
        global $security;
        global $language;

        $anonym = new Anonym();
        if($this->checkNumberofSubPublished($submissions)){
            $anonym = $anonym->getFromId($_POST['subToPublish']);
            if($anonym != false){
                if (!$security->idCheck($anonym->userId)){
                    return false;
                }

                if(strcmp ( $anonym->status ,"processing" )==0){
                    $this->errorMessage = $language['C_ANON_8'];
                    return false;
                }else{

                    if($anonym->utility < 0){
                        $this->errorMessage = $this->errorMessages[-$anonym->utility - 1];
                        return false;
                    }else{
                        return $anonym->publish();
                    }

                }
            }else{
                $this->errorMessage = $language['C_ANON_9'];
                return false;
            }
        }else{
            $this->errorMessage = $language['C_ANON_10'];
            return false;
        }

    }
    public function unpublishSub(){
        global $security;
        $anonym = new Anonym();

        $anonym = $anonym->getFromId($_POST['subToUnpublish']);
        if($anonym != false){
            if (!$security->idCheck($anonym->userId)){
                return false;
            }
            return $anonym->unpublish();
        }else{
            return false;
        }
    }
    public function deleteSub(){
        global $security;
        global $language;
        $anonym = new Anonym();
        $anonym = $anonym->getFromId($_POST['subToDelete']);
        if (!$security->idCheck($anonym->userId)){
            return false;
        }
        if($anonym != false){
            if(strcmp ( $anonym->status ,"processing" )==0){
                $this->errorMessage = $language['C_ANON_8'];
                return false;
            }else{
                return $anonym->delete();
            }

        } else {
            return false;
        }
    }

    public function renameSub(){
        global $security;
        $anonym = new Anonym();
        $anonym->submissionId = $_POST['subToRename'];
        return $anonym->rename($_POST['newName']);
    }

    public function upload(){
        global $security;
        global $language;
        if ($security->uploadVerify() && count($_SESSION['user']->getSubmissions()) <= 20 &&  count($_SESSION['user']->getUnprocessedSubmissions()) == 0) {
            Uploader::anonym();
        }
    }

    public function submission()
    {
        global $security;
        global $language;
        $submissions = $_SESSION['user']->getSubmissions();
        //S'il y a une demande de changement de nom de soumission
        if(isset($_POST['newName'])){
            if($security->nameRequestVerify('newName')){
                if($this->renameSub()){
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-success">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_12'].'</div>
                        </div>');
                } else {
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_13'].'</div>
                        </div>');
                }
            }
            else{
                echo('<div class="bs-component">
                    <div class="alert alert-dismissible alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_14'].'</div>
                    </div>');
            }}

            //S'il y a une demande de suppression
            if($security->idRequestVerify('subToDelete')){
                if($this->deleteSub()){
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-success">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_15'].'</div>
                        </div>');
                    } else {
                        echo('<div class="bs-component">
                            <div class="alert alert-dismissible alert-danger">
                            <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_16'].': '.$this->errorMessage.'</div>
                            </div>');
                    }
                }
            if($security->idRequestVerify('subToPublish')){
                if($this->publishSub($submissions)){
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-success">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_17'].'</div>
                        </div>');
                } else {
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_18'].': '.$this->errorMessage.'</div>
                        </div>');
                }

            }
            if(isset($_POST['uploadFiles'])){
                if (! $security->uploadVerify()){
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_25'].'</div>
                        </div>');
                }
                if (count($_SESSION['user']->getSubmissions()) > 20) {
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_26'].'</div>
                        </div>');
                }
                else if ( count($_SESSION['user']->getUnprocessedSubmissions()) > 0){
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_24'].'</div>
                        </div>');
                }
                else {
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-success">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_11'].'</div>
                        </div>');
                }
            }
            if($security->idRequestVerify('subToUnpublish')){
                if($this->unpublishSub()){
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-success">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_19'].'</div>
                        </div>');
                } else {
                    echo('<div class="bs-component">
                        <div class="alert alert-dismissible alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>'.$language['C_ANON_20'].'</div>
                        </div>');
                }

            }
            $submissions = $_SESSION['user']->getSubmissions();
            require_once('views/anonymisation/submission.php');
    }

}
