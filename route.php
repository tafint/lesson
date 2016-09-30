<?php 

$router->get("/","IndexController@index");

$router->any("/user/registration","UserController@registration");
$router->any("/user/login","UserController@login");
$router->get("/user/logout","UserController@logout");
$router->get("/user/successful","UserController@successful");
$router->get("/user/home","UserController@home");
$router->get("/user/profile/{:id}","UserController@profile");
$router->any("/user/changeemail","UserController@change_email");
$router->any("/user/changepassword","UserController@change_password");
$router->get("/user/manage","UserController@manage");
$router->get("/user/changegroup","UserController@change_group");
$router->post("/user/delete/{:id}","UserController@delete");
$router->post("/user/info/{:id}","UserController@info");
$router->post("/user/update","UserController@update");
$router->post("/user/dynamicupdate","UserController@dynamicupdate");
$router->get("/confirm/{:any}","UserController@confirm");

$router->get("/friend/index","FriendController@index");
$router->get("/friend/view/{:id}","FriendController@view");
$router->get("/friend/request","FriendController@request");
$router->get("/friend/index","FriendController@index");
$router->get("/friend/search","FriendController@search");
$router->post("/friend/add","FriendController@add");
$router->post("/friend/remove","FriendController@remove");
$router->get("/friend/suggest","FriendController@suggest");
$router->post("/friend/handle","FriendController@handle");

$router->get("/message/index","MessageController@index");
$router->post("/message/create","MessageController@create");
$router->get("/message/load","MessageController@load");

$router->post("/image/load","ImageController@upload");
$router->post("/image/like","ImageController@like");
$router->post("/image/unlike","ImageController@unlike");
$router->post("/image/view","ImageController@view");

$router->post("/favorite/add","FavoriteController@add");
$router->post("/favorite/remove","FavoriteController@remove");

$router->post("/follow/add","FollowController@add");
$router->post("/follow/remove","FollowController@remove");
$router->get("/follow/index","FollowController@view");
