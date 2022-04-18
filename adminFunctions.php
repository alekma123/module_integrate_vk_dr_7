<?php
/**
 * Functions for products
 *
 */


function getListCategories() {
    $IntegrateVK = getObjIntegrateVK();
    $response = $IntegrateVK->getCategories();
    $items = $response->res->response->items;
    $categories = [];
    $count = 0;
    foreach ($items as $item) {
        // $categories[$item->section->name][] = [$item->id => $item->name];        
        $categories[$item->section->name][$item->id] = $item->name;        
    }
    return $categories; 
}

function deleteAllProducts()
{
    $type = TYPE_NODE_CONTENT;
    $nodes = node_load_multiple(array(), array('type' => $type));

    $IntegrateVK = getObjIntegrateVK();
    $response = [];

    $totalError = 0;
    $totalNodes = count($nodes);
    variable_set("totalProducts", count($nodes));

    foreach ($nodes as $node) {
        $item_id_vk = $node->field_integratevk_item_id["und"][0]["value"];
        $nid = $node->nid;
        $name = $node->title;
        // удалить те товары, которые есть в группе
        if ( (int) $item_id_vk == 0 && $item_id_vk == NULL ) {
            continue;
        }

        $res_del = $IntegrateVK->deleteProduct($nid, $name, $item_id_vk);
        sleep(1);
        
        if ($res_del->state_response === "error") {
            $totalError+=1;
            variable_set("totalError", $totalError);
        }
        $response[] = $res_del;
        //set value item_id from vk
        $node->field_integratevk_item_id["und"][0]["value"] = null;
        node_save($node);

        // for progress bar set percentage
        $percentage = count($response) / $totalNodes * 100;
        $percentage = round($percentage, 2);
        variable_set("integrate_vk_progressbar", $percentage);
    }
    variable_del("integrate_vk_progressbar");
    return $response;
}

function delAllCategories() {

    $machine_name = TAXONOMY_VOCABULARY;
    $v = taxonomy_vocabulary_machine_name_load($machine_name);
    $terms = taxonomy_get_tree($v->vid);
    
    $IntegrateVK = getObjIntegrateVK();
    $response = [];

    $totalError = 0;
    $totalTerms = count($terms);
    variable_set("totalProducts", count($terms));
    
    $categories = [];

    foreach ($terms as $term) {
        $tid = $term->tid;
        $title = $term->name;
        $nids = taxonomy_select_nodes($tid, FALSE);
        
        // установить значение album_id_vk для категориии товара на сайте
        $term = taxonomy_term_load($tid);
        $album_id_vk = $term->field_integratevk_album_id['und'][0]['value'];

        // удалить те категории товаров, которые есть в группе
        if ( (int) $album_id_vk == 0 && $album_id_vk == NULL ) {
        continue;
        }
        $res_del = $IntegrateVK->deleteCategoryProduct($title, $album_id_vk);
        sleep(1);

        if ($res_del->state_response === "error") {
            $totalError+=1;
            variable_set("totalError", $totalError);
        }  
        $response[] = $res_del;
        $term->field_integratevk_album_id['und'][0]['value'] = NULL;
        taxonomy_term_save($term);

        //set null for all products, which are associated terms 
        $nodes = node_load_multiple($nids);
        foreach ($nodes as $node) {
            $node->field_integratevk_in_album['und'][0]['value'] = NULL;
            node_save($node);
        }
        
        // for progress bar set percentage
        $percentage = count($response) / $totalTerms * 100;
        $percentage = round($percentage, 2);
        variable_set("integrate_vk_progressbar", $percentage);

    }
    variable_del("integrate_vk_progressbar");
    return $response;
}

function uploadAllProducts()
{
    $type = TYPE_NODE_CONTENT;
    $nodes = node_load_multiple(array(), array('type' => $type));

    $IntegrateVK = getObjIntegrateVK();
    $response = [];
    $totalNodes = count($nodes);

    $testCount = 0;
    $totalError = 0;
    variable_set("totalProducts", count($nodes));
    $pauseTriger = 10;
    $pause = 0;

    foreach ($nodes as $node) {
        $pause+=1;
        if($pause == $pauseTriger) {
            sleep(3);
        }        
        $nid = $node->nid;
        $name = getProductTitle($node);
        $status = (int) !$node->status;
        $item_id_vk = $node->field_integratevk_item_id["und"][0]["value"];
        
        // перенести те товары, которых нет в группе
        if ( (int) $item_id_vk !== 0 && $item_id_vk !== NULL ) {
            continue;
        }  
        
        $description = getProductDescriprion($node);
        $price = getProductPrice($node);
        $files = getProductImg($node);

        $res_add = $IntegrateVK->addProduct($nid, $name, $status, $description, $price, $files["array"]);
        sleep(1);
        
        if ($res_add->state_response === "error") {
            $totalError+=1;
            variable_set("totalError", $totalError);
        } 
        $response[] = $res_add;
        // set value item_id from vk
        $node->field_integratevk_item_id["und"][0]["value"] = $res_add->res->response->market_item_id;
        node_save($node);

        // for progress bar set percentage
        $percentage = count($response) / $totalNodes * 100;
        $percentage = round($percentage, 2);
        variable_set("integrate_vk_progressbar", $percentage);
    }
    variable_del("integrate_vk_progressbar");
    return $response;
}

function getAllNodes(){
    $type = TYPE_NODE_CONTENT;
    $nodes = node_load_multiple(array(), array('type' => $type));
    return $nodes;
}

function uploadRangeProducts($start, $end){
    $type = TYPE_NODE_CONTENT;
    $allNodes = node_load_multiple(array(), array('type' => $type));
    $totalNodes = count($allNodes);
    //-------getRangeProducts ------------
    $query = new EntityFieldQuery();
    $rangeNodes = $query
          ->entityCondition('entity_type', 'node')
          ->entityCondition('bundle', $type)
          ->range($start, $end)
          ->execute();
    $nodes = $rangeNodes["node"];
    $nids = [];
    foreach($nodes as $node) {
      $nids[] = $node->nid;
    }
    // ------------------------------------
    $nodes = node_load_multiple($nids);

    $IntegrateVK = getObjIntegrateVK();
    $response = [];

    $testCount = 0;
    $totalError = 0;
    variable_set("totalProducts", count($nodes));

    foreach ($nodes as $node) {       
        $nid = $node->nid;
        $name = getProductTitle($node);
        $status = (int) !$node->status;
        $item_id_vk = $node->field_integratevk_item_id["und"][0]["value"];
        
        // перенести те товары, которых нет в группе
        if ( (int) $item_id_vk !== 0 && $item_id_vk !== NULL ) {
            continue;
        }  
        
        $description = getProductDescriprion($node);
        $price = getProductPrice($node);
        $files = getProductImg($node);

        $res_add = $IntegrateVK->addProduct($nid, $name, $status, $description, $price, $files["array"]);
        sleep(1);
        
        if ($res_add->state_response === "error") {
            $totalError+=1;
            variable_set("totalError", $totalError);
        } 
        $response[] = $res_add;
        // set value item_id from vk
        $node->field_integratevk_item_id["und"][0]["value"] = $res_add->res->response->market_item_id;
        node_save($node);

        // for progress bar set percentage
        $percentage = count($response) / $totalNodes * 100;
        $percentage = round($percentage, 2);
        variable_set("integrate_vk_progressbar", $percentage);
    }
    variable_del("integrate_vk_progressbar");
    return $response;
}

function uploadAllCategories()
{

    $machine_name = TAXONOMY_VOCABULARY;
    $taxonomy = [];

    $IntegrateVK = getObjIntegrateVK();

    $v = taxonomy_vocabulary_machine_name_load($machine_name);
    $terms = taxonomy_get_tree($v->vid);
 
    $response = [];
    $totalTerms = count($terms);
    $totalError = 0;
    variable_set("totalProducts", count($terms));

    foreach ($terms as $term) {
        $taxonomy[$term->tid] = $term->name;
        $tid = $term->tid;
        $title = $term->name;
        $img = getMainImgByTid($tid);
        
        $term = taxonomy_term_load($tid);
        $album_id_vk = $term->field_integratevk_album_id['und'][0]['value'];

          // перенести те категории товаров, которых нет в группе
          if ( (int) $album_id_vk !== 0 && $album_id_vk !== NULL ) {
            continue;
        }  
        $res_add = $IntegrateVK->addAlbum($title, $img);
        sleep(1);

        if ($res_add->state_response === "error") {
            $totalError+=1;
            variable_set("totalError", $totalError);
        } 

        $response[] = $res_add;
        $term = taxonomy_term_load($tid);
        $term->field_integratevk_album_id['und'][0]['value'] = $res_add->res->response->market_album_id;
        taxonomy_term_save($term);

        // for progress bar set percentage
        $percentage = count($response) / $totalTerms * 100;
        $percentage = round($percentage, 2);
        variable_set("integrate_vk_progressbar", $percentage);
    }
  
    variable_del("integrate_vk_progressbar");
    return $response;
}

function uploadAllProductsInCategories() {
    
    $machine_name = TAXONOMY_VOCABULARY;
    $taxonomy = [];
    
    $album_ids_vk = [];
    $item_ids_vk = [];

    $IntegrateVK = getObjIntegrateVK();
    $v = taxonomy_vocabulary_machine_name_load($machine_name);
    $terms = taxonomy_get_tree($v->vid);

    $response = [];
    $totalTerms = count($terms);
    $totalError = 0;
    variable_set("totalProducts", count($terms));

    // get massive categories with products
    $categories = [];
    $nodes_in_categories = [];

    foreach ($terms as $term) {
        $tid = $term->tid;
        $nids =  taxonomy_select_nodes($tid, FALSE);
        $categories[$tid] = $nids;
    }
    $totalTermValue = count($categories);

    $album = [];
    foreach ($categories as $tid => $nids) {
        $item_ids_vk = [];
        $term = taxonomy_term_load($tid);
        $album_id_vk = $term->field_integratevk_album_id['und'][0]['value'];
        $album_name = $term->name;

        // взять те подборки ,что уже загружены в вк 
        if ( (int) $album_id_vk == 0 && $album_id_vk == NULL ) {
            continue;
        }
        
        $nodes = node_load_multiple($nids);
        foreach ($nodes as $key => $node) {
           if (!isset($node->field_integratevk_item_id["und"][0]["value"])) {
             continue;
             } else {
                 $item_id_vk = $node->field_integratevk_item_id["und"][0]["value"];
                 // взять те товары ,что уже загружены в вк 
                 if ((int) $item_id_vk == 0 && $item_id_vk == NULL ) {
                  continue;
                 }
                $item_ids_vk[] = $node->field_integratevk_item_id["und"][0]["value"];
                $nodes_in_categories[] = $node;
            }
        }

        $album[$album_id_vk] = (object) ["item_ids_vk" => $item_ids_vk, "album_name" => $album_name, "nodes_in_categories" => $nodes_in_categories]; 
    }



    // call addToAlbum
    if (count($album) > 0) {
        foreach ($album as $album_id_vk => $obj) {
            //$nameCategory = $obj->
            $res_addToAlbum = $IntegrateVK->addToAlbum($obj->album_name, $obj->item_ids_vk, $album_id_vk);
            sleep(1);
            if ($res_addToAlbum->state_response === "error") {
                $totalError+=1;
                variable_set("totalError", $totalError);
            } else {
                // set response in field_integratevk_in_album {0,1}
                $nodes = $obj->nodes_in_categories;
                foreach ($nodes as $node) {
                  $node->field_integratevk_in_album['und'][0]['value'] = $res_addToAlbum->res->response;   
                  node_save($node);
                } 
            }
            // progresss bar
            $response[] = $res_addToAlbum;
            // for progress bar set percentage
            $percentage = count($response) / $totalTermValue * 100;
            $percentage = round($percentage, 2);
            variable_set("integrate_vk_progressbar", $percentage);
        }
    }
    variable_del("integrate_vk_progressbar");
    return $response;

}
/**
 * Functions create fields
 */

 // добавить поле field_integratevk_item_id для типа товара car
function addFiledItem_id()
{
    $type_name = TYPE_NODE_CONTENT;
    $instance = [];

    field_cache_clear();
    field_associate_fields('integrate_vk');

    if (!field_info_field('field_integratevk_item_id')) {
        $field = array(
            'field_name' => 'field_integratevk_item_id',
            'type' => 'text',
        );
        $field = field_create_field($field);

        $instance['field_integratevk_item_id'] = array(
            'field_name' => 'field_integratevk_item_id',
            'entity_type' => 'node',
            'bundle' => $type_name,
            'label' => 'item_id vk',
            'description' => t('идентификатор для товара в vk.'),
            'widget' => array(
                'type' => 'textfield',
            ),
        );
    }
    if (!field_info_field('field_integratevk_in_album')) {
        $field = array(
            'field_name' => 'field_integratevk_in_album',
            'type' => 'text',
        );
        $field = field_create_field($field);

        $instance['field_integratevk_in_album'] = array(
            'field_name' => 'field_integratevk_in_album',
            'entity_type' => 'node',
            'bundle' => $type_name,
            'label' => 'in_album vk',
            'description' => t('добавлен в подборки товаров в vk.'),
            'widget' => array(
                'type' => 'textfield',
            ),
            'default_value_function' => 0,
        );
    }
    if (!empty($instance)) {
        foreach ($instance as $instance_field) {
            field_create_instance($instance_field);
        }
    }
}
 // добавить поле field_integratevk_in_album - товар в подборке
 function addFieldInAlbum()
 {
     $type_name = TYPE_NODE_CONTENT;
     $instance = [];
 
     field_cache_clear();
     field_associate_fields('integrate_vk');
 
     if (!field_info_field('field_integratevk_in_album')) {
         $field = array(
             'field_name' => 'field_integratevk_in_album',
             'type' => 'text',
         );
         $field = field_create_field($field);
 
         $instance['field_integratevk_in_album'] = array(
             'field_name' => 'field_integratevk_in_album',
             'entity_type' => 'node',
             'bundle' => $type_name,
             'label' => 'in_album vk',
             'description' => t('добавлен в подборки товаров в vk.'),
             'widget' => array(
                 'type' => 'textfield',
             ),
             'default_value_function' => 0,
         );
     }
     if (!empty($instance)) {
         foreach ($instance as $instance_field) {
             field_create_instance($instance_field);
         }
     }
 }

 // добавить поле field_integratevk_album_id для термина таксономии товара avtobrand
function addFieldAlbumId()
{
    $type_vocabulary = TAXONOMY_VOCABULARY;
    $instance = [];

    field_cache_clear();
    field_associate_fields('integrate_vk');
    
        if (!field_info_field('field_integratevk_album_id')) {
        $field = array(
        'field_name' => 'field_integratevk_album_id',
        'type' => 'text',
        );
        $field = field_create_field($field);

        $instance = array(
            'field_name' => 'field_integratevk_album_id',
            'entity_type' => 'taxonomy_term',
            'bundle' => $type_vocabulary,
            'label' => 'vk_album_id',
            'description' => 'идентификатор для подборки товаров',
            'widget' => array(
              'type' => 'text_textfield',
            )
        );
        }
        if(!empty($instance)){
            field_create_instance($instance);
        } 
}



function getNameCategories(){
    $machine_name = TAXONOMY_VOCABULARY; 
    $taxonomy = [];

    $v = taxonomy_vocabulary_machine_name_load($machine_name);
    $terms = taxonomy_get_tree($v->vid);
      foreach ($terms as $term) {
        $taxonomy[$term->tid] = $term->name;
      };
}

/**
 * function for uploadAllCategories()
 */

function getMainImgByTid($tid) {
    $photoMarketAlbum=null;
  
    $nids[$tid] = taxonomy_select_nodes($tid, FALSE);
    $nodes = node_load_multiple($nids[$tid]);
  
    foreach($nodes as $node) { 
      try {
          if (! isset($node->field_avtofoto["und"])) break; 
          $imgs = $node->field_avtofoto["und"];
    
           foreach($imgs as $img) {
             if (isset($img["uri"])) {
                $photoMarketAlbum = drupal_realpath($img["uri"]);
                break;
              }  else {
                 continue;
              }
           }
      }
     catch(\Throwable $th) {
         //get default image
        }
    } 
  
    if (! isset($photoMarketAlbum)) {
            //get default image
            $field = field_info_fields();
            $uri = file_load($field['field_avtofoto']['settings']['default_image'])->uri;
            $photoMarketAlbum = drupal_realpath($uri);
        }  
    return $photoMarketAlbum;
  }


