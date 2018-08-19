
          <li class="treeview <?= isActive(['site',''])?>">
            <a href="<?= getPath('/site/index') ?>">
              <i class="fa fa-dashboard"></i> 
              <span>总览</span>
            </a>
          </li>
          <li class="treeview <?= isActive(['alert'])?>" >
            <a href="<?= getPath('/alert/index') ?>">
              <i class="fa fa-heartbeat"></i>
              <span>告警</span>
            </a>
          </li>
          <li class="treeview <?= isActive(['host'])?>">
            <a href="<?= getPath('/host/index') ?>">
              <i class="fa fa-server"></i>
              <span>安全设备</span>
            </a>
          </li>
          <li class="treeview <?= isActive(['report'])?>">
            <a href="<?= getPath('/report/index') ?>">
              <i class="fa fa-line-chart"></i>
              <span>报表</span>
            </a>
          </li>
<?php if (Yii::$app->user->identity->role == 'admin') { ?>
          <li class="treeview <?= isActive(['seting'])?>" >
            <a href="<?= getPath('/seting/user') ?>">
              <i class="fa fa-gears"></i>
              <span>设置</span>
            </a>
          </li>
          <?php } ?>
