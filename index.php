<?php

require_once('includes/vstw_basic-vsa.php');
require_once('includes/xserver.inc.php');
require_once('includes/geoweb.inc.php');
require_once('configs/configs.inc.php');

if (substr($gPgUrl, strlen($gPgUrl) - 1) == '/') {
    $pgDir = substr($gPgUrl, 0, strlen($gPgUrl) - 1);
    $buyBaseUrl = $_SERVER['REQUEST_URI']."buy/";
} else {
    $pgDir = dirname($gPgUrl);
    $buyBaseUrl = dirname($_SERVER['REQUEST_URI'])."/buy/";
}

siteMetaMobile('Business Class UPGRADE ',
     'viewsonic, desktop monitors, upgrade, vp, vg, professional, business',
     'Business Class UPGRADE ',
     '<meta property="og:type" content="website"/>'."\n".
     '<meta property="og:url" content="'.$GLOBALS['gPgUrl'].'">'."\n".
     '<meta property="og:image" content="'.$GLOBALS['pgDir'].'/images/fb4k-1200x630.jpg">'."\n".
     '<meta property="og:title" content="Business Class UPGRADE ">'."\n".
     '<meta property="og:description" content="Business Class UPGRADE ">'."\n".
     '<meta property="fb:app_id" content="'.$GLOBALS['gFacebookAppId'].'">'."\n".
     '<link href="'.$GLOBALS['gCountryBaseURL'].'/w20/css/animate.css" rel="stylesheet">'."\n".
     '<link href="css/vp-vg-upgrade.css" rel="stylesheet">'."\n".
     '<link href="https://fonts.googleapis.com/css?family=Noto+Sans+TC:400,700&display=swap" rel="stylesheet">'."\n".
     '<link href="css/country.css" rel="stylesheet">'."\n"
 );

siteHeaderMobile();

// get product list based on country determined by GeoIP
$dbConn = vsOpenDB();
$qstr = 'SELECT t1.id,t1.shortname,t1.tagline FROM product_info as t1 WHERE '.
        'onweb IN ("B", "H", "S", "V", "Y") && '.
        'category="'.$productLine.'" && '.
        '(shortname LIKE "'.implode('" || shortname LIKE "', array_keys($products)).'")';
$result = $dbConn->query($qstr);
$actuals = [];
while ($row = $result->fetch_assoc()) {
  $row['shortname'] = strtoupper($row['shortname']);
  $actuals[$row['shortname']] = $products[$row['shortname']];
  if (!$useConfigBullets &&
      $isset($GLOBALS['gProductShowTagline']) && $GLOBALS['gProductShowTagline']) {
    $tagline = html_entity_decode($row['tagline']);
    $tagline = preg_replace('/(<p>|<\/p>)/i', '', $tagline);
    if (strip_tags($tagline) == $tagline) {
      if (strpos($tagline, "•") !== false) {
        $bullets = explode("•", $tagline);
      } else if (strpos($tagline, "*")) {
        $bullets = explode("\n", $tagline);
      } else if (strpos($tagline, "\n")) {
        $bullets = explode("\n", $tagline);
      } else if (strpos($tagline, "\r")) {
        $bullets = explode("\r", $tagline);
      }
      $actuals[$row['shortname']]['bullet-point'] = $bullets;
    } else {
      $actuals[$row['shortname']]['bullet-point'] = $tagline;
    }
  }
  if (isset($useProductBuyLinks) && $useProductBuyLinks) {
    // get buy links from product page buy links
    $qstr = 'SELECT value,field FROM tags WHERE associate_table="product_info" && associate_id='.$row['id'].
            ' && field LIKE "buylink:%"';
    $res = $dbConn->query($qstr);
    $links = array();
    while ($linkRow = $res->fetch_assoc()) {
      $idx = explode(':', $linkRow['field']);
      $links[$idx[1]] = $linkRow['value'];
    }
    $qstr = 'SELECT value,field FROM tags WHERE associate_table="product_info" && associate_id='.$row['id'].
            ' && field LIKE "buylinktitle:%"';
    $res = $dbConn->query($qstr);
    $etailers = array();
    while ($linkRow = $res->fetch_assoc()) {
      $idx = explode(':', $linkRow['field']);
      $etailers[$idx[1]] = $linkRow['value'];
    }
    foreach ($links as $idx => $val) {
      $actuals[$row['shortname']]['buyLink'][$etailers[$idx]] = $val;
    }
  }
}
if ($gSiteType == 'live' || true) {
  $skus = array_keys($products);
  $products = array();
  foreach ($skus as $sku) {
    if (array_key_exists($sku, $actuals))
      $products[$sku] = $actuals[$sku];
  }
}
foreach ($filters as &$filter) {
  $filter = array_values(array_intersect($filter, array_keys($products)));
}

// read reviews from all country DBs defined in $gCountryGroupDBs
$reviews = array();
$revs = array();
if (isset($GLOBALS['gCountryGroupDBs']))
  $cdbs = &$GLOBALS['gCountryGroupDBs'];
else
  $cdbs = array($gCountryDB);
$modelClause = '(t3.shortname LIKE "'.
               implode('" || t3.shortname LIKE "', array_keys($actuals)).'")';
foreach ($cdbs as $db) {
  // exclude those product line specific award from the countries other than
  // the current active one
  if ($db != $GLOBALS['gCountryDB'])
    $exReview = ' && t1.product_id!=0';
  else
    $exReview = '';
  $qstr = 'SELECT t1.title as title,t1.media as media,'.
      't1.url as url,UNIX_TIMESTAMP(t1.post_date) as post_date,'.
      't1.author as author,'.
      't1.product_id as product_id,'.
      't1.logo_path as logo_path,t1.clip_path as clip_path,'.
      't1.quote as quote FROM '.$db.'.product_reviews as t1 LEFT JOIN '.
      $db.'.product_lines as t2 ON t1.pline_id=t2.id LEFT JOIN '.
      $db.'.product_info as t3 ON t1.product_id=t3.id WHERE '.
      $modelClause.' || '.
      '(t1.pline_id!=0 && t2.english_name LIKE "'.$productLine.'"'.$exReview.') '.
      'ORDER BY t1.ordinal,t1.post_date DESC';
  $result = $dbConn->query($qstr);
  while ($row = $result->fetch_assoc()) {
    // avoid duplicates
    if ($row['media'] != "" || $row['url'] == "")
      $key = date("m/Y", $row['post_date']).'-'.$row['media'];
    else 
      $key = $row['url'];
    $row['db'] = $db;
    if (!in_array($key, $revs)) {
      $reviews[] = $row;
      $revs[] = $key;
    }
  }
}

?>

            <!-- page content: begin -->

<div class="promo">
                   

  <!-- overview: begin -->           
  <div id="top" class="kv vs-container-fluid" style="background-color:#000;background: url(images/vpwp-bg.jpg) no-repeat  50% 30%;background-size: cover; " id="top">
               
  

  <div class="top__element hero vs-vcenter text-center">
    
    <div class="wow col-md-5 catch animated" style="visibility: visible;  animation-name: vpvg;"> 
      
      <img class="img-responsive" src="images/vpwp.png"> 
   
    </div> 

    <div class="wow  col-md-6 col-md-offset-1 catch animated" data-wow-duration="0.2s" style="visibility: visible; animation-duration: 0.5s; animation-delay: 0s; animation-name: vpwp;">


        <h1>數位新世代藝術創作</h1>

        <h2>靈感、質感爆發！</h2>

        <h3>條漫創作／攝影修圖／視覺設計／網頁設計／服裝設計</h3>

       <a href="#productDetail" class="btn btn-red opensans">立即購買</a></p>
      </div> 
      
    
  </div>

  </div>

            <!-- overview: end-->

            <!-- tabs panes: begin -->

  <div class="tab-content " id="tabUpgradeContents">
    <!-- slider for 3 videos -->

<!--     <div id="videoList">
      <a class="video center-block" data-target="#videoModal" data-toggle="modal" data-video="D0eBuu6Fqv0"></a><br>
    </div> -->

    
    <!-- <div class="vs-container">
      <div class="col-xs-12 promotion">
         <div class="col-md-3 pflex "><img class="img-responsive center-block" src="images/2019Q3promo.png"></div>
         <div class="col-md-6 promo-border text-center"><h3 class="center-block">買 VG48/VG55 人體工學及 VP Color Pro 系列螢幕</h3>
          <div class="col-xs-5 pflex"><h3><big>抽</big><br><font class="text-primary">雙人商務艙來回機票</font></h3></div>
          <div class="col-xs-2 pflex"><h1 style="border-radius: 100%; background: #900;  height: 40px; width: 40px;padding-top: 1px"><span style="color: white;line-height: 40px">+</span></h1></div>
          <div class="col-xs-5 pflex"><h3><big>送</big><br><font class="text-success">16GB USB 隨身碟</font></div><div class="clearfix"></div>
           <br><p class="text-center" style="color: #900;letter-spacing: 3px"><strong>業界最強 全機5年保固</strong></p>
</div>
         <div class="col-md-3 pflex"> <a href="<?=$GLOBALS['gCountryBaseURL']?>/promos/2019/lcdq3/" target="_blank" class="btn btn-red">了解更多</a></div>
         <div class="clearfix"></div>
      </div><div class="clearfix"></div>
    </div>  
     <br><br> -->
    <!-- <div class="vs-container">

          <div class="embed-responsive embed-responsive-16by9 text-center">
            <iframe class="embed-responsive-item" allow="autoplay; encrypted-media" allowfullscreen="" frameborder="0"  src="https://www.youtube.com/embed/LyJwhk7xVuk?start=2&rel=0&autoplay=1&loop=1&mute=1" ></iframe></div>

    </div> -->

    <div class="home container-filud" style="padding:3rem 0;background-color: #eee;">
	<div class="container">
	
		<section class="home text-center row" style="margin-top:50px;margin-bottom:30px;">
			<div class="text-block">
				
			
			</div>
			
			<div class="clearfix"></div>
			
			<div class="block-group">

           <!-- Photography-->
           <div class="col-sm-12 col-md-4 text-center">
                    <div class="block block-b col-sm-6 col-md-12">

                        
                           <div class="overlay-fade-in" style="border-radius: 100%;margin:3.5em">
                             <a href="#painter"> <img src="images/Painter.jpg" style="border-radius: 100%;  width: 100%;">
                           <div class="block-overlay" style="border-radius: 100%;margin:3.5em;">
                            <div class="txt-loading">
                            
                              
                            </div>
                               </div>
                               </a>
                             </div>
                    </div>
                    <h3 style="width: 100%">Painter</h3>
                </div>
           <!-- Photography end--> 
           
           <!-- Design-->    
                <div class="col-sm-12 col-md-4 text-center">
                    <div class="block block-b col-sm-6 col-md-12">

                        
                           <div class="overlay-fade-in" style="border-radius: 100%;margin:3.5em">
                             <a href="#designer"> <img src="images/Designer.jpg" style="border-radius: 100%; width: 100%;">
                           <div class="block-overlay" style="border-radius: 100%;margin:3.5em;">
                            <div class="txt-loading">
                            
                              
                            </div>
                               </div>
                               </a>
                             </div>
                    </div>
                    <h3 style="width: 100%">Designer</h3>
                </div>
                <!-- Design end--> 
                
                <!-- Video Editing-->
                <div class="col-sm-12 col-md-4 text-center">
                    <div class="block block-b col-sm-6 col-md-12">

                        
                           <div class="overlay-fade-in" style="border-radius: 100%;margin:3.5em">
                             <a href="#photographer"> <img src="images/Photographer.jpg" style="border-radius: 100%; width: 100%;">
                           <div class="block-overlay" style="border-radius: 100%;margin:3.5em;">
                            <div class="txt-loading">
                            
                             
                            </div>
                               </div>
                               </a>
                             </div>
                    </div>
                    <h3 style="width: 100%">Photographer</h3>
                </div>
                 <!-- Video Editing end--> 
                 
                 <div class="clearfix"></div>                    
            </div>
        </section>
    </div>
        </div>
      
      <div class="clearfix"></div>                    
 </div>




<!-- ColorPro+wp -->
<div class="row vs-container rc-feature ">
  <div class="pane-main kv-bg keyspec">
  <div class="col-lg-8 col-lg-offset-2  scrollme animateme" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);text-align: center;">
                   
                    
                    
                    <h2>兩種創作模式 螢幕+繪圖板完美結合</h2>
                    <p>新世代讀者使用手機或是平板作為閱讀工具，作品呈現需要符合讀者需求。條漫創作時，直立式螢幕與直立式繪圖板，畫家更容易一覽整體畫面與故事線。漫畫需要細膩的筆觸呈現，使用WoodPad竹質繪圖筆，容易呈現細部線條，在ColorPro專業色彩顯示器上，更能呈現完美小細節。</p>
                    
                    <br>
                    </div>
    <div class="col-xs-12 col-sm-6 col-lg-6 text-center" id="colorp">
    <img src="images/colorpro-01.png" class="product img-responsive center-block"> <br>
  
      <div class="rc-button">
        <a class="btn btn-white" href="#colorPro">ColorPro</a>
      </div>
    </div>


    <div class="col-xs-12 col-sm-6 col-lg-6 text-center" id="Woodp">
   <img src="images/Woodpad_F01.png" class="product img-responsive center-block"> <br>

   <div class="rc-button">
        <a class="btn btn-white" href="#woodpad">WoodPad</a>
      </div>

     
    </div>   
   
    <div class="clearfix"></div>    
  </div>
  </div>
<!-- ColorPro+wp end -->







<!-- painter -->

                 <div class="row vs-container rc-feature " id="painter">
                    <div class="col-lg-8 col-lg-offset-2  scrollme animateme" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);text-align: center;">
                    <h2>Painter</h2>
                    
                    
                    <h3><b>數位條漫 一覽整體畫面與故事線</b></h3>
                    <p>新世代讀者使用手機或是平板作為閱讀工具，作品呈現需要符合讀者需求。條漫創作時，直立式螢幕與直立式繪圖板，畫家更容易一覽整體畫面與故事線。漫畫需要細膩的筆觸呈現，使用WoodPad竹質繪圖筆，容易呈現細部線條，在ColorPro專業色彩顯示器上，更能呈現完美小細節。</p>
                    
                    
                    </div>
                    <div class="center-block" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100">
                    <img src="images/vpwp-01.jpg" class="img-responsive center-block" style="padding:3em">
                    </div>
                </div>


                <div class="clearfix"></div>

               
<!-- painter end -->

<!-- designer -->
                    <div class="home container-filud" style="padding:3rem 0;background-color: #eee;">
                    <div class="row vs-container rc-feature " id="designer">
                    <div class="col-lg-8 col-lg-offset-2  scrollme animateme" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);text-align: center;">
                    <h2>Designer</h2>
                    
                    
                    <h3><b>數位設計 貼近創作直覺</b></h3>
                    <p>網頁設計師、服裝設計師這類的工作者，因作品的不同，在創作與作品呈現時，時常需要調整螢幕比例。使用VP系列專業螢幕與WoodPad竹質繪圖板，兩種模式自動旋轉，更能符合創作直覺，加上ViewSonic ColorPro專業色彩顯示器涵蓋 100% 的 sRGB 色域，重現豐富鮮明的色彩，完美呈現細節給客戶。
</p>
                    
                    </div>
                    <div class="center-block" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100">
                    <img src="images/vpwp-02.jpg" class="img-responsive center-block" style="padding:3em">
                    </div>
                </div>


                <div class="clearfix"></div>

              
               </div>
<!-- designer end -->



<!--photographer -->

<div class="row vs-container rc-feature " id="photographer">
                    <div class="col-lg-8 col-lg-offset-2  scrollme animateme" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);text-align: center;">
                    <h2>Photographer</h2>
                    
                    
                    <h3><b>攝影後製 提升精確度</b></h3>
                    <p>在顯示器上以橫向模式觀賞縱像照片時，會因為螢幕尺寸而受限。以橫向畫面檢視縱向照片時，可能會忽略微小的細節，導致成品出現預期以外的瑕疵。</p>
                    
                    </div>
                    <div class="center-block" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100">
                    <img src="images/vpwp-03.jpg" class="img-responsive center-block" style="padding:3em">
                    </div>
                </div>


                <div class="clearfix"></div>

               


              
<!-- photographer end -->



<!-- RC Icon-->
<!-- <div class="row vp-icon" style="max-width: 980px;margin: auto;text-align: center;">
	<a class="col-sm-2 col-xs-4" href="/zh-tw/products/VP3268-4K.php#feature5"><img src="images/Auto Pivot.png"><h4>自動旋轉設計 兼顧細節</h4></a>
	<a class="col-sm-2 col-xs-4" href="/zh-tw/explore/content/Color_Calibration_is_Essential_for_Creating_Digital_Imagery_14.html"><img src="images/Color Accuracy.png"><h4>精準色彩 重現真實</h4></a>
	<a class="col-sm-2 col-xs-4" href="/zh-tw/explore/content/Pivot-Screen-Monitors-See-the-Bigger-Picture_15.html"><img src="images/Frameless.png"><h4>無邊框設計 視野無界</h4></a>
	<a class="col-sm-2 col-xs-4" href="/zh-tw/explore/content/DeltaE≦2Color-Accuracy_2.html"><img src="images/Frameless.png"><h4>精準色彩</h4></a>
	<a class="col-sm-2 col-xs-4" href="/zh-tw/explore/content/Thin-Bezel-Monitors-More-Than-Just-Good-Looking_24.html"><img src="images/Frameless.png" ><h4>無邊框設計</h4></a>
	<a class="col-sm-2 col-xs-4" href="/zh-tw/explore/content/KVM_Switch_with_USB_Type-C_for_Efficient_Multi-tasking_27.html"><img src="images/Frameless.png" ><h4>Type-C</h4></a>
  </div> -->
  <!-- RC Icon end-->



   <!-- ColorPro -->
  <div class="row vs-container rc-feature ">
  <div class="pane-main kv-bg keyspec">
    <h2 class="text-center" id="colorPro">ColorPro專業色彩顯示器</h2><br>
    <div class="col-xs-12 col-sm-4 col-lg-4 text-center">
      <img src="images/color-01.png" class="product img-responsive center-block"> <br>
      <h4>自動旋轉設計 兼顧細節</h4>
      <small style="    display: flex;   justify-content: center;">
        <ul style="text-align: left;padding-left: 5vh;">
          <li>符合人體工學設計</li>
          <li>更順暢的影像編輯工作流程</li>
               </ul>
      </small>
      <div class="rc-button">
        <a class="btn btn-white" href="https://color.viewsonic.com/zh-tw/" data-toggle="modal">了解更多</a>
      </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-4 text-center">
      <img src="images/color-02.png" class="product img-responsive center-block"> <br>
      <h4>精準色彩 重現真實</h4>
      <small style="    display: flex;   justify-content: center;">
        <ul style="text-align: left;padding-left: 5vh;">
          <li>Delta E < 2準確度</li>
          <li>100% sRGB 真實色彩</li>
               </ul>
      </small>
      <div class="rc-button">
        <a class="btn btn-white" href="https://color.viewsonic.com/zh-tw/" data-toggle="modal">了解更多</a>
      </div>
    </div>   
    <div class="col-xs-12 col-sm-4 col-lg-4 text-center">
      <img src="images/color-03.png" class="product img-responsive center-block"> <br>
      <h4>無邊框設計 視野無界</h4>
      <small style="    display: flex;   justify-content: center;">
        <ul style="text-align: left;padding-left: 5vh;">
          <li>流暢組合多部顯示器</li>
          <li>專注創作內容</li>
               </ul>
      </small>
      <div class="rc-button">
        <a class="btn btn-white" href="https://color.viewsonic.com/zh-tw/" data-toggle="modal">了解更多</a>
      </div>
    </div>
    <div class="clearfix"></div>    
  </div>
  </div>
<!-- ColorPro end -->




 <!-- 繪圖板 -->
 <div class="row vs-container rc-feature ">
  <div class="pane-main kv-bg keyspec">
    <h2 class="text-center" id="woodpad">ViewSonic竹質繪圖板</h2><br>
    <div class="col-xs-12 col-sm-4 col-lg-4 text-center">
      <img src="images/pen-01.png" class="product img-responsive center-block"> <br>
      <h4>專業繪圖筆 流暢繪圖</h4>
      <small style="    display: flex;   justify-content: center;">
        <ul style="text-align: left;padding-left: 5vh;">
          <li>4096感壓</li>
          <li>+/-60度傾斜感擬真筆刷</li>
               </ul>
      </small>
      <div class="rc-button">
        <a class="btn btn-white" href="https://pendisplay.viewsonic.com/pages/drawingtablet-woodpad10" data-toggle="modal">了解更多</a>
      </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-4 text-center">
      <img src="images/pen-02.png" class="product img-responsive center-block"> <br>
      <h4>極致輕博 外出方便</h4>
      <small style="    display: flex;   justify-content: center;">
        <ul style="text-align: left;padding-left: 5vh;">
          <li>274g輕量設計</li>
          <li>隨身攜帶</li>
               </ul>
      </small>
      <div class="rc-button">
        <a class="btn btn-white" href="https://pendisplay.viewsonic.com/pages/drawingtablet-woodpad10" data-toggle="modal">了解更多</a>
      </div>
    </div>   
    <div class="col-xs-12 col-sm-4 col-lg-4 text-center">
      <img src="images/pen-03.png" class="product img-responsive center-block"> <br>
      <h4>隨插即用 留住靈感</h4>
      <small style="    display: flex;   justify-content: center;">
        <ul style="text-align: left;padding-left: 5vh;">
          <li>軟體兼容性強</li>
          <li>隨處即可創作</li>
               </ul>
      </small>
      <div class="rc-button">
        <a class="btn btn-white" href="https://pendisplay.viewsonic.com/pages/drawingtablet-woodpad10" data-toggle="modal">了解更多</a>
      </div>
    </div>
    <div class="clearfix"></div>    
  </div>
  </div>
<!-- 繪圖板 end -->






<div class="row vs-container rc-feature " id="painter">
                    <div class="col-lg-8 col-lg-offset-2  scrollme animateme" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   
                    
                    </div>
                    <div class="center-block" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100">
                    <img src="images/VP3881_USB-type-C.png" class="img-responsive center-block" style="padding:1em">
                    </div>
                </div>

   

   

<!-- tabs panes: end -->



<!-- <div class="row container rc-feature vs-vcenter">
               <div class="col-lg-5 col-lg-offset-1 scrollme animateme col-lg-push-5" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <img src="images/Report.jpg" class="img-responsive center-block" style="max-width: 60%">
                   </div>
                   <div class="col-lg-4 col-lg-offset-1 scrollme animateme col-lg-pull-5" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1;transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <h2><b>開箱即用的完美色彩</b></h2>
                   <p>每台顯示器在出廠時都已預先校色，並有自己的色彩校準報告。 每個報告提供面板的Adobe RGB、DCI-P3、sRGB、EBU、SMPTE-C、REC709和均勻度數據，與一般的色彩報告相比，多了10種的不同項目數據。此外，在VP系列顯示器準備就緒以前，我們在微調所花的時間是競爭對手的四倍，就是為了給消費者帶來了最高品質的色彩校準。</p>
                       
                   </div>
                   
               </div> -->










  <!-- series tabs: begin -->

  <div id="productDetail">

    <h3 class="text-center opensans"><br><big>產品一覽</big></h3><big>

    <div class="vs-container pane-inner">

      <div class="inner-detail">

            <div class="col-md-4 col-xs-12 product-item compact" data-model="VP3268-4K">
              <a href="/products/lcd/VP3268-4K.php">
                <strong class="tag">32“</strong>
                <img src="images/products/VP3268-4K.png" class="img-responsive product-photo">
                <strong>VP3268-4K</strong></a>
                <h4>創意工作者必備</h4>
                <ul>
                <li>Ultra HD 3840 x 2160</li>
                <li>電源線<br>
                    DP線(mini DP to DP)<br>
                    HDMI 2.0線<br>
                    USB 3.0 UP Stream線(Type B to A)<br>
                    Audio線(3.5mm audio male to male)<br></li>
                <li>4K Ultra HD 解析度<br>
                    支援 HDR10<br>
                    無邊框設計<br>
                    Delta E＜2、100% sRGB<br>
                    硬體校色功能<br>
                    獨家Uniformity均勻度校調技術<br>
                    人體工學設計、畫面自動旋轉功能</li></ul>

                   <div class="rc-button">
        <a class="producthover btn btn-white " href="https://pendisplay.viewsonic.com/pages/drawingtablet-woodpad10" data-toggle="modal">了解更多</a>
      </div>
            </div>           
            



            <div class="col-md-4 col-xs-12 product-item compact" data-model="VP2785-2K">
              <a href="/products/lcd/VP2785-2K.php">
                <strong class="tag">27“</strong>
                <img src="images/products/VP2785-2K.png" class="img-responsive product-photo">
              <strong>VP2785-2K</strong></a>
               <h4>德國評測肯定</h4>
              <ul>
                 <li>WQHD 2560 x 1440</li>
                 <li>電源線<br>
                     DisplayPort 線 (v1.2; Male-Male) <br>
                     USB Type-C 線 (Male-Male)  <br>
                     AC/DC Adapter</li>
                 <li>WQHD 1440p 2K解析度<br>
                     IPS 178 度廣視角面板<br>
                     100% Adobe RGB<br>
                     DCI-P3 96% 色域<br>
                     Delta E &lt; 2 精準色準<br>
                     KVM 切換技術搭載 USB Type-C<br>
                     支援 Daisy Chain 串接的多元連結<br>
                     符合人體工學設計的自動旋轉功能
                       </li>
      </ul>
      <div class="rc-button">
        <a class="producthover btn btn-white " href="https://pendisplay.viewsonic.com/pages/drawingtablet-woodpad10" data-toggle="modal">了解更多</a>
      </div>
            </div>            
            
            
            
            <div class="col-md-4 col-xs-12 product-item compact" data-model="VP2768">
              <a href="/products/lcd/VP2768.php">
                <strong class="tag">27“</strong>
                <img src="images/products/VP2768.png" class="img-responsive product-photo">
              <strong>VP2768</strong></a>
               <h4>Declutter Your Desk</h4>
              <ul>
                 <li>WQHD 2560 x 1440</li>
                 <li>電源線<br>
                     DP 線 (mini DP to DP) <br>
                     USB 3.0 UP Stream 線 (Type B to A) </li>
                 <li>硬體校色功能 <br>
                     無邊框設計 <br>
                     Delta E &lt; 2、100% sRGB <br>
                     人體工學設計、畫面自動旋轉功能 <br>
                     獨家Uniformity均勻度校調技術</li></ul>

    <div class="rc-button">
        <a class="producthover btn btn-white" href="https://pendisplay.viewsonic.com/pages/drawingtablet-woodpad10" data-toggle="modal">了解更多</a>
      </div>
            </div>            
            
            
            <div class="col-md-4 col-xs-12 product-item compact" data-model="VP2458">
              <a href="/products/lcd/VP2458.php">
                <strong class="tag">24“</strong>
                <img src="images/products/VP2458.jpg" class="img-responsive product-photo">
                <strong>VP2458</strong></a>
               <h4>More Ways to Multitask</h4>
              <ul>
                <li>Full HD 1920 x 1080</li>
                <li>電源線<br>
                    DP線(DisplayPort to DisplayPort)<br>
                    USB3.1 Up Stream線</li>
                <li>Delta E＜2、100% sRGB<br>
                    支援硬體校色<br>
                    自動樞軸旋轉功能的人體工學設計<br>
                    無邊框設計<br>
                    抗藍光、零閃屏<br>
                    組裝方式輕鬆且簡單</li>
             </ul>
<div class="rc-button">
        <a class="producthover btn btn-white" href="https://pendisplay.viewsonic.com/pages/drawingtablet-woodpad10" data-toggle="modal">了解更多</a>
      </div>
            </div>



            <div class="col-md-4 col-xs-12 product-item compact" data-model="WoodPad">
              <a href="/products/lcd/VP2458.php">
                <strong class="tag">24“</strong>
                <img src="images/products/WoodPad.png" class="img-responsive product-photo">
                <strong>WoodPad</strong></a>
               <h4>More Ways to Multitask</h4>
              <ul>
                <li>Full HD 1920 x 1080</li>
                <li>電源線<br>
                    DP線(DisplayPort to DisplayPort)<br>
                    USB3.1 Up Stream線</li>
                <li>Delta E＜2、100% sRGB<br>
                    支援硬體校色<br>
                    自動樞軸旋轉功能的人體工學設計<br>
                    無邊框設計<br>
                    抗藍光、零閃屏<br>
                    組裝方式輕鬆且簡單</li>
             </ul>
<div class="rc-button">
        <a class="producthover btn btn-white" href="https://pendisplay.viewsonic.com/pages/drawingtablet-woodpad10" data-toggle="modal">了解更多</a>
      </div>
            </div>










        <div class="clearfix"></div>

      </div>

    </div>

  </big></div>

  


  </div>






  </div>


</div>




<!-- page content: end -->


<?php

siteLocationMobile();
siteFooterMobile(
    '<script src="'.$GLOBALS['gCountryBaseURL'].'/w20/vendor/wow.min.js"></script>'."\n".
    '<script src="js/landing.js"></script>'."\n".
    '<script src="js/country.js"></script>'
);

?>