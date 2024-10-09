<div class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary">
      <div class="container ">
        <a href="/" class="navbar-brand"><img src="/static/logo/logo120px.png" class="d-inline-block align-middle mr-2" alt="INSAnonym"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav mr-auto">

<?php if($admin_connected) :?>
            <li class="nav-item">
              <a class="nav-link <?=($page_check->isCurrent('admin/board')) ? 'active':''?>" href="/admin/board"><strong><?php echo $language['NAVBAR_ADMIN']; ?></strong></a>
            </li>
<?php endif; ?>

            <li class="nav-item">
              <a class="nav-link <?=($page_check->isCurrent('page/classement')) ? 'active':''?>" href="/page/classement"><?php echo $language['NAVBAR_RANKING']; ?></a>
            </li>




<?php if($user_connected) :?>
            <li class="nav-item">
              <a class="nav-link <?=($page_check->isCurrent('page/start')) ? 'active':''?>" href="/page/start"><?php echo $language['NAVBAR_START']; ?></a>
            </li>
            <?php if($submission_activated) :?>
            <li class="nav-item">
              <a class="nav-link <?=($page_check->isCurrent('anonymisation/submission')) ? 'active':''?>" href="/anonymisation/submission"><?php echo $language['NAVBAR_SUBMISSION']; ?></a>
            </li>
            <?php endif; ?>
            <?php if($attack_activated) :?>
            <li class="nav-item">
              <a class="nav-link <?=($page_check->isCurrent('attack/submission')) ? 'active':''?>" href="/attack/submission"><?php echo $language['NAVBAR_ATTACK']; ?></a>
            </li>
            <?php endif; ?>
<?php endif; ?>

            </ul>
            <ul class="navbar-nav navbar-right" style="margin-right: 10px;">
              <li class="nav-item">
                      <a class="nav-link" target="_blank" href="https://github.com/danymat/INSAnonym-utils/discussions">Forum</a>
                </li>
              <li class="nav-item">
                      <a class="nav-link" target="_blank" href="https://github.com/danymat/INSAnonym-utils">INSAnonym-utils</a>
                </li>
                <li class="nav-item">
                      <a class="nav-link <?=($page_check->isCurrent('page/about')) ? 'active':''?>" href="/page/about"><?php echo $language['NAVBAR_ABOUT']; ?></a>
                </li>
             <form class="form-inline my-2 my-lg-0" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>"> 
                    <input type="hidden" name="change_language">
                    <button class="btn btn-sm btn-secondary my-2 my-sm-0" type="submit"><?php echo $lang; ?></button>
                  </form>  
            </ul>

<?php if($user_connected) : ?>
              <form class="form-inline my-2 my-lg-0">
                    <a href="/user/profile" class="btn btn-secondary my-2 my-sm-0" type="submit"><?php echo $language['NAVBAR_PROFILE']; ?></a>
                  </form>
<?php else : ?>
            <form class="form-inline my-2 my-lg-0">
                    <a href="/user/login" class="btn btn-secondary my-2 my-sm-0" type="submit" ><?php echo $language['NAVBAR_CONNECT']; ?></a>
                  </form>
<?php endif; ?>
        </div>
      </div>
    </div>
