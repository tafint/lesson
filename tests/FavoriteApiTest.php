<?php
use PHPUnit\Framework\TestCase;
use App\Controller\Api\FavoriteApiController;
use GuzzleHttp\Client;
/**
 * this is class test Favorite API
 */
class FavoriteApiTest extends TestCase
{	
	/**
     * @dataProvider additionProvider
     */
	public function testAdd($a, $b, $expected) 
	{	
		$this->assertEquals($expected, $a + $b);
	}

    public function testFavoriteAdd()
    {	
    	$client = new Client();
	    $data = array(
	        'user_id' => 5
	    );
	    $request = $client->request("POST", 'http://dev.lampart.com.vn/lesson/favorite/add', []);
	    var_dump(json_decode($request->getBody(), true));
	    $this->assertEquals(200, $request->getStatusCode());
    	//$this->expectOutputString($request->getStatusCode());
    }

    public function additionProvider()
    {
    	return [
            [0, 0, 0],
            [0, 1, 1],
            [1, 0, 1],
            [1, 1, 2]
        ];
    }
}