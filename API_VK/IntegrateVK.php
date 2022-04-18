<?php
namespace vk_api;

require_once realpath(dirname(__FILE__) . '/src/API.php');
require_once realpath(dirname(__FILE__) . '/src/Market.php');
require_once realpath(dirname(__FILE__) . '/src/Photo.php');
use \vk_api_config\API as API;
use \vk_api_photo\Photo as Photo;
use \vk_api_market\Market as Market;

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


    public function getPhoto_ids($files){
        $photo_ids = array();
        foreach ($files as $path_file) {
            $photo_ids[] = $this->getUploadExtraPhotoId($path_file);
        }
        return implode(',', $photo_ids);
    }

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

?>