<?php
namespace vk_api_photo;
require_once 'API.php';
use \vk_api_config\API as API;

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


?>