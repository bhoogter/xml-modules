<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class classifiers_test extends TestCase
{
    public function testLoadDbSource()
    {
        $obj = new mysql_db_source();
        $this->assertNotNull($obj);
    }
}
