<?php global $language; ?>
<div class="bs-component">
    <div class="row">
              <div class="col-lg-12">
                <div class="page-header">
                    <h1><?php echo $language['NAVBAR_ADMIN']; ?></h1>
    </div></div></div>
    <br>



    <div class=container>
        <div class="col-lg-12">
        <div class="bs-component">
            <h3><?php echo $language['BOARD_ADMIN_UPLOAD_DESC']; ?> </h3>
        </div>
        <div class=container>
                <table class="table">
                    <tr scope="row">
                        <th scope="col-8">
                                <div class="custom-file" id="container">
                                    <input type="file" id="custom-file-input" href="javascript:;" class="custom-file-input" name="customFile">
                                    <label class="custom-file-label" for="customFile"><?php echo $language['BOARD_SELECT_FILE']; ?></label>
                                </div>
                                <th scope="col-sm-4">
                                        <button id="uploadfiles" class="btn btn-primary "/><?php echo $language['BOARD_SEND']; ?></button>
                                </th>
                        </th>
                    </tr>
                </table>
        </div>
    <ul id="filelist"></ul>
    <pre id="console"></pre>
<script>
$(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
</script>
<script type="text/javascript">
// Custom example logic

var uploader = new plupload.Uploader({
browse_button : 'custom-file-input',
    container: document.getElementById('container'),
    chunk_size: '2MB',
    url : '/admin/upload',

    init: {
    PostInit: function() {
        document.getElementById('filelist').innerHTML = '';
        document.getElementById('uploadfiles').onclick = function() {
            uploader.start();
            return false;
        };
    },

    FilesAdded: function(up, files) {
        plupload.each(files, function(file) {
            document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
            });
        },

        UploadProgress: function(up, file) {
            document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
        },

        UploadComplete: function(up, files) {
            window.location = window.location.href;
        },

        Error: function(up, err) {
            document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
        }
}
});

uploader.init();

</script>



        <div class="row">
        <div class="col-lg-12">
        <div class="bs-component">
        <h3><?php echo $language['BOARD_METRICS_DESC']; ?></h3>
    </div>
        <form class="form-inline float-left">
            <div class="mr-sm-2">
                <select name="metricNames[]" class="selectpicker" multiple data-style="btn-outline-warning mr-sm-2" data-live-search="true">
                    <?php foreach($metrics as $metric): ?>
                        <option value="<?=$metric?>" <?=(!isset($activeMetrics) || in_array($metric, $activeMetrics))?'selected':''?>><?=$metric?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="form-inline float-left">
                <select class="form-control mr-sm-2" id="aggregName" name="aggregName">
                    <option <?php if("min" == $selectedAggreg) :?>selected<?php endif; ?>>min</option>
                    <option <?php if("max" == $selectedAggreg) :?>selected<?php endif; ?>>max</option>
                    <option <?php if("median" == $selectedAggreg) :?>selected<?php endif; ?>>median</option>
                    <option <?php if("mean" == $selectedAggreg) :?>selected<?php endif; ?>>mean</option>
                </select>
            </div>
            <br><br>
                <input class="btn btn-warning" type="submit" value="<?php echo $language['BOARD_SELECT']; ?>">
        </form>
    </div>
</div>
<div class="row">
    <div class="col-lg-12" style="margin-top: 10px;">
        <table class="table">
          <thead class="table-warning">
            <tr>
              <th scope="col"><?php echo $language['BOARD_METRIC']; ?></th>
              <th scope="col"><?php echo $language['BOARD_PARAMETERS']; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($activeMetrics as $metric): ?>
                <thead class="table-active">
                <tr>
                    <td><strong><?=$metric ?></strong></td>

                    <td>
                    <form class="form-inline" method="post">
                    <input class="form-control mr-sm-2" name="metricParametersUpdate" placeholder="JSON" value="<?=htmlspecialchars($mectricsParameters[$metric]) ?>" style="width: 60%;">
                    <input type=hidden class="form-control" name="metricName" value="<?=$metric ?>">
                    <button type="submit" class="btn btn-primary"><?php echo $language['BOARD_UPDATE']; ?></button>
                    </form>

                    </td>
                </tr>
            </thead>
            <?php endforeach ?>
          </tbody>
        </table>
    </div>
</div>



    <br>
    <div class="row">
        <div class="col-lg-12">
        <div class="bs-component">
            <h3><?php echo $language['BOARD_ADD_SCRIPT']; ?></h3>
        </div>
        <div class=container>
                <table class="table">
                    <tr scope="row">
                        <th scope="col-8">
                                <div class="custom-file" id="scriptContainer">
                                    <input type="file" id="custom-script-input" href="javascript:;" class="custom-file-input" name="customScript">
                                    <label class="custom-file-label" for="customScript"><?php echo $language['BOARD_SELECT_FILE']; ?></label>
                                </div>
                                <th scope="col-sm-4">
                                        <button id="uploadScriptfiles" class="btn btn-primary "/><?php echo $language['BOARD_SEND']; ?></button>
                                </th>
                        </th>
                    </tr>
                </table>
        </div>
    <ul id="scriptlist"></ul>
    <pre id="scriptConsole"></pre>
<script>
$(".custom-script-input").on("change", function() {
    var scriptName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(scriptName);
});
</script>
<script type="text/javascript">
// Custom example logic

var scriptUploader = new plupload.Uploader({
browse_button : 'custom-script-input',
    scriptContainer: document.getElementById('scriptContainer'),
    chunk_size: '2MB',
    url : '/admin/uploadScript',

    init: {
    PostInit: function() {
        document.getElementById('scriptlist').innerHTML = '';
        document.getElementById('uploadScriptfiles').onclick = function() {
            scriptUploader.start();
            return false;
        };
    },

    FilesAdded: function(up, files) {
        plupload.each(files, function(file) {
            document.getElementById('scriptlist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
            });
        },

        UploadProgress: function(up, file) {
            document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
        },

        UploadComplete: function(up, files) {
            window.location = window.location.href;
        },

        Error: function(up, err) {
            document.getElementById('scriptConsole').innerHTML += "\nError #" + err.code + ": " + err.message;
        }
}
});

scriptUploader.init();

</script>



    <br>
    <div class="row">
    <div class="col-lg-12">
    <div class="bs-component">
        <h3><?php echo $language['BOARD_STAGES_DESC']; ?></h3>

        <?php if(!$submission_activated) :?>
        <form class="form-group" method="post">
                <input type=hidden class="form-control" name="submission_phase" value=true>
                <button type="submit" class="btn btn-success "><?php echo $language['BOARD_STAGES_ACTIVATE']; ?></button>
        </form>
        <?php endif; ?>
        <?php if($submission_activated) :?>
    <form class="form-group" method="post">
                <input type=hidden class="form-control" name="submission_phase" value=false>
                <button type="submit" class="btn btn-danger "><?php echo $language['BOARD_STAGES_DEACTIVATE']; ?></button>
        </form>
        <?php endif; ?>

    <?php if(!$attack_activated) :?>
    <form class="form-group" method="post">
                <input type=hidden class="form-control" name="attack_phase" value=true>
                <button type="submit" class="btn btn-success "><?php echo $language['BOARD_STAGES_ACTIVATE_ATTACK']; ?></button>
        </form>
        <?php endif; ?>
        <?php if($attack_activated) :?>
    <form class="form-group" method="post">
                <input type=hidden class="form-control" name="attack_phase" value=false>
                <button type="submit" class="btn btn-danger "><?php echo $language['BOARD_STAGES_DEACTIVATE_ATTACK']; ?></button>
        </form>
        <?php endif; ?>

    </div>
    </div>
    </div>
    <br>

    <div class="row">
    <div class="col-lg-12">
    <div class="bs-component">
    <h3><?php echo $language['BOARD_AUTHORIZATION']; ?></h3>
    <table class="table">
      <thead class="thead-dark">
        <tr>
          <th scope="col"><?php echo $language['BOARD_USERNAME']; ?></th>
          <th scope="col"><?php echo $language['BOARD_TEAM']; ?></th>
          <th scope="col"><?php echo $language['BOARD_AUTH_DESC']; ?></th>
          <th scope="col"><?php echo $language['BOARD_SUBMISSION_DELETE']; ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <?php foreach($users as $user): ?>
                <?php if ( !$user->registered ) { ?>
                    <td><strong><?=$user->username ?> (ID: <?=$user->id ?>)</strong></td>
                    <td><u><?=$user->team ?></u></td>
                    <th scope="col-8">
                            <form method="post">
                                <input type="hidden"  class="form-control"  name="authorizeId" value="<?=$user->id ?>" >
                                <input type="submit" name="submit" value="<?php echo $language['BOARD_AUTH_DESC']; ?>" class="btn btn-success">
                            </form>
                        </th>
                    <th scope="col-8">
                            <form method="post">
                                <input type="hidden"  class="form-control"  name="kickID" value="<?=$user->id ?>" >
                                <input type="submit" name="submit" value="<?php echo $language['BOARD_SUBMISSION_DELETE']; ?>" class="btn btn-danger">
                            </form>
                        </th>
                <?php } ?>
        </tr>
            <?php endforeach?>
        </tbody>
</table>

    <br>
    <div class="row">
    <div class="col-lg-12">
    <div class="bs-component">
    <h3><?php echo $language['BOARD_TEAM_MANAGEMENT']; ?></h3>
    <table class="table">
      <thead class="thead-dark">
        <tr>
          <th scope="col"><?php echo $language['BOARD_USERNAME']; ?></th>
          <th scope="col"><?php echo $language['BOARD_TEAM']; ?></th>
          <th scope="col"><?php echo $language['BOARD_ATTACK_SCORE']; ?></th>
          <th scope="col"><?php echo $language['BOARD_DEFENSE_SCORE']; ?></th>
        </tr>
      </thead>
      <tbody>

        <?php foreach($users as $user): ?>
                <?php if ( $user->registered ) { ?>
            <thead class="table-info">
            <tr>
                <td><strong><?=$user->username ?> (ID: <?=$user->id ?>)</strong></td>
                <td><u><?=$user->team ?></u></td>
                <td><?=round($user->getAttackScore(),4)?>/<?=count($user->getAllUsers())?></td>
                <td><?=round($user->getDefenseScore()*100,2) ?>%</td>

                <td>
                <form class="form-inline" method="post">
                <input class="form-control mr-sm-2" type="password" name="newPasswd" placeholder="Mot de passe">
                <input type=hidden class="form-control" name="id" value="<?=$user->id ?>">
                <button type="submit" class="btn btn-primary"><?php echo $language['BOARD_BTN_CHANGE_PWD']; ?></button>
                </form>
                </td>

                <td>
                <form class="form-inline" method="post">
                <input class="form-control mr-sm-2" name="newName" placeholder="Nom d'équipe">
                <input type=hidden class="form-control" name="id" value="<?=$user->id ?>">
                <button type="submit" class="btn btn-primary"><?php echo $language['BOARD_BTN_CHANGE_NAME']; ?></button>
                </form>
                </td>

                <td>
                <form method="post">
                <input type=hidden class="form-control" name="kickID" value="<?=$user->id ?>">
                <button type="submit" class="btn btn-danger "><?php echo $language['BOARD_BTN_DISQUALIFY']; ?></button>
                </form>
                </td>
                <td>
                    <button data-toggle="collapse" data-target="#Id<?=$user->id ?>" class="accordion-toggle btn btn-secondary">+</button>
                </td>
            </tr>
        </thead>


        <tbody >
        <?php foreach($submissions as $submission): ?>
                    <?php if($user->id==$submission->userId) :?>
                    <tr id="Id<?=$user->id ?>" class="accordian-body collapse">
                    <th scope="row"> <?php echo $language['BOARD_SUBMISSION_DESC']; ?> : #<?=$submission->submissionId ?> <?=$submission->name ?></th>
                        <td> <?php echo $language['BOARD_SUBMISSION_UTILITY']; ?> <?=round($submission->utility,4)?> </td>
                        <td> <?php echo $language['BOARD_SUBMISSION_ATTACK_NAIVE']; ?> <?=round($submission->naiveAttack,4)?> </td>
                        <td> <?php echo $language['BOARD_SUBMISSION_STATUS']; ?> <?= $submission->status?>  </td>
                        <th scope="col-8">
                            <form method="post">
                                <input type="hidden"  class="form-control"  name="subToDelete" value=<?=$submission->submissionId?> >
                                <input type="submit" name="submit" value="<?php echo $language['BOARD_SUBMISSION_DELETE']; ?>" class="btn btn-danger">
                            </form>
                        </th>
                        <th scope="col-8">
                            <form method="post">
                                <input type="hidden"  class="form-control"  name="subToDownload" value=<?=$submission->submissionId?> >
                                <input type="submit" name="submit" value="<?php echo $language['BOARD_SUBMISSION_DOWNLOAD']; ?>" class="btn btn-success">
                            </form>
                        </th>
                    </th>
                    </tr>
                    <?php endif; ?>
        <?php endforeach?>
        </tbody>

        <?php } ?>
        <?php endforeach ?>

      </tbody>
    </table>
    </div>
    </div>
    </div>




  <br>
  <br>

</div>
