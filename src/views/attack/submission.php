<?php global $language ?>
<div class="jumbotron">
	<h1> <?php echo $language['ATTACK_SUBMISSION_DESC']; ?></h1>
</div>

<div class=container>
	<div class="text-primary">
		<h2><?php echo $language['ATTACK_SUBMISSION_CHOOSE_DESC']; ?></h2>
	</div>
	<div class=container>
	<?php foreach($users as $user):?>
			<?php if($user->id != $_SESSION['user']->id) :?>
			<table class="table">
			<thead class="table-info">
			<tr>
				<th scope="col"> <?php echo $language['BOARD_TEAM']; ?> <?=$user->team?></th>
				<th scope="col"> <?php echo $language['NAVBAR_ATTACK']; ?> </th>
				<th scope="col"> <?php echo $language['ATTACK_SUBMISSION_DOWNLOAD']; ?> </th>
				<th scope="col"> <?php echo $language['ATTACK_SUBMISSION_UPLOAD']; ?> </th>
				<th scope="col">  </th>
				<th scope="col"> <?php echo $language['ATTACK_SUBMISSION_SEE_ALL']; ?> </th>
			</tr>
			<tbody>
			<?php foreach($user->getPublishedSubmissions() as $submission): ?>
						<tr>
						 <th scope="row">#<?=$submission->submissionId ?> <?= $submission->name ?></th>
						  <th scope="col"> Score: <?= round($_SESSION['user']->getAttackScoreOfSubmission($submission),4) ?></th>
							<?php $nameFile = $user->team."_".$submission->submissionId ?>
						  <th scope="col"> <a class="btn btn-primary" role="button" href="/files/<?=$submission->S_file?>.zip" download="<?=$nameFile?>.zip">source</a></th>
							  <th scope="col">
								  <form action="" method="post" enctype="multipart/form-data">
									  <div class="custom-file">
										  <input type="file" class="custom-file-input" id="customFile" name="file">
										  <input type="hidden" name="anonymisationID" value=<?=$submission->submissionId?>>
										  <label class="custom-file-label" for="customFile"><?php echo $language['BOARD_SELECT_FILE']; ?></label>
									  </div>
									  <th scope="col">
										  <input type="submit"  name="submit" value="<?php echo $language['ATTACK_SUBMISSION_SEND']?>" class="btn btn-primary">
									  </th>
								  </form>
							  </th>
							<th scope="col">
									<button data-toggle="collapse" data-target="#Id<?= $submission->submissionId ?>" class="accordion-toggle btn btn-warning ">+</button>
							</th>
						</tr>
						<tr>
								<td colspan="6" class="hiddenRow"><div id="Id<?= $submission->submissionId ?>" class="accordian-body collapse">
								<table class="table">
								<thead class="table-warning">
								<tr>
									<th scope="col"> <?php echo $language['ATTACK_SUBMISSION_ID']?></th>
									<th scope="col"> <?php echo $language['ATTACK_SUBMISSION_SCORE']?></th>
								</tr>
								</thead>
								<tbody>
								<?php foreach($_SESSION['user']->getAllAttacksOfSubmission($submission) as $attack): ?>
									<tr>
									<th scope="col"> <?=$attack->AttackId ?></th>
									<th scope="col"> <?=$attack->score ?></th>
								</tr>
								<?php endforeach?>
								</tbody>
								</table>
								</div></td>
						</tr>
				<?php endforeach ?>

		</tbody>
	</table>
	<?php endif; ?>
	<?php endforeach?>
	<script>
	$(".custom-file-input").on("change", function() {
	  var fileName = $(this).val().split("\\").pop();
	  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
	});
	</script>
	</div>
</div>
