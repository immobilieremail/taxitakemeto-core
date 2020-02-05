<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use MannikJ\Laravel\SingleTableInheritance\Traits\SingleTableInheritance;

class Facet extends Model
{
    use SingleTableInheritance;

    /**
     * The controller that triggered this facet
     */
    protected $controller;

    /**
     * Setup to use string primary keys
     * @var boolean
     */
    public      $incrementing   = false;
    protected   $keyType        = 'string';

    /**
     * Swiss model have string casted PrimaryKey
     * @var [type]
     */
    protected   $cast           = [
        'id'    => 'string'
    ];

    /**
     * Specific table to use
     * @var string
     */
    protected   $table          = 'facets';

    /**
     * Fillable data
     * @var array
     */
    protected $fillable         = [
        'id', 'target_id', 'type'
    ];

    /**
     * Constructor for eloquent model hierarchy
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes($attributes);
        $this->ensureTypeCharacteristics();
        $this->setSwissNumber();
    }

    public function setSwissNumber()
    {
        $this->id = swissNumber();
    }

    /**
     * Responses to HTTP methods
     */

    public function show() {
        return $this->response('Method Not Allowed', 405);
    }

    public function store(Request $request) {
        return $this->response('Method Not Allowed', 405);
    }

    public function httpUpdate(Request $request) {
        return $this->response('Method Not Allowed', 405);
    }

    public function httpDestroy() {
        return $this->response('Method Not Allowed', 405);
    }


    /**
     * Delete the target and all dependent facets
     *
     * This is not called by default.
     */
    public function deleteEverything() {
        $this->deleteDependentFacets();
        $this->target->delete();
        $this->delete();
        return $this->response('', 204);
    }

    /**
     * Delete dependent facets
     *
     * By default, this does nothing but can be overloaded.
     */
    public function deleteDependentFacets() {
    }


    public function response($content, $status = 200) {
        return new Response($content, $status, []);
    }

    public function jsonResponse($jsonData, $status = 200) {
        return new JsonResponse($jsonData, $status, [], 0);
    }

    /**
     * Common simple responses
     */

    public function noContent() {
        return $this->response('', 204);
    }

    public function badRequest() {
        return $this->response('Bad request', 400);
    }

    public function serverError() {
        return $this->response('Server error', 500);
    }

    public function recipients()
    {
        return $this->sent_invitations->map(function($invitation) { return $invitation->recipient()->first(); });
    }

    public function sender()
    {
        if ($this->received_invitation !== null)
            return $this->received_invitation->sender()->first();
        return null;
    }

    public function sent_invitations()
    {
        return $this->hasMany(Invitation::class, 'sender');
    }

    public function received_invitation()
    {
        return $this->hasOne(Invitation::class, 'recipient');
    }



    /**
     * Inverse of relationship
     *
     * @return [type] [description]
     */
    public function target()
    {
        return null;
    }
}
