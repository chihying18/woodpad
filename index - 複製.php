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

        <a><a href="#productDetail" class="btn btn-red opensans">立即購買</a></p>
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

                <div class="row container rc-feature vs-vcenter">
                <div class="col-lg-5 col-lg-offset-1 scrollme animateme" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <img src="images/vpwp02.png" class="img-responsive center-block" style="max-width: 60%">
                   </div> 
                   <div class="col-lg-4 col-lg-offset-1 scrollme animateme col-lg-pull-1" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1;transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <h2><b>直立/橫向兩種創作模式 </b></h2>
                   <p><b>90度/180度螢幕與繪圖板</b></p>
                        <p>ViewSonic提供完整方案，螢幕與繪圖板相互搭配，不論橫向或是直立作品，都能以最直覺的方式創作。直立創作方案，更能符合數位新世代創作者需求。</p>
                   </div>
                  
               </div>

               <div class="clearfix"></div>

               <div class="row container rc-feature vs-vcenter">
               <div class="col-lg-5 col-lg-offset-1 scrollme animateme col-lg-push-5" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <img src="images/4096.png" class="img-responsive center-block" style="max-width: 60%">
                   </div>
                   <div class="col-lg-4 col-lg-offset-1 scrollme animateme col-lg-pull-5" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1;transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <h2><b>專業繪圖筆</b></h2>
                   <p><b>4096感壓 / +/-60度傾斜角</b></p>
                        <p>ViewSonic竹質繪圖板擁有4096專業感壓與+/-60度傾斜角功能，輕鬆呈現細節，符合專業創作者需求。</p>
                   </div>
                   
               </div>
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

                <div class="row container rc-feature vs-vcenter">
                <div class="col-lg-5 col-lg-offset-1 scrollme animateme" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <img src="images/VP2458-sRGB-Panel-std.png" class="img-responsive center-block" style="max-width: 60%">
                   </div> 
                   <div class="col-lg-4 col-lg-offset-1 scrollme animateme col-lg-pull-1" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1;transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <h2><b>100% sRGB</b></h2>
                   <p><b>呈現豐富逼真的色彩</b></p>
                       
                   </div>
                  
               </div>

               <div class="clearfix"></div>

               <div class="row container rc-feature vs-vcenter">
               <div class="col-lg-5 col-lg-offset-1 scrollme animateme col-lg-push-5" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <img src="images/VP2458-Delta-E2-std-1124.jpg" class="img-responsive center-block" style="max-width: 90%">
                   </div>
                   <div class="col-lg-4 col-lg-offset-1 scrollme animateme col-lg-pull-5" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1;transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <h2><b>Delta E < 2 </b></h2>
                   <p><b>令人驚豔的色彩重現效果</b></p>
                   </div>
                   
               </div>
               <div class="clearfix"></div>

               <div class="row container rc-feature vs-vcenter">
                <div class="col-lg-5 col-lg-offset-1 scrollme animateme" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <img src="images/vpwp02.png" class="img-responsive center-block" style="max-width: 60%">
                   </div> 
                   <div class="col-lg-4 col-lg-offset-1 scrollme animateme col-lg-pull-1" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1;transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <h2><b>隨意旋轉</b></h2>
                   <p><b>符合直覺的創作模式</b></p>
                       
                   </div>
                  
               </div> </div>
<!-- designer end -->



<!--photographer -->

<div class="row vs-container rc-feature " id="photographer">
                    <div class="col-lg-8 col-lg-offset-2  scrollme animateme" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                    <h2>Photographer</h2>
                    
                    
                    <h3><b>符合數位新世代創作者需求</b></h3>
                    <p>新世代創作者需要面對，讀者使用的閱讀工具為手機或是平板，作品呈現隨著時代改變，例如時下最流行的條漫。直立式螢幕與直立式繪圖板，畫家創作時更容易一覽整體畫面與故事線。</p>
                    <h3><b>專業繪圖工具</h3></b>
                    <p>漫畫需要細膩的筆觸呈現，ViewSonic繪圖筆提供專業規格，呈現細部線條。VP系列螢幕，完美呈現每個小細節。</p>
                    
                    </div>
                    <div class="center-block" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100">
                    <img src="images/vpwp-01.jpg" class="img-responsive center-block" style="padding:3em">
                    </div>
                </div>


                <div class="clearfix"></div>

                <div class="row container rc-feature vs-vcenter">
                <div class="col-lg-5 col-lg-offset-1 scrollme animateme" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <img src="images/vpwp02.png" class="img-responsive center-block" style="max-width: 60%">
                   </div> 
                   <div class="col-lg-4 col-lg-offset-1 scrollme animateme col-lg-pull-1" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1;transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <h2><b>直立/橫向兩種創作模式 </b></h2>
                   <p><b>90度/180度螢幕與繪圖板</b></p>
                        <p>ViewSonic提供完整方案，螢幕與繪圖板相互搭配，不論橫向或是直立作品，都能以最直覺的方式創作。直立創作方案，更能符合數位新世代創作者需求。</p>
                   </div>
                  
               </div>

               <div class="clearfix"></div>

               <div class="row container rc-feature vs-vcenter">
               <div class="col-lg-5 col-lg-offset-1 scrollme animateme col-lg-push-5" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <img src="images/4096.png" class="img-responsive center-block" style="max-width: 60%">
                   </div>
                   <div class="col-lg-4 col-lg-offset-1 scrollme animateme col-lg-pull-5" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1;transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <h2><b>專業繪圖筆</b></h2>
                   <p><b>4096感壓 / +/-60度傾斜角</b></p>
                        <p>ViewSonic竹質繪圖板擁有4096專業感壓與+/-60度傾斜角功能，輕鬆呈現細節，符合專業創作者需求。</p>
                   </div>
                   
               </div>
<!-- photographer end -->



<!-- RC Icon-->
<div class="row vp-icon" style="max-width: 980px;margin: auto;text-align: center;">
	<a class="col-sm-2 col-xs-4" href="/zh-tw/products/VP3268-4K.php#feature5"><img src="images/Auto Pivot.png"><h4>自動旋轉設計 兼顧細節</h4></a>
	<a class="col-sm-2 col-xs-4" href="/zh-tw/explore/content/Color_Calibration_is_Essential_for_Creating_Digital_Imagery_14.html"><img src="images/Color Accuracy.png"><h4>精準色彩 重現真實</h4></a>
	<a class="col-sm-2 col-xs-4" href="/zh-tw/explore/content/Pivot-Screen-Monitors-See-the-Bigger-Picture_15.html"><img src="images/Frameless.png"><h4>無邊框設計 視野無界</h4></a>
	<a class="col-sm-2 col-xs-4" href="/zh-tw/explore/content/DeltaE≦2Color-Accuracy_2.html"><img src="images/Frameless.png"><h4>精準色彩</h4></a>
	<a class="col-sm-2 col-xs-4" href="/zh-tw/explore/content/Thin-Bezel-Monitors-More-Than-Just-Good-Looking_24.html"><img src="images/Frameless.png" ><h4>無邊框設計</h4></a>
	<a class="col-sm-2 col-xs-4" href="/zh-tw/explore/content/KVM_Switch_with_USB_Type-C_for_Efficient_Multi-tasking_27.html"><img src="images/Frameless.png" ><h4>Type-C</h4></a>
  </div>
  <!-- RC Icon end-->

             

<!-- designer end -->

<div class="row vs-container rc-feature " id="painter">
                    <div class="col-lg-8 col-lg-offset-2  scrollme animateme" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   
                    
                    </div>
                    <div class="center-block" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100">
                    <img src="images/VP3881_USB-type-C.png" class="img-responsive center-block" style="padding:1em">
                    </div>
                </div>

   

   

<!-- tabs panes: end -->



<div class="row container rc-feature vs-vcenter">
               <div class="col-lg-5 col-lg-offset-1 scrollme animateme col-lg-push-5" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="-100" style="opacity: 1; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <img src="images/Report.jpg" class="img-responsive center-block" style="max-width: 60%">
                   </div>
                   <div class="col-lg-4 col-lg-offset-1 scrollme animateme col-lg-pull-5" data-when="enter" data-from="0.5" data-to="0" data-crop="true" data-opacity="0" data-scale="1" data-translatey="200" style="opacity: 1;transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale3d(1, 1, 1);">
                   <h2><b>開箱即用的完美色彩</b></h2>
                   <p>每台顯示器在出廠時都已預先校色，並有自己的色彩校準報告。 每個報告提供面板的Adobe RGB、DCI-P3、sRGB、EBU、SMPTE-C、REC709和均勻度數據，與一般的色彩報告相比，多了10種的不同項目數據。此外，在VP系列顯示器準備就緒以前，我們在微調所花的時間是競爭對手的四倍，就是為了給消費者帶來了最高品質的色彩校準。</p>
                       
                   </div>
                   
               </div>










  <!-- series tabs: begin -->

  <div id="productDetail">

    <h3 class="text-center opensans"><br><big><b>Available in <?=count($products);?> Choices</b></big></h3>

    <div class="vs-container pane-inner">

      <div class="inner-detail">

<?php

foreach ($products as $model => $spec) {
  include('templates/model-v3.inc.php');
}

?>

        <div class="clearfix"></div>

      </div>

    </div>

  </div>

  


  </div>






  </div>


</div>


<!-- Modal Video-->
      <div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <div class="vs-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></div>
              <!--h3 class="modal-title" id="myModalLabel">Video</h3-->
            </div>
            <div class="modal-body iframe_height">
              <iframe width="100%" height="100%" src="" frameborder="0" allowfullscreen></iframe>
            </div>
          </div>
        </div>
      </div>
<!-- Modal Video-->

<!-- page content: end -->


<?php

siteLocationMobile();
siteFooterMobile(
    '<script src="'.$GLOBALS['gCountryBaseURL'].'/w20/vendor/wow.min.js"></script>'."\n".
    '<script src="js/landing.js"></script>'."\n".
    '<script src="js/country.js"></script>'
);

?>