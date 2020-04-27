            <div class="col-md-4 col-xs-6 product-item" data-model="<?=$spec['modelName']?>">
              <a href="<?=$GLOBALS['gCountryBaseURL']?>/products/lcd/<?=$spec['moreLink']?>">
                <strong class="tag"><?=$spec['size']?>“</strong>
                <img src="images/products/<?=$spec['image']?>" class="img-responsive product-photo">
              <strong><?=$spec['modelName']?></strong></a>
              <?php
                if (is_array($spec['bullet-point'])) {
                  echo "<ul>\n";
                  foreach ($spec['bullet-point'] as $bp) {
                    echo '<li>'.$bp.'</li>'."\n";
                  }
                  echo "</ul>\n";
                } else {
                  echo $spec['bullet-point'];
                }
              ?>
              <div class="text-center">
                <div class="btn-group">
                  <?php
                    if (isset($spec['buyLink']) && count($spec['buyLink']) > 0) {
                  ?>
                  <a class="btn btn-default btn-buyfrom dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=lstr('txt-Buy Now')?> <span class="caret"></span></a>
                  <ul class="dropdown-menu btnmenu-buy">
                  <?php
                      foreach ($spec['buyLink'] as $etailer => $link) {
                        echo '<li class="link-buy" data-gaurl="'.$buyBaseUrl.'/buy/'.
                              rawurlencode($etailer).'/'.$spec['modelName'].'" data-gaid="'.
                              $GLOBALS['gGAnalyticsCode'].'" data-sku="'.
                              $spec['modelName'].'" data-shop="'.$etailer.'"><a href="'.
                              $link.'" target="_blank">'.$etailer.'</a></li>'."\n";

                      }
                      echo "</ul>\n";
                    }
                    if (!empty($spec['moreLink'])) {
                  ?>
                  </div><a class="btn btn-default btn-learnmore" href="<?=$GLOBALS['gCountryBaseURL']?>/products/lcd/<?=$spec['moreLink']?>"><?=lstr('txt-Learn More')?></a>
                  <?php
                    }
                  ?>
                
              </div>
            </div>