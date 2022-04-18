<?php
namespace vk_api;

class API {
    protected $USER_ID = "713894096";
    protected $ACCESS_TOKEN = '025c00d9f4fb1ddeef34341dc13a17216ca41a27105855b3fe2f7d7e3a2a37b0f088585bf32877701bc8d';  // token accesss for market and photos 
    protected $APP_ID = "8111381"; 
    protected $GROUP_ID = "212007863";
    protected $CATEGORY_ID = "400"; // Транспорт.Автомобили,

    function __construct($group_id, $user_id, $access_token, $category_id){
        $this->GROUP_ID = $group_id;
        $this->USER_ID = $user_id;
        $this->ACCESS_TOKEN = $access_token;
        $this->CATEGORY_ID = $category_id;
    }

    public function settup($group_id, $user_id, $access_token, $category_id) {
        $this->GROUP_ID = $group_id;
        $this->USER_ID = $user_id;
        $this->ACCESS_TOKEN = $access_token;
        $this->CATEGORY_ID = $category_id;
    }

    public function setUserId($userId){
        $this->USER_ID = $userId;
    }

    public function getUserId()
    {
        return $this->USER_ID;
    }
    public function getAppId()
    {
        return $this->APP_ID;
    }
    public function getGroupId()
    {
        return $this->GROUP_ID;
    }
    public function getAccessToken()
    {
        return $this->ACCESS_TOKEN;
    }
    public function getCategoryId() 
    {
        return $this->CATEGORY_ID;
    }
    
    public function getConfig() {
        $config = (object) [
            "group_id" => $this->getGroupId(),
            "user_id" => $this->getUserId(),
            "access_token" => $this->getAccessToken(),
            "category_id" => $this->getCategoryId()
        ];

        return $config;
    }



}

class Photo extends API 
{
    public function getMarketUploadServer($main_photo){
        if ($main_photo === true) {
            $request_params = array(
                "group_id" => parent::getGroupId(), 
                "main_photo" => 1,
                "v" => "5.131",
                "access_token" => parent::getAccessToken()                
            );
        }  else {
            $request_params = array(
                "group_id" => parent::getGroupId(), 
                "v" => "5.131",
                "access_token" => parent::getAccessToken()                
            );
        }
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/photos.getMarketUploadServer?' . $get_params));

        if(isset($result->response->upload_url)) {
            return $result->response->upload_url;
        }
        else {
            $error_msg = $result->error->error_msg;
            throw new \Exception($error_msg);
            //return NULL;
        }
    }

    public function postPhotoMarket($url, $file)
    {
        $curl_file = new \CURLFile($file);
        $curl = curl_init();

        curl_setopt_array($curl, array(
        //CURLOPT_HEADER => true,    
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('file'=> $curl_file),
        CURLOPT_HTTPHEADER => array(
            'Cookie: remixlang=0'
        ),
        ));
        $response = curl_exec($curl);
        $result = json_decode($response);
        curl_close($curl);

        if (isset($result->error)) {
            $error_msg = $result->error;
            throw new \Exception($error_msg);
        }
        return $result;
    }

    public function confirmPostMarketPhoto($params){
        if (isset($params->hash)) {
            $request_params = array(
                "group_id" => parent::getGroupId(),
                "photo" => $params->photo,  
                "access_token" => parent::getAccessToken(),
                "server" => $params->server,
                "hash" => $params->hash,                
                "v" => '5.131',
            );
        } else {
            $request_params = array(
                "group_id" => parent::getGroupId(),
                "photo" => $params->photo,  
                "access_token" => parent::getAccessToken(),
                "server" => $params->server,
                "v" => '5.131',
            );
        }
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/photos.saveMarketPhoto?' . $get_params));

        if (isset($result->error->error_msg)) {
            $error_msg = $result->error->error_msg;
            throw new \Exception($error_msg);
        }
        return $result;
    }


    // Album Market Photo

    public function getMarketAlbumUploadServer() {
           $request_params = array(
                "group_id" => parent::getGroupId(), 
                "v" => "5.131",
                "access_token" => parent::getAccessToken()                
            );
        
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/photos.getMarketAlbumUploadServer?' . $get_params));

        if(isset($result->response->upload_url)) {
            return $result->response->upload_url;
        }
        else {
            $error_msg = $result->error->error_msg;
            throw new \Exception($error_msg);
        }
    }

    public function postPhotoMarketAlbum($url, $file)
    {
        $curl_file = new \CURLFile($file);
        $curl = curl_init();

        curl_setopt_array($curl, array(
        //CURLOPT_HEADER => true,    
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('file'=> $curl_file),
        CURLOPT_HTTPHEADER => array(
            'Cookie: remixlang=0'
        ),
        ));
        $response = curl_exec($curl);
        $result = json_decode($response);
        curl_close($curl);

        if (isset($result->error)) {
            $error_msg = $result->error;
            throw new \Exception($error_msg);
        }
        return $result;
    }

    public function saveMarketAlbumPhoto($params) {
        $request_params = array(
            "group_id" => parent::getGroupId(),
            "photo" => $params->photo,  
            "access_token" => parent::getAccessToken(),
            "server" => $params->server,
            "hash" => $params->hash,                
            "v" => '5.131',
        );
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/photos.saveMarketAlbumPhoto?' . $get_params));

        if (isset($result->error->error_msg)) {
            $error_msg = $result->error->error_msg;
            throw new \Exception($error_msg);
        }
        return $result;

    }


}


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

class Orders extends API {
    public function get($name, $deleted, $description, $price, $main_photo_id, $photo_ids){
        $request_params = array(
            "v" => "5.131",
            "access_token" => parent::getAccessToken()                
        ); 
        $get_params = http_build_query($request_params);
        $result = json_decode(file_get_contents('https://api.vk.com/method/orders.get?' . $get_params));

        if (isset($result->error->error_msg)) {
            $error_msg = $result->error->error_msg;
            throw new \Exception($error_msg);
        }

        return $result;
    }
}
class IntegrateVK 
{
    public $photo;
    public $market;
    public $api;

    function __construct($GROUP_ID, $USER_ID, $ACCESS_TOKEN, $CATEGORY_ID) {
        $this->photo = new Photo($GROUP_ID, $USER_ID, $ACCESS_TOKEN, $CATEGORY_ID);
        $this->market = new Market($GROUP_ID, $USER_ID, $ACCESS_TOKEN, $CATEGORY_ID);
        $this->api = new API($GROUP_ID, $USER_ID, $ACCESS_TOKEN, $CATEGORY_ID);
    }
    
    public function getUploadMainPhotoId($file)
    {
        $photo = $this->photo;
        // получить ссылку на загрузку фото товара c main_photo=1
        $upload_url = $photo->getMarketUploadServer(true);
        // отправить фото товара по ссылке $upload_url
        $params = $photo->postPhotoMarket($upload_url, $file);
        // подтвердить сохранение фото товара 
        $res_confirm = $photo->confirmPostMarketPhoto($params);

        $photo_id = $res_confirm->response[0]->id;
        
        return $photo_id;
    }

    public function getUploadExtraPhotoId($file) {
        $photo= $this->photo;
        // получить ссылку на загрузку фото товара c main_photo=0
        $upload_url = $photo->getMarketUploadServer(false);
        // отправить фото товара по ссылке $upload_url
        $params = $photo->postPhotoMarket($upload_url, $file);
        // подтвердить сохранение фото товара 
        $res_confirm = $photo->confirmPostMarketPhoto($params);

        $photo_id = $res_confirm->response[0]->id;
        
        return $photo_id;
    }

    public function getUploadAlbumMarketPhotoId($file) {
        $photo = $this->photo;
        //получить ссылку на загрузку фото подборки товара
        $upload_url = $photo->getMarketAlbumUploadServer();
        // отправить фото подборки товара по ссылке $upload_url
        $params = $photo->postPhotoMarketAlbum($upload_url, $file);
        // подтвердить сохранение фото товара 
        $res_confirm = $photo->saveMarketAlbumPhoto($params);

        $photo_id = $res_confirm->response[0]->id;
        
        return $photo_id; 
    }

    /**
     * @param $files - array
     * @return String
     */
    public function getPhoto_ids($files){
        $photo_ids = array();
        foreach ($files as $path_file) {
            $photo_ids[] = $this->getUploadExtraPhotoId($path_file);
        }
        return implode(',', $photo_ids);
    }

    /**
     * @param $name, $item_id, $description, $price - String
     * @param $deleted - {0,1} 
     * @param $files - array
     */
    public function addProduct($nid, $name, $deleted, $description, $price, $files) {
        $market = $this->market;
        $state_response = null;
        $main_file = array_shift($files);
        try {
            $main_photo_id = $this->getUploadMainPhotoId($main_file);
            $photo_ids = $this->getPhoto_ids($files);
            $res_add = $market->add($name, $deleted, $description, $price, $main_photo_id, $photo_ids);  
            
            $state_response = "ok";
        } 
        catch (\Exception $th) {
            $res_add = $th->getMessage();
            $res_add .= ' Файл: ' . $th->getFile() . ' Строка: ' . $th->getLine();
            $res_add .= PHP_EOL;
            $res_add .= $th->getTraceAsString();
            $state_response = "error";
        }
        catch(Throwable $ex) {
            $res_add = "Ошибка при выполнении программы";

            $res_add .= PHP_EOL;
            $res_add .= $ex->getTraceAsString();
            $state_response = "error";
        }

        $options = array('absolute' => TRUE);
        $url = url('node/' . $nid, $options);

        $res = (object) [
           "state_response" => $state_response,
           "res" => $res_add,
           "obj" => (object) [
               "name" => $name,
               "link" => $url,
           ] ,
        ];
        return $res;
    }


    public function deleteProduct($nid, $name, $item_id) {
        $market = $this->market;
        $state_response = null;

        try {
            $res_del = $market->deleteProduct($item_id);

            $state_response = "ok";
        }
        catch (\Exception $th) {
            $res_del = $th->getMessage();
            $res_del .= ' Файл: ' . $th->getFile() . ' Строка: ' . $th->getLine();
            $res_del .= PHP_EOL;
            $res_del .= $th->getTraceAsString();
            $state_response = "error";
        } 
        catch (\Throwable $th) {
            $res_del = "Ошибка при выполнении программы";

            $res_del .= PHP_EOL;
            $res_del .= $ex->getTraceAsString();
            $state_response = "error";
        }

        $options = array('absolute' => TRUE);
        $url = url('node/' . $nid, $options);

        $res = (object) [
            "state_response" => $state_response,
            "res" => $res_del,
            "obj" => (object) [
                "name" => $name,
                "link" => $url,
            ],
         ];
         return $res;
    }

    /**
     * @param $name, $item_id, $description, $price - String
     * @param $deleted - {0,1} 
     * @param $files - array
     */
    function editProduct($item_id, $name, $description, $price, $deleted, $files) {
        $market = $this->market;
        $state_response = null;
        $main_file = array_shift($files);
        try {
            $main_photo_id = $this->getUploadMainPhotoId($main_file);
            $photo_ids = $this->getPhoto_ids($files);
            $res_edit = $market->editProduct($item_id, $name, $description, $price, $deleted, $main_photo_id, $photo_ids);
            $state_response = "ok";
        }
        catch (\Exception $th) {
            $res_edit = $th->getMessage();
            $res_edit .= ' Файл: ' . $th->getFile() . ' Строка: ' . $th->getLine();
            $res_edit .= PHP_EOL;
            $res_edit .= $th->getTraceAsString();
            $state_response = "error";
        } 
        catch (\Throwable $th) {
            $res_edit = "Ошибка при выполнении программы";

            $res_edit .= PHP_EOL;
            $res_edit .= $ex->getTraceAsString();
            $state_response = "error";
        }

        $res = (object) [
            "state_response" => $state_response,
            "res" => $res_edit,
            "obj" => (object) [
                "name" => $name
            ],
         ];
         return $res;
    }

    // Market Album 
    public function addAlbum($title, $photo) {
        $market = $this->market;
        $state_response = null;

        try {
            $photo_id = $this->getUploadAlbumMarketPhotoId($photo);
            $res_add = $market->addAlbum($title, $photo_id);

            $state_response = "ok";
        } 
        catch (\Exception $th) {
            $res_add = $th->getMessage();
            $res_add .= ' Файл: ' . $th->getFile() . ' Строка: ' . $th->getLine();
            $res_add .= PHP_EOL;
            $res_add .= $th->getTraceAsString();
            $state_response = "error";
        }
        catch (\Throwable $th) {
            $res_add = "Ошибка при выполнении программы";

            $res_add .= PHP_EOL;
            $res_add .= $ex->getTraceAsString();
            $state_response = "error";
        }
        $res = (object) [
            "state_response" => $state_response,
            "res" => $res_add,
            "obj" => (object) [
                "name" => $title,
                ],
         ];
         return $res;
    }

    public function getString_ids($items){
        $items_ids = array();
        foreach ($items as $items_id) {
            $items_ids[] = $items_id;
        }
        return implode(',', $items_ids);
    }

/**
 * @param $name - наименование альбома подборки (String)
 * @param $item_ids -идентификаторы товаров вк (array)
 * @param $album_ids - идентификаторы подборок товаров вк (array)
 */
    public function addToAlbum($name, $item_ids, $album_ids) {
        $market = $this->market;
        $state_response = null;

        try {
            // get string from array $item_ids
            $item_ids = $this->getString_ids($item_ids);
            $album_ids = $this->getString_ids($album_ids);
            $res = $market->addToAlbum($item_ids, $album_ids);
            $state_response = "ok";
        }
        catch (\Exception $th) {
            $res = $th->getMessage();
            $res .= ' Файл: ' . $th->getFile() . ' Строка: ' . $th->getLine();
            $res .= PHP_EOL;
            $res .= $th->getTraceAsString();
            $state_response = "error";
        }  
        catch (\Throwable $th) {
            $res = $th->getMessage();
            $res .= ' Файл: ' . $th->getFile() . ' Строка: ' . $th->getLine();
            $res .= PHP_EOL;
            $res .= $th->getTraceAsString();
            $state_response = "error";
        }
        $res = (object) [
            "state_response" => $state_response,
            "res" => $res,
            "obj" => (object) [
                "name" => $name,
                ],
         ];
         return $res;

    }
/**
 * @param $item_id - string
 * @param $album_ids - array
 */
    public function removeFromAlbum($item_id, $album_ids) {
        $market = $this->market;
        $state_response = null;

        try {
            // get string from array $album_ids
            $album_ids = $this->getString_ids($album_ids);

            $res = $market->removeFromAlbum($item_id, $album_ids);
            $state_response = "ok";
        }
        catch (\Exception $th) {
            $res = $th->getMessage();
            $res .= ' Файл: ' . $th->getFile() . ' Строка: ' . $th->getLine();
            $res .= PHP_EOL;
            $res .= $th->getTraceAsString();
            $state_response = "error";
        } 
        catch (\Throwable $th) {
            $res = "Ошибка при выполнении программы";

            $res .= PHP_EOL;
            $res .= $th->getTraceAsString();
            $state_response = "error";
        }

        $res = (object) [
            "state_response" => $state_response,
            "res" => $res,
         ];
         return $res;  
    }

/**
 * @param $item_id (string)
 * @param $album_id_from (array)
 * @param $album_id_to (array)
 */
    public function changeProductCategory($item_id, $album_id_from, $album_id_to) {
        $market = $this->market;
        $state_response = null;
        $name = null;
        try {
            $res = $this->removeFromAlbum($item_id, $album_id_from);
            $res = $this->addToAlbum($name, array($item_id), $album_id_to);
        }
        catch (\Exception $th) {
            $res = $th->getMessage();
            $res .= ' Файл: ' . $th->getFile() . ' Строка: ' . $th->getLine();
            $res .= PHP_EOL;
            $res .= $th->getTraceAsString();
            $state_response = "error";
        } 
        catch (\Throwable $th) {
            $res = "Ошибка при выполнении программы";
            $res .= PHP_EOL;
            $res .= $th->getTraceAsString();
            $state_response = "error";
        }

        $res = (object) [
            "state_response" => $state_response,
            "res" => $res,
         ];
         return $res;

    }


    public function deleteCategoryProduct($name, $album_id) {
        $market = $this->market;
        $state_response = null;

        try {
            $res_del = $market->deleteAlbum($album_id);
            $state_response = "ok";
        }
        catch (\Exception $th) {
            $res_del = $th->getMessage();
            $res_del .= ' Файл: ' . $th->getFile() . ' Строка: ' . $th->getLine();
            $res_del .= PHP_EOL;
            $res_del .= $th->getTraceAsString();
            $state_response = "error";
        } 
        catch (\Throwable $th) {
            $res_del = "Ошибка при выполнении программы";

            $res_del .= PHP_EOL;
            $res_del .= $th->getTraceAsString();
            $state_response = "error";
        }

        $res = (object) [
            "state_response" => $state_response,
            "res" => $res_del,
            "obj" => (object) [
                "name" => $name,
                ],
         ];
         return $res;
    }

    public function getCategories() {
        $market = $this->market;
        $state_response = null;
        try {
            $res = $market->getCategories();
            $state_response = "ok";
        }
        catch (\Exception $th) {
            $res = $th->getMessage();
            $res .= ' Файл: ' . $th->getFile() . ' Строка: ' . $th->getLine();
            $res .= PHP_EOL;
            $res .= $th->getTraceAsString();
            $state_response = "error";
        } 
        catch (\Throwable $th) {
            $res = "Ошибка при выполнении программы";
            $res .= PHP_EOL;
            $res .= $th->getTraceAsString();
            $state_response = "error";
        }

        $res = (object) [
            "state_response" => $state_response,
            "res" => $res,
         ];
         return $res;
    }
 
}


/**
 * example
 */
/*
$USER_ID = "713894096";
$ACCESS_TOKEN = '025c00d9f4fb1ddeef34341dc13a17216ca41a27105855b3fe2f7d7e3a2a37b0f088585bf32877701bc8d';  // token accesss for market and photos 
$APP_ID = "8111381"; 
$GROUP_ID = "212007863";
$CATEGORY_ID = "400";

$integrateVK = new IntegrateVK($GROUP_ID, $USER_ID, $ACCESS_TOKEN, $CATEGORY_ID);

$uploadPhotos = [
    "http://v.tachki.pro/sites/default/files/imagecar/508810-0.JPG",
    "http://v.tachki.pro/sites/default/files/imagecar/508810-1.JPG",
    "http://v.tachki.pro/sites/default/files/imagecar/508810-2.JPG",
    
];

$res_add = $integrateVK->addProduct('авто_тест', 'описание авто_тест', '87654321', $uploadPhotos);

var_dump($res_add);

*/


?>