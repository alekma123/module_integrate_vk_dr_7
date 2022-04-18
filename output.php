<?php 
function output($response, $operation){
    // вывод состояния загрузки товаров в вк
    // $key_error = array_search('error', array_column($response, 'state_response'));
    $totalProducts = (int) variable_get("totalProducts");
    $totalError = (int) variable_get("totalError");

    $successTotalOperation = $totalProducts - $totalError;

    $commands[] = ajax_command_html("#msg", "<div class='messages status'>$operation : $successTotalOperation из $totalProducts </div>");
    
    $commands[] = ajax_command_append("#logs-output", "<div class='operation'><p>$operation : $successTotalOperation из $totalProducts </p>
    <p>--------------------</p></div>");
   
    foreach ($response as $res) {
        $name = $res->obj->name;
        if (isset($res->obj->link)) {
            $link = $res->obj->link;
        } else $link = null;

      if ($res->state_response == 'error') {
        $text_error = $res->res;
        $commands[] = ajax_command_append("#logs-output", "<div>$name <a href='$link'>$link</a>
        <div class='log-error'> Ошибка: $text_error</div>
        </div>");
      } else {
        $commands[] = ajax_command_append("#logs-output", "<div class='log-status'>$name <a href='$link'>$link</a>
        <div>Успешно</div>
        </div>");
      }
    }

    variable_del("totalError");
    return $commands;
  }

  ?>