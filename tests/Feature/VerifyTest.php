<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VerifyTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_form_page_can_be_rendered()
    {
        $response = $this->get('post-data');

        $response->assertStatus(200);
    }

    public function test_log_table_page_can_be_rendered()
    {
        $response = $this->get('log');

        $response->assertStatus(200);
    }

    public function test_data_is_verified(){
        $mockJson = '"data": {
            "id": "63c79bd9303530645d1cca00",
            "name": "Certificate of Completion",
            "recipient": {
              "name": "Marty McFly",
              "email": "marty.mcfly@gmail.com"
            },
            "issuer": {
              "name": "Accredify",
              "identityProof": {
                "type": "DNS-DID",
                "key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller",
                "location": "ropstore.accredify.io"
              }
            },
            "issued": "2022-12-23T00:00:00+08:00"
          },
          "signature": {
            "type": "SHA3MerkleProof",
            "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
          }
        }';
        
        $this->post('verify', ["load"=>$mockJson])->assertJsonFragment(["result"=>"verified"]);
    }

}
