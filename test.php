<?php
//$pattern = '/({:.+?})/';
// echo  preg_match($pattern,'tai/{:name}/{:slug}/',$matchers);
// var_dump($matchers);
// $result = preg_replace_callback($pattern,function($matchers){ return $matchers[0];},'tai/{:name}/{:slug}/');
// var_dump($result);
$pattern = '/user/tai';
var_dump(preg_match("#^$pattern$#",'/user/tai'));