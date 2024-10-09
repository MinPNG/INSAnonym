<?php global $language; ?>
<div class="bs-docs-section">
        <div class="row">
          <div class="col-lg-12">
            <div class="page-header">
              <h1 id="forms"><?php echo $language['NAVBAR_CONNECT']; ?></h1>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="bs-component">
              <form method="post">
                <fieldset>


                  <div class="form-group">
                    <label for="exampleInputPassword1"><?php echo $language['BOARD_USERNAME']; ?></label>
                    <input class="form-control" name="username" placeholder="Username">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1"><?php echo $language['LOGIN_PWD']; ?></label>
                    <input type="password" class="form-control" name="password" placeholder="Password">
                  </div>
                  <button type="submit" class="btn btn-primary"><?php echo $language['NAVBAR_CONNECT']; ?></button>
                </fieldset>
              </form>
          </div>
          </div>
</div>
