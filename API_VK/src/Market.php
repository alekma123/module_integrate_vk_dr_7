<?php
namespace vk_api_market;
require_once "API.php";
use \vk_api_config\API as API;

class Market extends API
{
    public function getCategories() {
        $request_params = array(
            "owner_id" => '-' . parent::getGroupId(),
            "count" => 100,
            "v" => "5.131",
            "access_token" => parent::getAccessToken()                
        ); 
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/market.getCategories?' . $get_params));

        if (isset($result->error->error_msg)) {
            $error_msg = $result->error->error_msg;
            throw new \Exception($error_msg);
        }

        return $result;
    }

    public function add($name, $deleted, $description, $price, $main_photo_id, $photo_ids){
        $request_params = array(
            "owner_id" => '-' . parent::getGroupId(),
            "name" => $name,
            //"deleted" => $deleted,
            "description" => $description,
            "price" => $price,
            "category_id" => parent::getCategoryId(),
            "main_photo_id" => $main_photo_id, 
            "photo_ids" => $photo_ids,
            "v" => "5.131",
            "access_token" => parent::getAccessToken()                
        ); 
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/market.add?' . $get_params));

        if (isset($result->error->error_msg)) {
            $error_msg = $result->error->error_msg;
            throw new \Exception($error_msg);
        }

        return $result;
    }

    public function deleteProduct($item_id) {
        
        $request_params = array(
            "owner_id" => '-' . parent::getGroupId(),
            "item_id" => $item_id,
            "v" => "5.131",
            "access_token" => parent::getAccessToken()                
        ); 
        
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/market.delete?' . $get_params));

        if (isset($result->error->error_msg)) {
            $error_msg = $result->error->error_msg;
            throw new \Exception($error_msg);
        }

        return $result;
    }

    public function editProduct($item_id, $name, $description, $price, $deleted, $main_photo_id, $photo_ids){
        $request_params = array(
            "owner_id" => '-' . parent::getGroupId(),
            "item_id" => $item_id,
            "name" => $name,
            "description" => $description,
            //"category_id" => $category_id,
            "price" => $price,
            // "old_price" => $old_price,
            "deleted" => $deleted,
            "main_photo_id" => $main_photo_id,
            "photo_ids" => $photo_ids,
            "v" => "5.131",
            "access_token" => parent::getAccessToken()                
        ); 
        
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/market.edit?' . $get_params));

        if (isset($result->error->error_msg)) {
            $error_msg = $result->error->error_msg;
            throw new \Exception($error_msg);
        }

        return $result;
    }

    // Album Market
    public function addAlbum($title, $photo_id){
        $request_params = array(
            "owner_id" => '-' . parent::getGroupId(),
            "title" => $title,
            "photo_id" => $photo_id,
            "main_album" => 0,
            "is_hidden" => 0, 
            "v" => "5.131",
            "access_token" => parent::getAccessToken()                
        ); 
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/market.addAlbum?' . $get_params));

        if (isset($result->error->error_msg)) {
            $error_msg = $result->error->error_msg;
            throw new \Exception($error_msg);
        }

        return $result;
    }

    public function addToAlbum($item_ids, $album_ids){
            $request_params = array(
                "owner_id" => '-' . parent::getGroupId(),
                "item_ids" => $item_ids,
                "album_ids" => $album_ids,
                "v" => "5.131",
                "access_token" => parent::getAccessToken()                
            ); 
        
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/market.addToAlbum?' . $get_params));

        if (isset($result->error->error_msg)) {
            $error_msg = $result->error->error_msg;
            throw new \Exception($error_msg);
        }

        return $result;
    }

    public function removeFromAlbum($item_id, $album_ids){
            $request_params = array(
                "owner_id" => '-' . parent::getGroupId(),
                "item_id" => $item_id,
                "album_ids" => $album_ids,
                "v" => "5.131",
                "access_token" => parent::getAccessToken()                
            ); 
        
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/market.removeFromAlbum?' . $get_params));

        if (isset($result->error->error_msg)) {
            $error_msg = $result->error->error_msg;
            throw new \Exception($error_msg);
        }

        return $result;
    }

    public function deleteAlbum($album_id){
       
        $request_params = array(
            "owner_id" => '-' . parent::getGroupId(),
            "album_id" => $album_id,
            "v" => "5.131",
            "access_token" => parent::getAccessToken()                
        ); 
         
         $get_params = http_build_query($request_params);
         $result = json_decode(file_get_contents('https://api.vk.com/method/market.deleteAlbum?' . $get_params));
 
         if (isset($result->error->error_msg)) {
             $error_msg = $result->error->error_msg;
             throw new \Exception($error_msg);
         }
 
         return $result;
     }

}
?>