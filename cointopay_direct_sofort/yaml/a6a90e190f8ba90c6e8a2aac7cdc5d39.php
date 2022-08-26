<?php return array (
  'name' => 'warehouse',
  'display_name' => 'Warehouse',
  'version' => '4.5.2',
  'author' => 
  array (
    'name' => 'IQIT COMMERCE',
    'email' => 'support@iqit-commerce.com',
    'url' => 'http://www.iqit-commerce.com',
  ),
  'meta' => 
  array (
    'compatibility' => 
    array (
      'from' => '1.7.8.0',
      'to' => NULL,
    ),
    'available_layouts' => 
    array (
      'layout-full-width' => 
      array (
        'name' => 'Full Width',
        'description' => 'No side columns, ideal for distraction-free pages such as product pages.',
      ),
      'layout-both-columns' => 
      array (
        'name' => 'Three Columns',
        'description' => 'One large central column and 2 side columns.',
      ),
      'layout-left-column' => 
      array (
        'name' => 'Two Columns, small left column',
        'description' => 'Two columns with a small left column',
      ),
      'layout-right-column' => 
      array (
        'name' => 'Two Columns, small right column',
        'description' => 'Two columns with a small right column',
      ),
    ),
  ),
  'global_settings' => 
  array (
    'configuration' => 
    array (
      'PS_IMAGE_QUALITY' => 'png',
      'PS_QUICK_VIEW' => true,
      'PS_BLOCK_CART_AJAX' => true,
      'PS_PRODUCTS_PER_PAGE' => 24,
    ),
    'modules' => 
    array (
      'to_enable' => 
      array (
        0 => 'revsliderprestashop',
        1 => 'iqitadditionaltabs',
        2 => 'iqithtmlandbanners',
        3 => 'iqitcompare',
        4 => 'iqitcontactpage',
        5 => 'iqitcookielaw',
        6 => 'iqitcrossselling',
        7 => 'iqitcountdown',
        8 => 'iqitelementor',
        9 => 'iqitemailsubscriptionconf',
        10 => 'iqitaddthisplugin',
        11 => 'iqitfreedeliverycount',
        12 => 'iqitlinksmanager',
        13 => 'iqitmegamenu',
        14 => 'iqitpopup',
        15 => 'iqitproductsnav',
        16 => 'iqitproducttags',
        17 => 'iqitreviews',
        18 => 'iqitsearch',
        19 => 'iqitsizecharts',
        20 => 'iqitthemeeditor',
        21 => 'iqitwishlist',
        22 => 'iqitextendedproduct',
        23 => 'ps_emailsubscription',
        24 => 'ph_simpleblog',
        25 => 'ph_blog_column_custom',
        26 => 'ph_relatedposts',
        27 => 'iqitdashboardnews',
        28 => 'iqitsociallogin',
      ),
      'to_disable' => 
      array (
        0 => 'ps_linklist',
        1 => 'ps_mainmenu',
        2 => 'ps_categorytree',
        3 => 'ps_featuredproducts',
        4 => 'ps_searchbar',
        5 => 'ps_imageslider',
        6 => 'ps_customtext',
        7 => 'blockreassurance',
        8 => 'ps_banner',
        9 => 'ps_contactinfo',
        10 => 'ps_socialfollow',
        11 => 'blockwishlist',
      ),
    ),
    'hooks' => 
    array (
      'modules_to_hook' => 
      array (
        'displayNav1' => 
        array (
          0 => 'iqitlinksmanager',
        ),
        'displayNav2' => 
        array (
          0 => 'ps_languageselector',
          1 => 'ps_currencyselector',
          2 => 'iqitwishlist',
          3 => 'iqitcompare',
        ),
        'displayTopColumn' => 
        array (
          0 => 'revsliderprestashop',
        ),
        'displayHome' => 
        array (
          0 => 'revsliderprestashop',
          1 => 'iqitelementor',
          2 => NULL,
        ),
        'displayFooter' => 
        array (
          0 => 'iqitlinksmanager',
          1 => 'iqitcontactpage',
        ),
        'displayLeftColumn' => 
        array (
          0 => 'ps_categorytree',
          1 => 'ps_facetedsearch',
        ),
        'displayProductAdditionalInfo' => 
        array (
          0 => 'ps_sharebuttons',
          1 => 'iqitproducttags',
          2 => NULL,
        ),
        'displayAfterProductAddCartBtn' => 
        array (
          0 => 'iqitwishlist',
          1 => 'iqitcompare',
        ),
        'displayCustomerAccount' => 
        array (
          0 => 'iqitwishlist',
          1 => NULL,
        ),
        'displayProductListFunctionalButtons' => 
        array (
          0 => 'iqitwishlist',
          1 => 'iqitcompare',
        ),
        'displayBeforeBodyClosingTag' => 
        array (
          0 => 'iqitwishlist',
          1 => 'iqitcompare',
          2 => 'iqitpopup',
          3 => 'iqitcookielaw',
          4 => NULL,
        ),
        'displayRightColumnProduct' => 
        array (
          0 => 'iqithtmlandbanners',
        ),
        'displayReassurance' => 
        array (
          0 => 'iqitaddthisplugin',
        ),
        'displayCustomerLoginFormAfter' => 
        array (
          0 => 'iqitsociallogin',
        ),
        'displayRegistrationBeforeForm' => 
        array (
          0 => 'iqitsociallogin',
        ),
        'displayFooterBefore' => NULL,
        'displayCheckoutLoginFormAfter' => 
        array (
          0 => 'iqitsociallogin',
        ),
        'displayBackOfficeHeader' => 
        array (
          0 => NULL,
          1 => 'iqitelementor',
        ),
        'displayCMSDisputeInformation' => 
        array (
          0 => 'iqitelementor',
        ),
        'displayBlogElementor' => 
        array (
          0 => 'iqitelementor',
        ),
        'displayProductElementor' => 
        array (
          0 => 'iqitelementor',
        ),
        'displayCategoryElementor' => 
        array (
          0 => 'iqitelementor',
        ),
        'displayManufacturerElementor' => 
        array (
          0 => 'iqitelementor',
        ),
        'displayProductRating' => 
        array (
          0 => 'productcomments',
        ),
      ),
    ),
    'image_types' => 
    array (
      'cart_default' => 
      array (
        'width' => 125,
        'height' => 162,
        'scope' => 
        array (
          0 => 'products',
        ),
      ),
      'small_default' => 
      array (
        'width' => 98,
        'height' => 127,
        'scope' => 
        array (
          0 => 'products',
          1 => 'categories',
          2 => 'manufacturers',
          3 => 'suppliers',
        ),
      ),
      'medium_default' => 
      array (
        'width' => 452,
        'height' => 584,
        'scope' => 
        array (
          0 => 'products',
          1 => 'categories',
          2 => 'manufacturers',
          3 => 'suppliers',
        ),
      ),
      'home_default' => 
      array (
        'width' => 236,
        'height' => 305,
        'scope' => 
        array (
          0 => 'products',
        ),
      ),
      'large_default' => 
      array (
        'width' => 381,
        'height' => 492,
        'scope' => 
        array (
          0 => 'products',
          1 => 'manufacturers',
          2 => 'suppliers',
        ),
      ),
      'category_default' => 
      array (
        'width' => 1003,
        'height' => 200,
        'scope' => 
        array (
          0 => 'categories',
        ),
      ),
      'stores_default' => 
      array (
        'width' => 170,
        'height' => 115,
        'scope' => 
        array (
          0 => 'stores',
        ),
      ),
      'thickbox_default' => 
      array (
        'width' => 1100,
        'height' => 1422,
        'scope' => 
        array (
          0 => 'products',
        ),
      ),
    ),
  ),
  'theme_settings' => 
  array (
    'default_layout' => 'layout-full-width',
    'layouts' => 
    array (
      'category' => 'layout-left-column',
      'best-sales' => 'layout-left-column',
      'new-products' => 'layout-left-column',
      'prices-drop' => 'layout-left-column',
    ),
  ),
  'dependencies' => 
  array (
    'modules' => 
    array (
      0 => 'iqitadditionaltabs',
      1 => 'iqitaddthisplugin',
      2 => 'iqitcompare',
      3 => 'iqitcontactpage',
      4 => 'iqitcookielaw',
      5 => 'iqitcountdown',
      6 => 'iqitcrossselling',
      7 => 'iqitelementor',
      8 => 'iqitemailsubscriptionconf',
      9 => 'iqitextendedproduct',
      10 => 'iqitfreedeliverycount',
      11 => 'iqitlinksmanager',
      12 => 'iqitmegamenu',
      13 => 'iqitpopup',
      14 => 'iqitproductsnav',
      15 => 'iqitproducttags',
      16 => 'iqitreviews',
      17 => 'iqitsearch',
      18 => 'iqitsizecharts',
      19 => 'iqitsociallogin',
      20 => 'iqithtmlandbanners',
      21 => 'iqitthemeeditor',
      22 => 'iqitwishlist',
      23 => 'revsliderprestashop',
      24 => 'ph_simpleblog',
      25 => 'ph_blog_column_custom',
      26 => 'ph_relatedposts',
      27 => 'iqitdashboardnews',
    ),
  ),
);
