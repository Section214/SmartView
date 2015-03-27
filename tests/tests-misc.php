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

    public function test_get_title_tags() {
        $this->assertStringStartsWith( '<div class="smartview-tag-list">', smartview_get_title_tags() );
        $this->assertStringEndsWith( '</div>', smartview_get_title_tags() );
    }

    public function test_parse_title_tags() {
        $content = 'Test {sitename}';
        $parsed_content = 'Test Test Blog';

        $this->assertEquals( $parsed_content, smartview_parse_title_tags( $content ) );
    }

    public function test_check_sameorigin() {
        $this->assertTrue( smartview_check_sameorigin( 'http://google.com' ) );
        $this->assertFalse( smartview_check_sameorigin( 'http://ketv.com' ) );
    }
}
