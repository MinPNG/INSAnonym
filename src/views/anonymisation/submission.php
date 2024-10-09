<?php global $language; ?>
<form method="POST" id="UploadForm">
<input type="hidden" name="uploadFiles" />
</form>

<div class="jumbotron">
    <h1><?php echo $language['SUBMISSION_DESC']; ?></h1>
    <p><u><?php echo $language['SUBMISSION_REMINDER']; ?></u> <?php echo $language['SUBMISSION_REMINDER_DESC']; ?> </p>
</div>

<div class=container>
    <div class="text-primary">
        <h2><?php echo $language['SUBMISSION_CHOOSE_DESC']; ?> </h2>
    </div>
    <div class=container>
            <table class="table">
                <tr scope="row">
                    <th scope="col-8">
                            <div class="custom-file" id="container">
                                <input type="file" id="custom-file-input" href="javascript:;" class="custom-file-input" name="customFile">
                                <label class="custom-file-label" for="customFile"><?php echo $language['SUBMISSION_SELECT_ZIP']; ?></label>
                            </div>
                            <th scope="col-sm-4">
                                <button id="uploadfiles" class="btn btn-primary "/><?php echo $language['BOARD_SEND']; ?></button>
                            </th>
                    </th>
                </tr>
            </table>
<ul id="filelist"></ul>
<br />
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
    url : '/anonymisation/upload',

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
            document.getElementById("UploadForm").submit();
        },

        Error: function(up, err) {
            document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
        }
    }
});

uploader.init();

</script>
<br>
<br>
<table class="table">
  <thead class="table-info">
    <tr>
      <th scope="col"><?php echo $language['SUBMISSION_COL_SUBMISSIONS']; ?></th>
        <th scope="col"><?php echo $language['BOARD_SUBMISSION_UTILITY']; ?></th>
        <th scope="col"><?php echo $language['SUBMISSION_COL_ATTACK_NAIVE']; ?></th>
        <th scope="col"><?php echo $language['SUBMISSION_COL_STATUS']; ?></th>
                <th scope="col"><?php echo $language['BOARD_BTN_CHANGE_NAME']; ?></th>
        <th scope="col"><?php echo $language['BOARD_SUBMISSION_DELETE']; ?></th>
        <th scope="col"><?php echo $language['SUBMISSION_COL_PUBLISH']; ?></th>
    </tr>
  </thead>
  <tbody>
        <?php foreach($submissions as $submission): ?>
                    <tr>
                    <th scope="row">#<?=$submission->submissionId ?> <?=$submission->name ?></th>
                        <td> <?=round($submission->utility,4)?> </td>
                        <td> <?=round($submission->naiveAttack,4)?>  </td>
                        <td> Status : <?= $submission->status?>  </td>
                                                <td>
                                                    <form method="post" class="form-inline">
                                                        <input class="form-control mr-sm-2" name="newName" placeholder="<?php echo $language['SUBMISSION_NAME_DESC']; ?>">
                                                        <input type=hidden class="form-control" name="subToRename" value="<?=$submission->submissionId ?>">
                                                        <button type="submit" class="btn btn-primary"><?php echo $language['SUBMISSION_MODIFY']; ?></button>
                                                    </form>
                                                </td>
                        <td>
                            <form method="post">
                                <input type="hidden"  class="form-control"  name="subToDelete" value=<?=$submission->submissionId?> >
                                <input type="submit" name="submit" value="<?php echo $language['BOARD_SUBMISSION_DELETE']; ?>" class="btn btn-danger">
                            </form>
                            </td>
                    </th>
<?php
if($submission->naiveAttack >= 0){
    if($submission->isPublished==0){
        echo'<th scope="col-8">';
        echo'<form method="post">';
        echo'<input type="hidden"  class="form-control"  name="subToPublish" value='.$submission->submissionId.'>';
        echo'<input type="submit" name="submit" value="'.$language["SUBMISSION_COL_PUBLISH"].'" class="btn btn-success">';
        echo'	</form>';
        echo'</th>';
                        }else{
                            echo'<th scope="col-8">';
                            echo'<form method="post">';
                            echo'<input type="hidden"  class="form-control"  name="subToUnpublish" value='.$submission->submissionId.'>';
                            echo'<input type="submit" name="submit" value="'.$language["SUBMISSION_UNPUBLISH"].'" class="btn btn-success">';
                            echo'	</form>';
                            echo'</th>';
                        }
                    }
?>
    </tr>
    </tr>
    <?php endforeach?>
    </tbody>
    </table>
