<<<<<<< Updated upstream
<?php
namespace App\Controller;

use App\Service\FriendService;
use \Exception;
use App\Exception\UserException;
use App\Exception\CheckException;

/**
 * This is a class FriendController
 */
class FriendController extends Controller
{   
    public function __construct()
    {   
        parent::__construct();
        
        $this->_model->load('favorite');
        $this->_model->load('group');
        $this->_model->load('image');
        $this->_model->load('image_like');
    }

    /**
     * action friend list
     *
     */
    public function index()
    {   
        try {
            $data = $this->_data;

            if (isset($data['error'])) {
                throw new UserException("Please login");
            }

            $list_friends = $this->friend_list->get_all($data['user']['id']);
            
            if ($list_friends) {
                foreach ($list_friends as $key => $friend) {
                    
                    if ($friend['user_id'] == $data['user']['id']) {
                        $friend['user'] = $this->user->find_id($friend['user_id_to']);
                    } else {
                        $friend['user'] = $this->user->find_id($friend['user_id']);
                    }
                    
                    if ($friend['user']) {
                        $data['list_friends'][$key] = $friend;
                    }
                }
            }
        } catch (UserException $e) {
            redirect();
        }
        
        $this->_view->load_content('friend.list', $data);
    }

    /**
     * action view friend detail
     *
     */
    public function view($params)
    {   
        $data = $this->_data;
        try {
            
            if (isset($data['error'])) {
                throw new UserException("Please login");
            }
            
            if (isset($params[0])) {
                $id = $params[0];
                $data['permisson'] = false;
                
                // get info
                if ($id == $data['user']['id']){
                    $data['is_owner'] = true;
                    $data['profile'] = $data['user'];
                    $data['permisson'] = true;
                } else {
                    $data['is_owner'] = false;
                    $user = $this->user->find_id($id);
                    if (!$user) {
                        throw new CheckException("Not exist user");
                    }
                    $data['profile'] = $this->user->find_id($id);
                }
                
                // check relation
                $result = $this->friend_list->is_friend($id, $data['user']['id']);
                
                if ($result) {
                    $data['is_friend'] = true;
                    $data['permisson'] = true;
                } elseif ($data['user']['group_id'] ==1) {
                    $data['is_friend'] = false;
                    $data['permisson'] = true;
                } elseif (($data['user']['group_id'] == $data['profile']['group_id']) && ($data['profile']['id'] != $data['user']['id'])) {
                    $data['permisson'] = true;
                    $data['is_friend'] = false;
                } else {
                    $data['is_friend'] = false;
                }
                
                if (!$data['is_friend']) {
                    $data['is_request'] = $this->friend_request->have_request($data['user']['id'], $data['profile']['id']);
                }
                
                // check favorite & follow
                $data['is_favorite'] = $this->favorite->is_favorite($data['user']['id'], $id);
                $data['is_follow'] = $this->follow->is_follow($data['user']['id'], $id);
                
                // get message
                if ($data['user']['id'] != $data['profile']['id']) {
                    $messages = $this->message_log->get_message_user($id, $data['user']['id']);
                    $data['message_log']=$messages;
                }
                
                // get friend
                if ($data['is_owner'] || $data['is_friend']) {
                    $friends = $this->friend_list->get_all($data['profile']['id']);
                    $data['friends'] = array();

                    foreach ($friends as $friend) {
                        if ($friend['user_id'] == $data['profile']['id']) {
                            $friend['user_info'] = $this->user->find_id($friend['user_id_to']);
                        } else {
                            $friend['user_info'] = $this->user->find_id($friend['user_id']);
                        }

                        if($friend['user_info']) {
                            $friend['is_friend'] = $this->friend_list->is_friend($data['user']['id'], $friend['user_info']['id']);
                            
                            if (!$friend['is_friend']) {
                                $friend['is_request'] = $this->friend_request->have_request($data['user']['id'], $friend['user_info']['id']);
                            }

                            $data['friends'][] = $friend;
                        }
                    }

                    // get favorite
                    $data['favorites'] = array();
                    $favorites = $this->favorite->get_all($data['profile']['id']);

                    foreach ($favorites as $favorite) {
                        $favorite['user_info'] = $this->user->find_id($favorite['user_id_to']);
                        if ($favorite['user_info']) {
                            $favorite['is_friend'] = $this->friend_list->is_friend($data['user']['id'], $favorite['user_info']['id']);

                            if (!$favorite['is_friend']) {
                                $favorite['is_request'] = $this->friend_request->have_request($data['user']['id'], $favorite['user_info']['id']);
                            }

                            $favorite['is_favorite'] = $this->favorite->is_favorite($data['user']['id'], $favorite['user_info']['id']);
                            $data['favorites'][] = $favorite;
                        }
                    }
                    
                }
                
                // get conversation
                if (($data['user']['group_id'] == 1) && ($data['user']['id'] != $data['profile']['id']) && ($data['profile']['group_id'] != 1)) {
                    $message_service = new MessageService();
                    $data['conversations'] = $message_service->conversations($data["profile"]["id"]);
                }
                
                // get group
                $groups = $this->group->get();
                $data['groups'] = $groups;
                
                // get image
                $image_service = new ImageService();
                $data['images'] = $image_service->index($data["user"]["id"], $data["profile"]["id"]);
            }
        } catch (CheckException $e) {
            $data['error'] = true;
            $data['message'] = $e->getMessage();
        } catch (UserException $e) {
            redirect();
        }
        
        $this->_view->load_content('friend.view', $data);
    }

    /**
     * action suggest user
     *
     */
    public function suggest()
    {   
        $data = $this->_data;
        try {
            
            if (isset($data['error'])) {
                throw new UserException("Please login");
            }
            
            $friend_service = new FriendService();
            $suggest_data = $friend_service->suggest($data["user"]["id"]);


            if($suggest_data["error"]) {
                throw new Exception($suggest_data["message"]);
            }

            $data["users"] = $suggest_data["users"];

        } catch (CheckException $e) {
            $data['message'][]=$e->getMessage();
        } catch (UserException $e) {
            redirect();
        }
        
        $this->_view->load_content('friend.suggest', $data);
    }

    /**
     * action view all friend request
     *
     */
    public function request()
    {
        $data = $this->_data;
        try {
            if (isset($data['error'])) {
                throw new UserException("Please login");
            }
            
            $friend_service = new FriendService();
            $request_data = $friend_service->request($data["user"]["id"]);


            if($request_data["error"]) {
                throw new Exception($request_data["message"]);
            }

            $data["users"] = $request_data["users"];
        } catch (CheckException $e) {
            $data['message'][] = $e->getMessage();
        } catch (UserException $e) {
            redirect('');
        }
        
        $this->_view->load_content('friend.request', $data);

    }


=======
<?php
namespace App\Controller;

use \Exception;
use App\Exception\UserException;
use App\Exception\CheckException;

/**
 * This is a class FriendController
 */
class FriendController extends Controller
{   
    public function __construct()
    {   
        parent::__construct();
        
        $this->_model->load('favorite');
        $this->_model->load('group');
        $this->_model->load('image');
        $this->_model->load('image_like');
    }

    /**
     * action friend list
     *
     */
    public function index()
    {   
        try {
            $data = $this->_data;

            if (isset($data['error'])) {
                throw new UserException("Please login");
            }

            $list_friends = $this->friend_list->get_all($data['user']['id']);
            
            if ($list_friends) {
                foreach ($list_friends as $key => $friend) {
                    
                    if ($friend['user_id'] == $data['user']['id']) {
                        $friend['user'] = $this->user->find_id($friend['user_id_to']);
                    } else {
                        $friend['user'] = $this->user->find_id($friend['user_id']);
                    }
                    
                    if ($friend['user']) {
                        $data['list_friends'][$key] = $friend;
                    }
                }
            }
        } catch (UserException $e) {
            redirect();
        }
        
        $this->_view->load_content('friend.list', $data);
    }

    /**
     * action view friend detail
     *
     */
    public function view($params)
    {   
        $data = $this->_data;
        try {
            
            if (isset($data['error'])) {
                throw new UserException("Please login");
            }
            
            if (isset($params[0])) {
                $id = $params[0];
                $data['permisson'] = false;
                
                // get info
                if ($id == $data['user']['id']){
                    $data['is_owner'] = true;
                    $data['profile'] = $data['user'];
                    $data['permisson'] = true;
                } else {
                    $data['is_owner'] = false;
                    $user = $this->user->find_id($id);
                    if (!$user) {
                        throw new CheckException("Not exist user");
                    }
                    $data['profile'] = $this->user->find_id($id);
                }
                
                // check relation
                $result = $this->friend_list->is_friend($id, $data['user']['id']);
                
                if ($result) {
                    $data['is_friend'] = true;
                    $data['permisson'] = true;
                } elseif ($data['user']['group_id'] ==1) {
                    $data['is_friend'] = false;
                    $data['permisson'] = true;
                } elseif (($data['user']['group_id'] == $data['profile']['group_id']) && ($data['profile']['id'] != $data['user']['id'])) {
                    $data['permisson'] = true;
                    $data['is_friend'] = false;
                } else {
                    $data['is_friend'] = false;
                }
                
                if (!$data['is_friend']) {
                    $data['is_request'] = $this->friend_request->have_request($data['user']['id'], $data['profile']['id']);
                }
                
                // check favorite & follow
                $data['is_favorite'] = $this->favorite->is_favorite($data['user']['id'], $id);
                $data['is_follow'] = $this->follow->is_follow($data['user']['id'], $id);
                
                // get message
                if ($data['user']['id'] != $data['profile']['id']) {
                    $messages = $this->message_log->get_message_user($id, $data['user']['id']);
                    $data['message_log']=$messages;
                }
                
                // get friend
                if ($data['is_owner'] || $data['is_friend']) {
                    $friends = $this->friend_list->get_all($data['profile']['id']);
                    $data['friends'] = array();

                    foreach ($friends as $friend) {
                        if ($friend['user_id'] == $data['profile']['id']) {
                            $friend['user_info'] = $this->user->find_id($friend['user_id_to']);
                        } else {
                            $friend['user_info'] = $this->user->find_id($friend['user_id']);
                        }

                        if($friend['user_info']) {
                            $friend['is_friend'] = $this->friend_list->is_friend($data['user']['id'], $friend['user_info']['id']);
                            
                            if (!$friend['is_friend']) {
                                $friend['is_request'] = $this->friend_request->have_request($data['user']['id'], $friend['user_info']['id']);
                            }

                            $data['friends'][] = $friend;
                        }
                    }

                    // get favorite
                    $data['favorites'] = array();
                    $favorites = $this->favorite->get_all($data['profile']['id']);

                    foreach ($favorites as $favorite) {
                        $favorite['user_info'] = $this->user->find_id($favorite['user_id_to']);
                        if ($favorite['user_info']) {
                            $favorite['is_friend'] = $this->friend_list->is_friend($data['user']['id'], $favorite['user_info']['id']);

                            if (!$favorite['is_friend']) {
                                $favorite['is_request'] = $this->friend_request->have_request($data['user']['id'], $favorite['user_info']['id']);
                            }

                            $favorite['is_favorite'] = $this->favorite->is_favorite($data['user']['id'], $favorite['user_info']['id']);
                            $data['favorites'][] = $favorite;
                        }
                    }
                    
                }
                
                // get conversation
                if (($data['user']['group_id'] == 1) && ($data['user']['id'] != $data['profile']['id']) && ($data['profile']['group_id'] != 1)) {
                    $conversations = $this->message_log->get_all_con($data['profile']['id']);
                    
                    foreach ($conversations as $key => $value) {
                        $conver_user = $this->user->find_id($value);
                        $data['conversations'][$key] = array('id' => $value, 'fullname' => $conver_user['fullname']);
                    }
                }
                
                // get group
                $groups = $this->group->get();
                $data['groups'] = $groups;
                
                // get image
                $images = $this->image->get_all($data['profile']['id']);
                $data['images'] = [];
                
                foreach ($images as $image) {
                    
                    if ($this->image_like->is_like($data['user']['id'], $image['id'])) {
                        $image['is_like'] = true;
                    } else {
                        $image['is_like'] = false;
                    }

                    $image['like'] = $this->image_like->count_all($image['id']);
                    $data['images'][] = $image;
                }
            }
        } catch (CheckException $e) {
            $data['error'] = true;
            $data['message'] = $e->getMessage();
        } catch (UserException $e) {
            redirect();
        }
        
        $this->_view->load_content('friend.view', $data);
    }

    /**
     * action suggest user
     *
     */
    public function suggest()
    {   
        $data = $this->_data;
        try {
            
            if (isset($data['error'])) {
                throw new UserException("Please login");
            }
            
            $result = $this->friend_list->suggest_friend($data['user']['id']);
            
            if (!$result) {
                throw new CheckException("Not found");
            }
            
            foreach ($result as $key => $value) {   
                $user_request = $this->friend_request->have_request($id, $value['id']);
                $value['request_status'] = $user_request ? true : false;
                $data['users'][$key] = $value;
            }
        } catch (CheckException $e) {
            $data['message'][]=$e->getMessage();
        } catch (UserException $e) {
            redirect();
        }
        
        $this->_view->load_content('friend.suggest', $data);
    }

    /**
     * action view all friend request
     *
     */
    public function request()
    {
        $data = $this->_data;
        try {
            if (isset($data['error'])) {
                throw new UserException("Please login");
            }
            
            $user_request = $this->friend_request->where('user_id_to', $data['user']['id'])->get();
            
            if (!$user_request) {
                throw new CheckException("Not have friend request");
            }
            
            foreach ($user_request as $key => $value) {
                $user_info = $this->user->where('id',$value['user_id'])->first();

                if ($user_info) {
                    $value['user_info'] = $user_info;
                    $data['users'][$key] = $value;
                }
            }
        } catch (CheckException $e) {
            $data['message'][] = $e->getMessage();
        } catch (UserException $e) {
            redirect('');
        }
        
        $this->_view->load_content('friend.request', $data);

    }


>>>>>>> Stashed changes
}