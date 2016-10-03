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
$router->post("/user/changegroup","UserController@change_group");
$router->delete("/user/delete/{:id}","UserController@delete");
$router->get("/user/info/{:id}","UserController@info");
$router->post("/user/update","UserController@update");
$router->post("/user/dynamicupdate","UserController@dynamicupdate");
$router->get("/user/confirm/{:any}","UserController@confirm");
$router->any("/user/search","UserController@search");

$router->get("/friend/index","FriendController@index");
$router->get("/friend/view/{:id}","FriendController@view");
$router->get("/friend/request","FriendController@request");
$router->get("/friend/index","FriendController@index");
$router->post("/friend/add","FriendController@add");
$router->post("/friend/remove","FriendController@remove");
$router->get("/friend/suggest","FriendController@suggest");
$router->post("/friend/handle","FriendController@handle");

$router->get("/message/index","MessageController@index");
$router->post("/message/create","MessageController@create");
$router->post("/message/load","MessageController@load");

$router->post("/image/upload","ImageController@upload");
$router->post("/image/like","ImageController@like");
$router->post("/image/unlike","ImageController@unlike");
$router->post("/image/view","ImageController@view");
$router->post("/image/delete","ImageController@delete");

$router->post("/favorite/add","FavoriteController@add");
$router->post("/favorite/remove","FavoriteController@remove");

$router->post("/follow/add","FollowController@add");
$router->post("/follow/remove","FollowController@remove");
$router->get("/follow/index","FollowController@view");
