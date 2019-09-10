<?php

namespace Tests\Feature;

use App\Audio,
    App\AudioViewFacet,
    App\AudioEditFacet;

use App\AudioList,
    App\AudioListViewFacet,
    App\AudioListEditFacet;

use Illuminate\Http\Request;

use Eris\Generator,
    Eris\TestTrait;

use Tests\TestCase;

class ModelTest extends TestCase
{
    use TestTrait;
    use RefreshDatabase;

    /** @test */
<<<<<<< HEAD
    public function audio_multiple_add_and_delete()
=======
    public function audioEditFacetGetView()
    {
        $audio = Audio::create(['extension' => 'mp3']);
        $audio_edit = AudioEditFacet::create(['id_audio' => $audio->id]);
        $audio_view = AudioViewFacet::create(['id_audio' => $audio->id]);

        $this->assertEquals($audio_view->getOriginal(), $audio_edit->getViewFacet()->getOriginal());
    }

    /** @test */
    public function audioMultipleAddAndDelete()
>>>>>>> Add tests for Audio - AudioList relationships
    {
        $this->limitTo(10)->forAll(Generator\nat(), Generator\nat())->then(function ($nb1, $nb2) {
            $nbr_add = ($nb1 > $nb2) ? $nb1 : $nb2;
            $nbr_del = ($nb1 < $nb2) ? $nb1 : $nb2;
            $audio_id_array = [];

            $count_before = Audio::all()->count();
            for ($i = 0; $i < $nbr_add; $i++) {
                $audio_id_array[] = Audio::create(['extension' => 'mp3']);
            }
            for ($j = 0; $j < $nbr_del; $j++) {
                $audio_id_array[$j]->delete();
            }
            $count_after = Audio::all()->count();

<<<<<<< HEAD
            $this->assertEquals($count_after - $count_before, $nbr_add - $nbr_del);
=======
            $this->assertEquals($count_after - $count_before, $nbr_add - $nbr_del, "With $count_after - $count_before and $nbr_add - $nbr_del");
        });
    }

    /** @test */
    public function objectListViewFacetAddToDB()
    {
        $this->limitTo(10)->forAll(Generator\nat())->then(function ($nbr) {
            $list = AudioList::create();
            $count_before = AudioListViewFacet::all()->count();
            for ($i = 0; $i < $nbr; $i++) {
                AudioListViewFacet::create(['id_list' => $list->id]);
            }
            $count_after = AudioListViewFacet::all()->count();
            $this->assertEquals($count_before + $nbr, $count_after, "With $count_before + $nbr and $count_after");
        });
    }

    /** @test */
    public function objectListEditFacetAddToDB()
    {
        $this->limitTo(10)->forAll(Generator\nat())->then(function ($nbr) {
            $list = AudioList::create();
            $count_before = AudioListEditFacet::all()->count();
            for ($i = 0; $i < $nbr; $i++) {
                AudioListEditFacet::create(['id_list' => $list->id]);
            }
            $count_after = AudioListEditFacet::all()->count();
            $this->assertEquals($count_before + $nbr, $count_after, "With $count_before + $nbr and $count_after");
>>>>>>> Add tests for Audio - AudioList relationships
        });
    }
}
