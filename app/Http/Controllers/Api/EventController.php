<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use PhpParser\Node\Expr\Cast\Bool_;

class EventController extends Controller implements HasMiddleware
{
    use CanLoadRelationships;

    private array $relations = ['user', 'attendees', 'attendees.user'];

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']) , //applies authentication for creating updating and deleting 
            new Middleware('throttle:60,1', only: ['store','destroy','update'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $query = $this->loadRelationships(Event::query());

        return EventResource::collection($query->latest()->paginate());
        // return EventResource::collection(Event::with('user')->paginate());    //without any specific relations
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        // Add the user_id to the validated data
        $validatedData['user_id'] = $request->user()->id;

        // Create the event with the validated data
        $event = Event::create($validatedData);
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        // $event->load('user','attendees');
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        //Gate::authorize('modify',$event);                //using policy
        // if (Gate::denies('update-event', $event))     //using gates .you should writebthe methods in appserviceprovider
        // {
        //     abort(403, 'You are not authorized to update this event');
        // }

        //$this->authorize('update-event',$event);  //simple method for the above lines

        $event->update(
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'sometimes|date',
                'end_time' => 'sometimes|date|after:start_time'
            ])
        );
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // if (Gate::denies('update-event', $event))     //using gates .you should writebthe methods in appserviceprovider
        // {
        //     abort(403, 'You are not authorized to update this event');
        // }
       // Gate::authorize('modify',$event);
        $event->delete();
        return response(status: 204);      //using policy
    }
}
