<?php

namespace Tests\Feature;

use App\Audio,
    App\AudioList,
    App\AudioListViewFacet,
    App\AudioListEditFacet;

use Illuminate\Http\Request;

use Eris\Generator,
    Eris\TestTrait;

use Tests\TestCase,
    Illuminate\Foundation\Testing\RefreshDatabase;

class ModelTest extends TestCase
{
    use TestTrait;

    public $path = "/storage/uploads/";
    public $extension = "mp3";

    /** @test */
    public function audioMultipleAddAndDelete()
    {
        $this->limitTo(10)->forAll(Generator\nat(), Generator\nat())->then(function ($nb1, $nb2) {
            $nbr_add = ($nb1 > $nb2) ? $nb1 : $nb2;
            $nbr_del = ($nb1 < $nb2) ? $nb1 : $nb2;
            $audio_id_array = array();

            $count_before = Audio::all()->count();
            for ($i = 0; $i < $nbr_add; $i++) {
                $audio = Audio::create(['path' => '/storage/uploads/', 'extension' => 'mp3']);
                array_push($audio_id_array, $audio);
            }
            for ($j = 0; $j < $nbr_del; $j++) {
                $audio_id_array[$j]->delete();
            }
            $count_after = Audio::all()->count();

            $this->assertEquals($count_after - $count_before, $nbr_add - $nbr_del, "With $count_after - $count_before and $nbr_add - $nbr_del");
        });
    }

    /** @test */
    public function audioListViewFacetAddToDB()
    {
        $this->limitTo(10)->forAll(Generator\nat())->then(function ($nbr) {
            $audiolist = AudioList::create();
            $count_before = AudioListViewFacet::all()->count();
            for ($i = 0; $i < $nbr; $i++) {
                AudioListViewFacet::create(['id_list' => $audiolist->id]);
            }
            $count_after = AudioListViewFacet::all()->count();
            $this->assertEquals($count_before + $nbr, $count_after, "With $count_before + $nbr and $count_after");
        });
    }

    /** @test */
    public function audioListEditFacetAddToDB()
    {
        $this->limitTo(10)->forAll(Generator\nat())->then(function ($nbr) {
            $audiolist = AudioList::create();
            $count_before = AudioListEditFacet::all()->count();
            for ($i = 0; $i < $nbr; $i++) {
                AudioListEditFacet::create(['id_list' => $audiolist->id]);
            }
            $count_after = AudioListEditFacet::all()->count();
            $this->assertEquals($count_before + $nbr, $count_after, "With $count_before + $nbr and $count_after");
        });
    }
}
