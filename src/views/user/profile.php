<?php global $language; ?>
<div class="bs-component">
              <div class="card mb-3">
                <h3 class="card-header"><?php echo $language['NAVBAR_PROFILE']; ?></h3>
                <div class="card-body">
                  <h5 class="card-title"><?php echo($_SESSION['user']->username); ?></h5>
                  <h6 class="card-subtitle text-muted"><?php echo $language['PROFILE_ID']; ?>: <?php echo($_SESSION['user']->id); ?></h6>
                </div>
                <ul class="list-group list-group-flush">
                  <li class="list-group-item"><p class="card-text"><u><?php echo $language['BOARD_TEAM']; ?>:</u> <?php echo($_SESSION['user']->team); ?></p></li>
                  <li class="list-group-item"><p class="card-text"><u><?php echo $language['PROFILE_ROLE']; ?>:</u>
                      <?php if($admin_connected) : ?>
                        <span class="badge badge-pill badge-danger"><?php echo $language['PROFILE_ADMIN']; ?></span>
                      <?php else : ?>
                        <span class="badge badge-pill badge-primary"><?php echo $language['PROFILE_USER']; ?></span>
                      <?php endif; ?>
                    </li>
                  <li class="list-group-item"><p class="card-text"><u><?php echo $language['PROFILE_CHANGE_PWD']; ?>:</u>
                    <form method="post" class="form-inline my-2 my-lg-0">
                      <input class="form-control mr-sm-2" type="password" name="newPasswd" placeholder="<?php echo $language['LOGIN_PWD']; ?>">
                      <input class="form-control mr-sm-2" type="password" name="newPasswdConfirm" placeholder="<?php echo $language['PROFILE_PWD_CONFIRM']; ?>">
                      <button type="submit" class="btn btn-primary"><?php echo $language['BOARD_UPDATE']; ?></button>
                    </form>
                  </li>
                </ul>
                <div class="card-footer text-muted">
                  <?php echo(date("Y-m-d H:i:s")); ?>
                </div>
              </div>
    <form action="/user/loggout" method="get" class="form-inline my-2 my-lg-0">
                    <button class="btn btn-danger my-2 my-sm-0" type="submit"><?php echo $language['PROFILE_LOGOUT']; ?></button>
                  </form>
</div>
