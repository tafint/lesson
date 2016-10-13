<?php 
namespace Config;

/**
 * This is a class Route
 */
class Route
{
    private $_router;

    public function __construct($router){
        
        $this->_router = $router;

        $this->_router->get("/","IndexController@index");

        $this->_router->any("/user/registration","UserController@registration");
        $this->_router->any("/user/login","UserController@login");
        $this->_router->get("/user/logout","UserController@logout");
        $this->_router->get("/user/successful","UserController@successful");
        $this->_router->get("/user/home","UserController@home");
        $this->_router->any("/user/profile/{:id}","UserController@profile");
        $this->_router->any("/user/changeemail","UserController@change_email");
        $this->_router->any("/user/changepassword","UserController@change_password");
        $this->_router->get("/user/manage","UserController@manage");
        $this->_router->get("/user/confirm/{:any}","UserController@confirm");
        $this->_router->any("/user/search","UserController@search");

        $this->_router->post("/user/changegroup","Api\UserApiController@change_group");
        $this->_router->delete("/user/delete/{:id}","Api\UserApiController@delete");
        $this->_router->get("/user/info/{:id}","Api\UserApiController@info");
        $this->_router->post("/user/update","Api\UserApiController@update");
        $this->_router->post("/user/dynamicupdate","Api\UserApiController@dynamicupdate");

        $this->_router->get("/friend/index","FriendController@index");
        $this->_router->get("/friend/view/{:id}","FriendController@view");
        $this->_router->get("/friend/request","FriendController@request");
        $this->_router->get("/friend/suggest","FriendController@suggest");

        $this->_router->post("/friend/add","Api\FriendApiController@add");
        $this->_router->post("/friend/remove","Api\FriendApiController@remove");
        $this->_router->post("/friend/handle","Api\FriendApiController@handle");


        $this->_router->get("/message/index","MessageController@index");
        $this->_router->post("/message/create","Api\MessageApiController@create");
        $this->_router->post("/message/load","Api\MessageApiController@load");

        $this->_router->post("/image/upload","Api\ImageApiController@upload");
        $this->_router->post("/image/like","Api\ImageApiController@like");
        $this->_router->post("/image/unlike","Api\ImageApiController@unlike");
        $this->_router->post("/image/view","Api\ImageApiController@view");
        $this->_router->post("/image/delete","Api\ImageApiController@delete");

        $this->_router->post("/favorite/add","Api\FavoriteApiController@add");
        $this->_router->post("/favorite/remove","Api\FavoriteApiController@remove");

        $this->_router->post("/follow/add","Api\FollowApiController@add");
        $this->_router->post("/follow/remove","Api\FollowApiController@remove");
        $this->_router->get("/follow/index","FollowController@index");
    }

    public function getRoute()
    {
        return $this->_router;
    }
}