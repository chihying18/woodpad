<?php /* Smarty version 2.6.25, created on 2018-08-22 18:42:55
         compiled from home-items.stpl */ ?>
<!-- feature product : begin -->

  <div class="feature" id="<?php echo $this->_tpl_vars['modelName']; ?>
" style="background: url(images/PX747-4K_bk.jpg) no-repeat top center; background-size: cover;">
    <div class="row vs-container">
      <div class="col-md-12" >
         <h2><?php echo $this->_tpl_vars['modelName']; ?>
</h2>
         <p><?php echo $this->_tpl_vars['title']; ?>
</p>
         <img class="productImg" src="<?php echo $this->_tpl_vars['modelImg']; ?>
">

       </div>
                    
        <div class="col-md-6" >
         <p><?php echo $this->_tpl_vars['summary']; ?>
</p>
         <br><br>
         <p><b>Projection System:</b> <?php echo $this->_tpl_vars['projection']; ?>
</p>
         <p><b>Native Resolution:</b> <?php echo $this->_tpl_vars['resolution']; ?>
</p>
         <p><b>Brightness:</b> <?php echo $this->_tpl_vars['brightness']; ?>
</p>
         <p><b>Contrast Ratio With SuperEco Mode:</b> <?php echo $this->_tpl_vars['ratio']; ?>
</p>
         <p><b>Display Colour:</b> <?php echo $this->_tpl_vars['displayColour']; ?>
</p>
         <p><b>Image Size:</b> <?php echo $this->_tpl_vars['imageSize']; ?>
</p>
         <p><b>Throw Distance:</b> <?php echo $this->_tpl_vars['throwDistance']; ?>
</p>
         <p><b>Keystone:</b> <?php echo $this->_tpl_vars['keystone']; ?>
</p>
         <p><b>Optical Zoom:</b> <?php echo $this->_tpl_vars['zoom']; ?>
</p>
         <p><b>Recommended for:</b> <br><br>
         <?php echo $this->_tpl_vars['recommended']; ?>
</p>
       </div>
                    
        <div class="col-md-6 text-center" >
          <div id="canvasBox">
          <canvas id="myCanvas" style=" height: 100%;width: 100%;margin: 0;padding: 0;display: block;">
            Sorry, your browser doesn't support the "canvas" element.
          </canvas>
                        
          <script>
            drawRadar(5, 5, 4, 3 ,3, 4, 4, 4);
          </script>
          </div>

          <button class="btn">MORE INFO</button>
          <button class="btn">BUY NOW</button>

        </div>

    </div>
  </div>
<!-- feature product: end -->
