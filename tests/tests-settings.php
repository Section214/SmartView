<?php
/**
 * @group smartview_settings
 */
class Test_Settings extends WP_UnitTestCase {
    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function test_get_settings_tabs() {
        $this->assertArrayHasKey( 'general', smartview_get_settings_tabs() );
        $this->assertArrayHasKey( 'modal', smartview_get_settings_tabs() );
    }

    public function test_get_registered_settings() {
        $this->assertArrayHasKey( 'general', smartview_get_registered_settings() );
        $this->assertArrayHasKey( 'modal', smartview_get_registered_settings() );
    }

    public function test_get_option() {
        global $smartview_options;

        $smartview_options['my_opt'] = 'option';
        update_option( 'smartview_settings', $smartview_options );

        $this->assertFalse( smartview_get_option( 'fake_opt', false ) );
        $this->assertEquals( 'option', smartview_get_option( 'my_opt', false ) );
    }

    public function test_get_settings() {
        $this->assertEmpty( smartview_get_settings() );
    }

    public function test_sanitize_text_field() {
        $input = 'Test string ';

        $this->assertEquals( 'Test string', smartview_sanitize_text_field( $input ) );
    }
}
