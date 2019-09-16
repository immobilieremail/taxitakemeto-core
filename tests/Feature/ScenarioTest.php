<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use GuzzleHttp\Client;

class ScenarioTest extends TestCase
{
    /** @test */
    public function createAudioListAndAccessToItsEditFacet()
    {
        print("\n\n\nTEST createAudioListAndAccessToItsEditFacet in tests/Feature/ScenarioTest.php\n");
        /** Send a POST request to /api/audiolist entry point : create an empty AudioList */
        $response_post_audiolist = $this->post('/api/audiolist');
        $response_post_audiolist->assertStatus(200);

        /** Get last request's JSON */
        $audiolist_post_json = json_decode($response_post_audiolist->getContent());
        print("\nPOST /api/audiolist:\n" . json_encode($audiolist_post_json) . "\n");

        /** Send a GET request to returned JSON's url field : access to AudioListEdit */
        $response_get_audiolist = $this->get($audiolist_post_json->url);
        $response_get_audiolist->assertStatus(200);

        /** Get last request's JSON */
        $audiolist_get_json = json_decode($response_get_audiolist->getContent() . "\n");
        print("\nGET $audiolist_post_json->url:\n" . json_encode($audiolist_get_json) . "\n");
    }

    /** @test */
    public function createAudioAndAddItToAudioList()
    {
        print("\n\n\nTEST createAudioAndAddItToAudioList in tests/Feature/ScenarioTest.php\n");
        /** Send a POST request to /api/audio entry point : create an audio */
        $client = new Client();
        $response_post_audio = $client->request('POST', 'http://localhost:8000/api/audio', [
            'multipart' => [
                [
                    'name' => 'audio',
                    'contents' => fopen("/home/louis/Musique/applause.wav", 'r')
                ]
            ]
        ]);
        $this->assertEquals($response_post_audio->getStatusCode(), 200);

        /** Get last request's JSON */
        $audio_post_json = json_decode($response_post_audio->getBody());
        print("\nPOST /api/audio:\n" . json_encode($audio_post_json) . "\n");

        /** Send a GET request to returned JSON's url field : access AudioEdit */
        $response_edit_get_audio = $this->get($audio_post_json->url);
        $response_edit_get_audio->assertStatus(200);

        $audio_edit_get_json = json_decode($response_edit_get_audio->getContent());
        print("\nGET $audio_post_json->url:\n" . json_encode($audio_edit_get_json) . "\n");

        /** Get AudioView id */
        $view_id = str_replace("/api/audio/", "", $audio_edit_get_json->view_facet);
        print("\nview_id:\n$view_id\n");

        /** Send a POST request to /api/audiolist entry point : create an empty AudioList */
        $response_post_audiolist = $this->post('/api/audiolist');
        $response_post_audiolist->assertStatus(200);

        /** Get last request's JSON */
        $audiolist_post_json = json_decode($response_post_audiolist->getContent());
        print("\nPOST /api/audiolist:\n" . json_encode($audiolist_post_json) . "\n");

        /** Send a GET request to returned JSON's url field : access to AudioListEdit */
        $response_get_audiolist = $this->get($audiolist_post_json->url);
        $response_get_audiolist->assertStatus(200);

        /** Get last request's JSON */
        $audiolist_get_json = json_decode($response_get_audiolist->getContent() . "\n");
        print("\nGET $audiolist_post_json->url:\n" . json_encode($audiolist_get_json) . "\n");

        /** SEND a PUT request to returned JSON's update field : update audiolist */
        $response_update_audiolist = $this->put($audiolist_get_json->update, ["data" => ["audios" => [["id" => $view_id]]]]);
        $response_update_audiolist->assertStatus(200);

        /** Get last request's JSON */
        $audiolist_update_json = json_decode($response_update_audiolist->getContent() . "\n");
        print("\nPUT $audiolist_get_json->update:\n" . json_encode($audiolist_update_json));
    }
}
