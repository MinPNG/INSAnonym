<?php global $language; ?>
<div class="bs-docs-section">
        <div class="row">
          <div class="col-lg-12">
            <div class="page-header">
              <h1 id="forms"><?php echo $language['HOME_DESC3']; ?></h1>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="bs-component">
              <form method="post">
                <fieldset>

                  <div class="form-group">
                    <label for=""><?php echo $language['BOARD_USERNAME']; ?></label>
                    <input class="form-control" name="username" placeholder="Username">
                      <small class="form-text text-muted"><?php echo $language['REGISTER_DESC7']; ?></small>
                  </div>
                  <div class="form-group">
                    <label for=""><?php echo $language['LOGIN_PWD']; ?></label>
                    <input type="password" class="form-control" name="password" placeholder="Password">
                      <small class="form-text text-muted"><?php echo $language['REGISTER_DESC2']; ?></small>
                  </div>
                  <div class="form-group">
                    <label for=""><?php echo $language['BOARD_TEAM']; ?></label>
                    <input class="form-control" name="team" placeholder="Team name">
                    <small class="form-text text-muted"><?php echo $language['REGISTER_DESC8']; ?></small>
                  </div>
                  <div class="bs-component">
                    <p><?php echo $language['REGISTER_DESC6']; ?></p>
                  </div>
                <button type="submit" class="btn btn-primary"><?php echo $language['HOME_DESC3']; ?></button>
                </fieldset>
              </form>
            </div>
          </div>
          </div></div>
