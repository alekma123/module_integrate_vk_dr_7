<?php



function integrate_vk_orders(){
    return drupal_get_form('form_orders_vk');
}

function form_orders_vk($form, &$form_state)
{
    $form['orders'] = array(
        '#type' => 'textfield',
        '#title' => t('test'),        
        '#default_value' => '400',
        '#required' => TRUE, 
    );
    return $form;
}

?>