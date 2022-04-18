<?php

/**
 * @file
 * Admin page callbacks for the integrate_vk module.
 */
 require_once "integrateVK.php";
 use \vk_api\IntegrateVK as IntegrateVK;

 require_once "adminFunctions.php";
 require_once "output.php";
 
 function integrate_vk_admin(){
    return drupal_get_form('form_integrate_vk');
 }



  function form_integrate_vk($form, &$form_state) {
    $IntegrateVK = getObjIntegrateVK();
    
    $categories = getListCategories();

    $form['wrapper'] = [
        '#markup' => "<div id='msg' name='output-msg'></div> 
                      <div id='progress-status'></div>" 
      ];

    $form['settings'] = array(
        '#type' => 'fieldset', 
        '#title' => t('Настройка'),
        '#collapsible' => TRUE, 
        '#collapsed' => FALSE,
    );
    $form['settings']['GROUP_ID'] = array(
        '#type' => 'textfield',
        '#title' => t('GROUP_ID'),
        '#default_value' => $IntegrateVK->api->getGroupId(),
        '#required' => TRUE,
        '#description' =>  t('Для получения GROUP_ID перейдите на страницу группы вк. В адресной сроке будет: https://vk.com/public212007863, где 212007863 - GROUP_ID ')
    );
    $form['settings']['USER_ID'] = array(
        '#type' => 'textfield',
        '#title' => t('USER_ID'),
        '#default_value' => $IntegrateVK->api->getUserId(),
        '#required' => TRUE,
        '#description' => t('Для получения USER_ID перейдите на страницу своего профиля в вк. В адресной строке будет: https://vk.com/id713894096, где 713894096 - USER_ID')
    );
    $form['settings']['ACCESS_TOKEN'] = array(
        '#type' => 'textarea',
        '#title' => t('ACCESS_TOKEN'),
        '#default_value' => $IntegrateVK->api->getAccessToken(),
        '#required' => TRUE, 
        '#description' => t('Для получения ACCESS_TOKEN откройте новую вкладку в браузере и введите в адресную строку такой запрос: <br>
        <a href="@request">https://oauth.vk.com/authorize?client_id=8111381&display=page&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos,offline&response_type=token&v=5.131</a>
        <br>
        В адресной строке будет URL https://oauth.vk.com/blank.html, а после # вы увидите дополнительные параметры — access_token, expires_in и user_id:
        <br>
        Где число 8111381 в запросе - API_ID приложения. <br>
        Для получения API_ID перейдите в <a href="@vk-app">регистрацию приложения вк</a>, создайте приложение с платформой: сайт. В настройках приложения будет указан 
        ID приложения.', array('@vk-app' => 'https://vk.com/editapp?act=create', '@request' => 'https://oauth.vk.com/authorize?client_id=8111381&display=page&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos,offline&response_type=token&v=5.131'))
    );
    $form['settings']['CATEGORY_ID'] = array(
      '#type' => 'select',
      '#title' => t('Категория товаров'),
      '#options' => $categories,
      '#default_value' => $IntegrateVK->api->getCategoryId()
     );   
    $form['settings']['submit'] = array(
        '#type' => 'button',
        '#value' => t('Save settings'),
        '#ajax' => array(
            'callback' => 'ajax_setting_submit',
            'wrapper' => 'settings-submit',
            'method' => 'replace',
            'effect' => 'fade'
        ), 
        '#id' => 'btn-configure-vk',
      );
      
      $form['actions'] = array(
        '#type' => 'fieldset', 
        '#title' => t('Actions'),
        '#collapsible' => TRUE, 
        '#collapsed' => FALSE,
      );
    
     $form['actions']['upload_products'] = array(
        '#type' => 'button',
        '#value' => t('Загрузить все товары в vk'),
        '#ajax' => array(
          'callback' => 'ajax_upload_products',
          'wrapper' => 'upload-all',
          'method' => 'prepend',
          'effect' => 'fade',
          'progress' => array(
              'type' => 'bar',
              'message' => t('Выгрузка авто'),
              'url' => url('import/integrate_vk/progress'),
              'interval' => 1000
          ),
      ),
      '#id' => 'btn-upload_products-vk',
    );
    
    $form['actions']['remove_products'] = array(
      '#type' => 'button',
      '#value' => t('Удалить все товары из vk'),
      '#ajax' => array(
        'callback' => 'ajax_remove_products',
        'wrapper' => 'remove_products',
        'method' => 'replace',
        'effect' => 'fade',
        'progress' => array(
            'type' => 'bar',
            'message' => t('Удаление авто'),
            'url' => url('import/integrate_vk/progress'),
            'interval' => 1000
        ),
    ),
    '#id' => 'btn-remove_products-vk',
   );

   $form['actions']['add_categories'] = array(
    '#type' => 'button',
    '#value' => t('Добавить подборки товаров в vk'),
    '#ajax' => array(
      'callback' => 'ajax_add_category',
      'wrapper' => 'add_categories',
      'method' => 'replace',
      'effect' => 'fade',
      'progress' => array(
        'type' => 'bar',
        'message' => t('Выполняется'),
        'url' => url('import/integrate_vk/progress'),
        'interval' => 1000
    ),
  ),
  '#id' => 'btn-add_categories-vk',
  );

  $form['actions']['upload_products_in_categories'] = array(
    '#type' => 'button',
    '#value' => t('Связать подборки товаров с товарами в vk'),
    '#ajax' => array(
      'callback' => 'ajax_upload_products_in_categories',
      'wrapper' => 'upload_products_in_categories',
      'method' => 'replace',
      'effect' => 'fade',
      'progress' => array(
        'type' => 'bar',
        'message' => t('Выполняется'),
        'url' => url('import/integrate_vk/progress'),
        'interval' => 1000
    ),
  ),
  '#id' => 'btn-add_products_in_categories-vk',
  );

  $form['actions']['del_categories'] = array(
    '#type' => 'button',
    '#value' => t('Удалить подборки товаров из vk'),
    '#ajax' => array(
      'callback' => 'ajax_del_category',
      'wrapper' => 'del_categories',
      'method' => 'replace',
      'effect' => 'fade',
      'progress' => array(
        'type' => 'bar',
        'message' => t('Выполняется'),
        'url' => url('import/integrate_vk/progress'),
        'interval' => 1000
    ),
  ),
  '#id' => 'btn-del_categories-vk',
  );

  $form['logs'] = array(
    '#type' => 'fieldset', 
    '#title' => t('Logs'),
    '#collapsible' => TRUE, 
    '#collapsed' => FALSE,
  );

  $form['logs']['output'] = array(
    '#markup' => '<div id="logs-output"></div>',
    '#attached' => array(
      'css' => array(
        drupal_get_path('module', 'integrate_vk') . '/integrate_vk.css',
      )
    ) 

  ); 
    return $form;
 }


 function ajax_add_category($form, &$form_state) {
    addFieldAlbumId($form);
    $res_addCategoriesProduct = uploadAllCategories();
    $commands = output($res_addCategoriesProduct, 'Добавить подборки товаров в vk');

    return array(
      '#type'=>'ajax',
      '#commands'=> $commands,
    );
 }

 function ajax_upload_products_in_categories($form, &$form_state) {
    addFieldInAlbum();
    $res_addCategoriesProduct = uploadAllProductsInCategories();
    $commands = output($res_addCategoriesProduct, 'Связать подборки товаров с товарами в vk');

    return array(
      '#type'=>'ajax',
      '#commands'=> $commands,
    );
 }
 
 function ajax_del_category($form, &$form_state) {
    $res_delCategoriesProduct = delAllCategories();
    $commands = output($res_delCategoriesProduct, 'Удалить подборки товаров из vk');

    return array(
      '#type'=>'ajax',
      '#commands'=> $commands,
    );
 }

 function ajax_remove_products($form, &$form_state){
    // удалить все товары тип car из вк группы
    $res_deleteAllProducts = deleteAllProducts();
    $commands = output($res_deleteAllProducts, 'Удалить все товары из vk');

    return array(
      '#type'=>'ajax',
      '#commands'=> $commands,
    );

 }

 function ajax_upload_products($form, &$form_state) {
    
    $commands = array();
    $nodes = getAllNodes();
    $count = count($nodes);

    $IntegrateVK = getObjIntegrateVK();

    $step=15; $i=0;
    //for ($i=0; $i<=$count; $i=$i+$step) {
      $res_uploadAllProducts = uploadRangeProducts($i, $step);
      $commands = output($res_uploadAllProducts, 'Загрузить все товары в vk');
    //  sleep(1);
    //}

    /* 
    $res_uploadAllProducts = uploadAllProducts();  
    $commands = output($res_uploadAllProducts, 'Загрузить все товары в vk');
    */
     return array(
        '#type'=>'ajax',
        '#commands'=> $commands,
      );
 }


 function ajax_setting_submit($form, &$form_state) {
     
     $GROUP_ID = trim($form_state['values']['GROUP_ID']);
     $USER_ID = trim($form_state['values']['USER_ID']);
     $ACCESS_TOKEN = trim($form_state['values']['ACCESS_TOKEN']);
     $CATEGORY_ID = trim($form_state['values']['CATEGORY_ID']);
     
     $IntegrateVK = new IntegrateVK($GROUP_ID, $USER_ID, $ACCESS_TOKEN, $CATEGORY_ID);

     // Настройка класса integrateVK
     variable_set("propertiesIntegrateVK", $IntegrateVK->api->getConfig());
     // Добавить поле item_id для интеграции с vk товарами
     addFiledItem_id($form);
     
     // show message  
     $commands = array();
     $commands[] = ajax_command_html("#msg", "<div class='messages status'>
     <div> Настройки сохранены </div>
     </div>");
    
     return array(
        '#type'=>'ajax',
        '#commands'=> $commands,
      );
   }


?>