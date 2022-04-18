<?php
namespace vk_api_config;

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
?>