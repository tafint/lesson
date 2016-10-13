<?php
use PHPUnit\Framework\TestCase;
use App\Service\HeaderService;
/**
 * this is class test HeaderServie
 */
class HeaderServiceTest extends TestCase
{	
    public function testLoadHeaderData()
    {	
        $header_service = new HeaderService();
        $id = 1;
        $result = $header_service->load_data($id);
        $this->assertArraySubset(['error' => false], $result);
        
    }
}