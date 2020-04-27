    <div class="tab-pane tab-product-pane" role="tabpanel" id="<?=$data['domId']?>">

      <div class="vs-container-fluid pane-inner">

        <div class="vs-container">

          <div class="header-detail row vs-vcenter">
            <div class="col-md-5">
              <h2>
                <div class="glyphicon glyphicon-menu-right"></div>
                <?=$data['title']?>
              </h2>
            </div>
            <div class="col-md-7">
              <div class="scene" style="background-image: url('images/<?=$data['scene']?>');"></div>
            </div>
          </div>

          <div class="inner-detail">
<?php

foreach ($data['models'] as $model) {
  if (!isset($products[$model]))
    continue;
  $spec = $products[$model];
  include('templates/model.inc.php');
}

?>
            <div class="clearfix"></div>

            <div class="product-na">
                
                <h3 class="text-center"><i class="fa fa-exclamation-triangle fa-2x"></i> <?=lstr('txt-No products matched')?></h3>

            </div>

          </div>

        </div>

      </div>

    </div>
