<?php
use PHPUnit\Framework\TestCase;
use App\Service\UserService;

/**
 * this is class test UserService
 */
class UserServiceTest extends TestCase
{   
    /**
     * @dataProvider dataRegistration
     */
    public function testRegistration($array)
    {    
        $user_service = new UserService();
        $result = $user_service->registration($array);
        $this->assertNotEmpty($result);
    }

    public function dataRegistration()
    {   
        $_SESSION['code_capcha'] = "----capcha----";
        return array(
            array( 
                array(
                    "fullname" => "Admin",
                    "username" => "admin34",
                    'code' => "----capcha----",
                    'email' => "admin34@gmail.com",
                    'password' => "aaaa",
                    're_password' => "aaaa",
                    'address' => "HCMC",
                    'sex' => 1,
                    'birthday' => "2016-02-02",
                )
            )
        );
    }

    /**
     * test login true
     */
    public function testLoginTrue()
    {   
        $user_service = new UserService();
        $data = array(
            "username" => "admin",
            "password" => "aaaa"
        );
        $result = $user_service->login($data);
        $this->assertArraySubset(["error" => false], $result);
        
    }

    /**
     * test login false
     */
    public function testLoginFalse()
    {   
        $user_service = new UserService();
        $data = array(
            "username" => "admin",
            "password" => "aaaaa"
        );
        $result = $user_service->login($data);
        $this->assertArraySubset(["error" => true], $result);
        
    }


    /**
     * test management
     */
    public function testManagement()
    {   
        $user_service = new UserService();
        $data = array(
            "group_id" => 1
        );
        $result = $user_service->manage($data);
        $this->assertNotEmpty($result);
        
    }

    /**
     * test search
     */
    public function testSearch()
    {   
        $user_service = new UserService();
        $id = 1;
        $key = "admin";
        $result = $user_service->search($id, $key);
        $this->assertNotEmpty($result);
        
    }

    /**
     * test Change Email
     */
    public function testEmail()
    {   
        $user_service = new UserService();
        $id = 1;
        $email = "thanh_tai@lampart-vn.com";
        $result = $user_service->change_email($id, $email);
        $this->assertNotEmpty($result);
        $this->assertArraySubset(["error" => true], $result);
        
    }

    /**
     * test Change Pasword
     */
    public function testChangePass()
    {   
        $user_service = new UserService();
        $data = array(
            "id" => 1,
            "password" => "aaaa",
            "new_password" => "aaaa",
            "confirm_password" => "aaaa"
        );
        $result = $user_service->change_password($data);
        $this->assertNotEmpty($result);
        $this->assertArraySubset(["error" => true], $result);
        
    }

    /**
     * test Change Profile
     */
    public function testChangeProfile()
    {   
        $user_service = new UserService();
        $id = 1;
        $data = array(
            "fullname" => "AAAdmin",
            "address" => "HCMC",
            "sex" => 1,
            "birthday" => "1991-01-01"
        );
        $result = $user_service->change_profile($id, $data);
        $this->assertNotEmpty($result);
        $this->assertArraySubset(["error" => false], $result);
        
    }

}