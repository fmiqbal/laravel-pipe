<?php

namespace Fikrimi\Pipe\Tests\Feature;

use Fikrimi\Pipe\Models\Credential;
use Fikrimi\Pipe\Tests\TestCase;

class CredentialTest extends TestCase
{
    public function test_that_auth_is_encrypted_when_saved()
    {
        $credData = factory(Credential::class)->make();
        $cred = (clone $credData);
        $cred->save();

        $this->assertEquals(
            $credData->auth,
            \Illuminate\Support\Facades\Crypt::decrypt($cred->auth)
        );

        $this->assertEquals(
            $credData->auth,
            $cred->auth_decrypted,
        );
    }

    public function test_fingerprint_is_created_when_saved()
    {
        $credData = factory(Credential::class)->create();

        /**
         * @url https://stackoverflow.com/questions/19989481/how-to-determine-if-a-string-is-a-valid-v4-uuid
         */
        $UUIDv4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

        $this->assertRegExp($UUIDv4, $credData->fingerprint);
    }
}
