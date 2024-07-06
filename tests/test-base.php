<?php

class Activity_Map_Test_Base extends WP_UnitTestCase
{

    public function test_plugin_activated()
    {
        $this->assertTrue(is_plugin_active(PLUGIN_PATH));
    }

    public function test_getInstance()
    {
        $this->assertInstanceOf('AM_Main', AM_Main::instance());
    }
}
