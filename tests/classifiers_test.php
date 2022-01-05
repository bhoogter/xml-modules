<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once(__DIR__ . "/logger-stub.php");

class classifiers_test extends TestCase
{
    public function testLoadDbSource()
    {
        $obj = new mysql_db_source();
        $this->assertNotNull($obj);
    }
}
