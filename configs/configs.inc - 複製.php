<?php

$useConfigBullets = true;
$useProductBuyLinks = true;
$productLine = 'LCD Display';

$products = [


  "VP2458" => [
    'size' => 24,
    'modelName' => 'VP2458',
    'modelTitle' => '創意工作者必備',
    'bullet-point' => [
        '24”W (23.8” Viewable)',
        'Full HD 1920 x 1080',
        'HDMI 1.4 x1<br>DisplayPort 1.2 x1<br>VGA x1<br>USB3.1 Type A x2<br>&nbsp;',
        '100% sRGB 色域覆蓋率<br>Colorbration+  軟體<br>支援硬體校色',
    ], 
    'moreLink' => 'VP2458.php',
    'buyLink' => [
        // 'Amazon' => 'https://www.amazon.co.uk/ViewSonic-VP2458-Viewsonic-Monitor/dp/B07JR9R5YS/ref=sr_1_1?keywords=VP2458&qid=1554733889&s=gateway&sr=8-1'
        //Amazon
        //'eu' => 'https://www.amazon.co.uk',
        //'de' => 'https://www.amazon.de'
    ],
    'image' => 'VP2458.jpg',
  ],

  "VG2448" => [
    'size' => 24,
    'modelName' => 'VG2448',
    'modelTitle' => '德國評測肯定',
    'bullet-point' => [
        '24”W (23.8” Viewable)',
        'Full HD 1920 x 1080',
        'HDMI 1.4 x1<br>DisplayPort 1.2 x1<br>VGA x1<br>USB3.0 Type A x4<br>&nbsp;',
        '92% sRGB 色域覆蓋率<br>&nbsp;<br>&nbsp;',
    ],
    'moreLink' => 'VG2448.php',
    'buyLink' => [
        // 'Amazon' => 'https://www.amazon.co.uk/ViewSonic-VG2448-SuperClear-Ergonomics-DisplayPort/dp/B0789CDW8Z/ref=sr_1_1?keywords=vg2448&qid=1554801788&s=gateway&sr=8-1'
        //Amazon
        //'eu' => 'https://www.amazon.co.uk',
        //'de' => 'https://www.amazon.de'
    ],
    'image' => 'VG2448.jpg',
  ],

  "VG2455" => [
    'size' => 24,
    'modelName' => 'VG2455',
    'modelTitle' => 'Declutter Your Desk',
    'bullet-point' => [
        '24”W (23.8” Viewable)',
        'Full HD 1920 x 1080',
        'HDMI 1.4 x1<br>DisplayPort 1.2 x1<br>VGA x1<br>USB3.1 Type A x3<br>USB3.1 Type C 60W x1',
        'vDisplay Manager Software<br>&nbsp;<br>&nbsp;',
    ],
    'moreLink' => 'VG2455.php',
    'buyLink' => [
        // 'Amazon' => 'https://www.amazon.co.uk/ViewSonic-VG2455/dp/B07JVKS8JQ/ref=sr_1_1?keywords=VG2455&qid=1554733849&s=gateway&sr=8-1'
        //Amazon
        //'eu' => 'https://www.amazon.co.uk',
        //'de' => 'https://www.amazon.de'
    ],
    'image' => 'VG2455.jpg',
  ],

  "VG2755" => [
    'size' => 27,
    'modelName' => 'VG2755',
    'modelTitle' => 'More Ways to Multitask',
    'bullet-point' => [
        '27”W (27” Viewable)',
        'Full HD 1920 x 1080',
        'HDMI 1.4 x1<br>DisplayPort 1.2 x1<br>VGA x1<br>USB3.1 Type A x3<br>USB3.1 Type C 60W x1',
        'vDisplay Manager Software<br>&nbsp;<br>&nbsp;',
    ],
    'moreLink' => 'VG2755.php',
    'buyLink' => [
        // 'Amazon' => 'https://www.amazon.co.uk/ViewSonic-VG2755-Advanced-Ergonomics-DisplayPort/dp/B07JV8SVPF/ref=sr_1_3?keywords=VG2755&qid=1554733875&s=gateway&sr=8-3'
        //Amazon
        //'eu' => 'https://www.amazon.co.uk',
        //'de' => 'https://www.amazon.de'
    ],
    'image' => 'VG2755-2K.jpg',
  ],
  
  "VG2755-2K" => [
    'size' => 27,
    'modelName' => 'VG2755-2K',
    'modelTitle' => '雙螢幕工作首選',
    'bullet-point' => [
        '27”W (27” Viewable)',
        'WQHD 2560 x 1440',
        'HDMI 1.4 x1<br>DisplayPort 1.2 x1<br>USB3.1 Type A x3<br>USB3.1 Type C 60W x1<br>&nbsp;',
        '100% sRGB 色域覆蓋率<br>獨家vDisplay Manager Software畫面管理軟體<br>&nbsp;',
    ],
    'moreLink' => 'VG2755-2K.php',
    'buyLink' => [
        // 'Amazon' => 'https://www.amazon.co.uk/ViewSonic-VG2755-2K-Advanced-Ergonomics-DisplayPort/dp/B07L55CTT1/ref=sr_1_2?keywords=VG2755&qid=1554733875&s=gateway&sr=8-2'
        //Amazon
        //'eu' => 'https://www.amazon.co.uk',
        //'de' => 'https://www.amazon.de'
    ],
    'image' => 'VG2755-2K.jpg',
  ],
];

$series = [
    'VG33' => [
        'title' => 'VG33<br><small>Series</small>',
        'models' => [ 'VG2233-LED', 'VG2233MH', 'VG2437SMC', 'VG2719-2K' ],
        'image' => 'series-vg33.png',
        'scene' => 'VG33-banner.jpg',
        'domId' => 'tab-vg33'
    ],

    'VG39' => [
        'title' => 'VG39<br><small>Series</small>',
        'models' => [ 'VG2239SMH-2', 'VG2439SMH-2', 'VG2739' ],
        'image' => 'series-vg39.png',
        'scene' => 'VG39-banner.jpg',
        'domId' => 'tab-vg39'
    ],

    'VG48' => [
        'title' => 'VG48<br><small>Series</small>',
        'models' => [ 'VG2448', 'VG2748' ],
        'image' => 'series-vg48.png',
        'scene' => 'VG48-banner.jpg',
        'domId' => 'tab-vg48'
    ],
    'VG55' => [
        'title' => 'VG55<br><small>Series</small>',
        'models' => [ 'VG2455', 'VG2755', 'VG2755-2K' ],
        'image' => 'series-vg55.png',
        'scene' => 'VG55-banner.jpg',
        'domId' => 'tab-vg55'
    ],
    'VP58/68' => [
        'title' => 'VG58/68<br><small>Series</small>',
        'models' => [ 'VP2468', 'VP2768', 'VP2768-4K', 'VP2758' ],
        'image' => 'series-vp68.png',
        'scene' => 'VP68-banner.jpg',
        'domId' => 'tab-vp5868'
    ],
    'VP2785-4K' => [
        'title' => 'VP2785-4K',
        'models' => [ 'VP2785-4K' ],
        'image' => 'series-vp2785-4K.png',
        'scene' => 'VP2785-4K-banner.jpg',
        'domId' => 'tab-vp2785'
    ],
    'VP3881' => [
        'title' => 'VP3881',
        'models' => [ 'VP3881' ],
        'image' => 'series-vp3881.png',
        'scene' => 'VP3881-banner.jpg',
        'domId' => 'tab-vp3881'
    ],
];

$filters = [
    'USB-C' => [ 'VG2455', 'VG2755', 'VG2755-2K', 'VP2785-4K', 'VP3881' ],
    'sRGB' => [ 'VG2719-2K', 'VG2755-2K', 'VP2458', 'VP2768', 'VP2768-4K', 'VP3268-4K', 'VP2785-4K', 'VP3881' ],
    'Hardware Calibration' => [ 'VP2458', 'VP2768', 'VP2768-4K', 'VP3268-4K', 'VP2785-4K', 'VP3881' ],
    'Recyclable Packaging' => [ 'VG2448', 'VG2748', 'VG2455', 'VG2755', 'VG2755-2K', 'VP2458' ],
];

$locs = array(
    'lab-projection' => 'Projection System',
    //'lab-resolution' => 'Native Resolution',
    //'lab-brightness' => 'Brightness',
    //'lab-ratio' => 'Contrast Ratio With SuperEco Mode',
    //'lab-displayColour' => 'Display Colour',
    //'lab-imageSize' => 'Image Size',
    //'lab-throwDistance' => 'Throw Distance',
    //'lab-keystone' => 'Keystone',
    //'lab-zoom' => 'Optical Zoom',
    //'lab-recommended' => 'Recommended for',
    'btn-moreinfo' => 'Learn more',
    'btn-buynow' => 'Buy now',
    'txt-Business Class UPGRADE' => 'Business Class UPGRADE',
    'txt-Model' => 'Model',
    //'lab-spidergraph' => 'Spider Graph',
    //'txt-Compare Models' => 'Compare Models',
);

$lstr = array_merge($lstr, $locs);

?>









