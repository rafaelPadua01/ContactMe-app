<?php
namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Message\MessagesController;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use App\Models\ProfileUser;


class ChatsController extends Controller {
    private $chats;

public function __construct(Chat $chats)
    {
        $this->chats  = $chats;
    }
    public function index($id){
        try{
            $talk = Chat::where('receiver_id', '=', $id)
            ->join('users', 'users.id', '=', 'chats.receiver_id')
            ->join('profile_users', 'profile_users.user_id', '=', 'users.id')
            ->join('profile_images', 'profile_images.user_id', '=', 'profile_users.user_id')
            ->get([
                'chats.*',
                'users.name',
                'profile_users.lastname',
                'profile_images.image_name',
            ]);
           
           
            return \Response::json($talk);
        }
        catch(Exception $e){
            return \Response::json($e);
        }
    }
    public function create(Request $request, $id){
        $profile_user = ProfileUser::where('user_id', '=', $id)->first();
        $chat = chat::where('receiver_id', '=', $profile_user->user_id)->first();
        
        $user = \Auth::user();
        if($chat){
            
                return MessagesController::send($request, $id);
                //echo "enviando mensangem...";
                
        }
        else{
            $insert_chat = Chat::create([
                    'receiver_id' => $id,
                    'sender_id' => $user->id,
                ]);
                if($insert_chat){
                    return MessagesController::send($request, $user);
                     
                }
                else{
                    echo "não foi possível enviar a mensagem...";
                    return false;
                }
            }
    }

    public function selectChat($id){
        try{
            $chat = Chat::findOrFail($id)
            ->join('users', 'users.id', '=', 'receiver_id')
            ->join('profile_users', 'profile_users.user_id', '=', 'users.id')
            ->join('profile_images', 'profile_images.user_id', '=', 'profile_users.user_id')
            ->get([
                'chats.*',
                'users.name',
                'profile_users.lastname',
                'profile_images.image_name',
               
            ]);
            return \Response::json($chat);
        }
        catch(Exception $e){
            return \Response::json($e);
        }
    }
    public function changeColor(Request $request, $id){
        try{
            
            $chat = Chat::where('id', '=', $id)->update(['color' => $request->colorPicker]);
            return \Response::json($request->colorPicker);
        }
        catch(Exception $e){
            return \Response::json($e);
        }
        
    }
    
}