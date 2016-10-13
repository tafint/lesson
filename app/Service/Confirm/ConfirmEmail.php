<?php
namespace App\Service\Confirm;
use Model\User;
use Model\Token;
use \Exception;
/**
 * This is a class ConfirmEmail
 */
class ConfirmEmail extends Confirm
{   
    public function confirm()
    {   
        $user = new User();
        $user_info = $user->where('email', $this->_token['content'])->first();

        $result["status"] = true;
        if ($user_info) {
            $result = array("status" => false, "message" => "Email exists");
        } else {
            $user->update_id($this->_token['user_id'], array('email'=>$this->_token['content']));
            $token = new Token();
            $token->where('user_id', $this->_token['user_id'])->where('status', 0)->update(array('status' => 1));
            $result["message"] = "Change email success";
        }
        
        return $result;
    }
}