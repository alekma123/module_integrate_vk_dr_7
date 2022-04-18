<?php

function ajax_upload_products_progressbar_progress() {
    $progress = array(
      'message' => t('Пожалуйста, подождите...'),
      'percentage' => -1,
    );

    $completed_percentage = variable_get('integrate_vk_progressbar');
  
    if ($completed_percentage) {
      $progress['message'] = t('Выполняется');
      $progress['percentage'] = $completed_percentage;
    }
  
    drupal_json_output($progress);
  }

?>