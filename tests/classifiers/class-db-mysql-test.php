<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class mysql_db_source_test extends TestCase
{
	public function testCreateXmlSource(): void
	{ 
		$obj = new source();
        $this->assertNotNull($obj);
    }

	public function testLoadSourcesAndAccess(): void
	{ 
        $dbid = "TESTDB";
        $db_src = "mysql:root:root@192.168.0.1/test";
        $obj = new source();
		$obj->add_source($dbid, $db_src);
        
        $result = $obj->get("//${dbid}/persons/1/name");

        $this->assertEquals("30", $result);
    }

    
}
