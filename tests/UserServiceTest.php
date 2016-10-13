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
        $this->assertEquals(['error' => false], $result, var_dump($result));
        
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
}