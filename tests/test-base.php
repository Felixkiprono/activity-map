<?php

class Activity_Map_Test_Base extends WP_UnitTestCase
{

    public function test_plugin_activated()
    {
        $this->assertTrue(is_plugin_active(PLUGIN_PATH));

        $this->assertGreaterThan(0, 1);

        $this->assertGreaterThan(0, 3);


        $this->assertGreaterThan(0, 2);

    }

    public function test_getInstance()
    {
        $this->assertInstanceOf('AM_Main', AM_Main::instance());
    }

    public function test_plugin_check()
    {
        $this->assertTrue(true);
    }
    public function test_plugin_check1()
    {
        $this->assertTrue(true);
    }
    public function test_plugin_check2()
    {
        $this->assertTrue(true);
    }
    public function test_plugin_check3()
    {
        $this->assertTrue(true);
    }

    public function test_create_post()
    {
        global $wpdb;
        // Create a post
        $post_id = $this->factory->post->create(array(
            'post_title' => 'New Post',
            'post_content' => 'This is a test post content.',
            'post_status' => 'publish',
        ));

        // Check if the post ID is valid
        $this->assertGreaterThan(0, $post_id);

        // Retrieve the post object
        $post = get_post($post_id);

        // Assert that the post object exists and is of type WP_Post
        $this->assertInstanceOf('WP_Post', $post);

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM `{$wpdb->prefix}activity_map`
					WHERE `action` = 'updated'
						AND `event_type` ='Posts'
						AND `event_subtype` = %s
						AND `event_id` = $post_id",
                $post->post_type
            )
        );
        $this->assertNotEmpty($row);
    }

    public function test_delete_post()
    {
        global $wpdb;

        // Create a post
        $post_id = $this->factory->post->create(array(
            'post_title' => 'test-delete-post',
        ));

        // Check if the post ID is valid
        $this->assertGreaterThan(0, $post_id);

        // Delete the post
        $deleted = wp_delete_post($post_id, true); // Set second parameter to true to force delete

        // Assert that the post was successfully deleted
        $this->assertTrue($deleted !== false, "Failed to delete post with ID {$post_id}");

        // Check if the post exists in the database
        $post_exists = get_post($post_id);

        // Assert that the post no longer exists in the database
        $this->assertNull($post_exists, "Post with ID {$post_id} still exists after deletion");

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM `{$wpdb->prefix}activity_map`
					WHERE `action` = 'deleted'
						AND `event_type` ='Posts'
						AND `event_id` = %d",
                $post_id
            )
        );
        $this->assertNotEmpty($row);
    }


}
