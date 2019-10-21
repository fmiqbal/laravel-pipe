<?php

namespace Fikrimi\Pipe\Tests\Feature;

use Fikrimi\Pipe\Models\Credential;
use Fikrimi\Pipe\Tests\TestCase;

class CredentialModuleTest extends TestCase
{
    /** @var Credential */
    private $credentialOwned;

    /** @var Credential */
    private $credentialOther;

    public function setUp(): void
    {
        parent::setUp();

        $this->credentialOwned = factory(Credential::class)->create();
        $this->credentialOther = factory(Credential::class)->create();

        $this->credentialOwned->setCreator($this->user)->save();
        $this->credentialOther->setCreator(factory(\Illuminate\Foundation\Auth\User::class)->create())->save();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_index_can_be_accessed()
    {
        $this->checkAuth('credentials');

        $this->get('credentials')
            ->assertStatus(200)
            ->assertSeeTextInOrder([
                'CREDENTIALS',
                'new credentials',
            ]);

        // Check for view other module
        config([
            'pipe.auth.policies.credentials.view_other' => true,
        ]);

        $this->get('credentials')
            ->assertSee($this->credentialOwned->username)
            ->assertSee($this->credentialOther->username);
    }

    public function test_index_not_showing_other_cred()
    {
        $this->checkAuth('credentials');

        config([
            'pipe.auth.policies.credentials.view_other' => false,
        ]);

        $this->get('credentials')
            ->assertSee($this->credentialOwned->username)
            ->assertDontSee($this->credentialOther->username);
    }

    public function test_create_can_be_accessed()
    {
        $this->checkAuth('credentials/create');

        $this->get('credentials/create')
            ->assertSeeTextInOrder([
                'NEW CREDENTIAL',
                'Username',
            ]);
    }

    public function test_user_can_create_new_credential()
    {
        $this->actingAs($this->user);

        $credential = factory(Credential::class)->make();

        $this->post('credentials', $credential->toArray())
            ->assertRedirect('credentials');

        $this->get('credentials')
            ->assertSee($credential['username']);
    }

    public function test_user_can_delete_credential()
    {
        $this->actingAs($this->user);

        config([
            'pipe.auth.policies.credentials.delete_other' => false,
        ]);

        $cred = factory(Credential::class)->create();

        $this->delete('credentials/' . $cred->id)
            ->assertRedirect('credentials')
            ->assertDontSee($cred->username);
    }
}
