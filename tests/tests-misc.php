<?php
/**
 * @group smartview_misc
 */
class Test_Misc extends WP_UnitTestCase {
    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function test_get_types() {
        $this->assertArrayHasKey( 'post', smartview_get_types() );
        $this->assertArrayHasKey( 'page', smartview_get_types() );
    }
}