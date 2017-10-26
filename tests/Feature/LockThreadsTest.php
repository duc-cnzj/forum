<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function non_administrators_may_not_lock_threads()
    {
        $this->withExceptionHandling();
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread))->assertStatus(403);

        $this->assertFalse(! ! $thread->fresh()->locked);
    }

    /** @test */
    function administrators_can_lock_threads()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Thread');
        $this->post(route('locked-threads.store', $thread));
        $this->assertTrue(!! $thread->fresh()->locked, 'failed');

    }

    /** @test */
    function once_locked_a_thread_may_not_receive_replies()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $thread->lock();

        $this->post($thread->path() . '/replies', [
            'body'    => 'fix',
            'user_id' => create('App\User')->id,
        ])->assertStatus(422);

    }
}