<?php

namespace App\Models;

use Illuminate\Http\Request;

class PIEditFacet extends Facet
{
    /**
     * Inverse relation of EditFacet for specific PI
     *
     * @return [type] [description]
     */
    public function target()
    {
        return $this->belongsTo(PI::class);
    }

    public function show()
    {
        $ocapListFacet = $this->target->mediaOcapListFacets->first();

        return $this->jsonResponse([
            'type' => 'PIEditFacet',
            'url' => route('obj.show', ['obj' => $this->id]),
            'view_facet' => route('obj.show', ['obj' => $this->target->viewFacet->id]),
            'data' => [
                'title' => $this->target->title,
                'description' => $this->target->description,
                'address' => $this->target->address,
                'medias' => ($ocapListFacet != null)
                    ? route('obj.show', ['obj' => $ocapListFacet->id]) : null
            ]
        ]);
    }

    public function httpUpdate(Request $request)
    {
        $success = false;
        $new_data = intersectFields(['title', 'description', 'address'], $request->all());
        $tested_data = array_filter($new_data, function ($value, $key) {
            $tests = [
                'title' => is_string($value),
                'description' => is_string($value),
                'address' => is_string($value)
            ];

            return $tests[$key];
        }, ARRAY_FILTER_USE_BOTH);

        if ($request->has('medias') && is_string($request->medias)) {
            $listFacet = Facet::find(getSwissNumberFromUrl($request->medias));
            if ($listFacet == null) {
                return $this->badRequest();
            } else {
                $this->target->mediaOcapListFacets()->detach();
                $this->target->mediaOcapListFacets()->save($listFacet);
                $success = true;
            }
        }

        if (empty($tested_data) && !$success) {
            return $this->badRequest();
        } else {
            $this->target->update($tested_data);
            $success = true;
        }

        return $success ? $this->noContent() : $this->badRequest();
    }

    public function httpDestroy()
    {
        return $this->deleteEverything();
    }

    public function deleteDependentFacets() {
        $this->target->viewFacet->delete();
    }
}
