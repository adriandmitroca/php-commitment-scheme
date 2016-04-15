<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

use App\Events\FirstCommitment;
use App\Events\SecondCommitment;
use App\Models\Message;
use Illuminate\Http\Request;

Route::group([ 'middleware' => 'web' ], function () {
    Route::get('/', function () {
        session()->put('client_id', wordwrap(strtoupper(str_random(16)), 4, '-', true));

        return view('chat');
    });

    Route::post('/chat/new', function (Request $request) {
        Validator::make($request->all(), [
            'content' => 'required'
        ]);

        $message            = new Message;
        $message->r1        = wordwrap(strtoupper(str_random(16)), 4, '-', true);
        $message->r2        = wordwrap(strtoupper(str_random(16)), 4, '-', true);
        $message->content   = $request->get('content');
        $message->hash      = hash('sha256', $message->r1 . $message->r2 . $message->content);
        $message->client_id = session('client_id');
        $message->save();

        event(new FirstCommitment($message));

        return $message;
    });

    Route::post('commitment/first', function (Request $request) {
        session()->push('queue', $request->all());
        $message = Message::findOrFail($request->id);

        event(new SecondCommitment($message));

        return [ 'success' => true ];
    });

    Route::post('commitment/second', function (Request $request) {
        $commitment = collect(session()->get('queue'))->where('id', $request->id)->first();
        $response   = '';
        $validated  = false;

        if ( ! count($commitment)) {
            return [ 'response' => '<p><i>Message was not found</i>' ];
        }

        if ($commitment['r1'] === $request->r1) {
            $response .= '<p><i>Old R1 is equal with new R1.</i></p>';
        } else {
            $response .= '<p><i>Old R1 is <strong>NOT</strong> equal with new R1.</i></p>';
        }

        $newHash = hash('sha256', $request->r1 . $request->r2 . $request->message);

        if ($commitment['hash'] === $newHash) {
            $response .= '<p><i>Old hash is equal with new hash.</i></p>';
            $validated = true;
        } else {
            $response .= '<p><i>Old hash is <strong>NOT</strong> equal with new hash.</i></p>';
        }

        $response .= "<p><i>New hash: {$newHash}</i>";

        $response .= '<p><i>Received <strong>';
        $response .= $validated ? '[VALIDATED]' : '[NOT VALIDATED]';
        $response .= '</strong> message:</i> ' . $request->message . '</p>';

        return [ 'response' => $response ];
    });
});