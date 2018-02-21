<?php

namespace App\Http\Controllers;

use App\Article;
use App\Customer;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{

    protected function validator(array $data){
        return Validator::make($data, [
            'title' => 'required|string|min:1|max:255',
            'body' => 'required|string|min:1',
            'object' => 'required|string|min:1|max:255',
        ]);
    }

    protected function titleValidator(array $data){
        return Validator::make($data, [
            'title' => 'required|string|min:1|max:255',
            //'body' => 'required|string|min:1',
            //'object' => 'required|string|min:1|max:255',
        ]);
    }

    protected function bodyValidator(array $data){
        return Validator::make($data, [
            //'title' => 'required|string|min:1|max:255',
            'body' => 'required|string|min:1',
            //'object' => 'required|string|min:1|max:255:',
        ]);
    }

    protected function objectValidator(array $data){
        return Validator::make($data, [
            //'title' => 'required|string|min:1|max:255',
            //'body' => 'required|string|min:1',
            'object' => 'required|string|min:1|max:255',
        ]);
    }


    /*protected function publishOnValidator(array $data){
        return Validator::make($data, [
            //'title' => 'required|string|min:1|max:255',
            //'body' => 'required|string|min:1',
            'published_on' => 'required|string|min:1|max:255',
        ]);
    }

    protected function publishByValidator(array $data){
        return Validator::make($data, [
            //'title' => 'required|string|min:1|max:255',
            //'body' => 'required|string|min:1',
            'published_by' => 'required|integer',
        ]);
    }*/

    public function index(){
        return Article::all();
    }


    public function articleById($id){
        return Article::find($id);
    }

    protected function create(array $data)
    {
        //date("Y-m-d H:i:s", time())
         $article = Article::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'object' => $data['object'],
            'created_by' => Auth::id()
        ]);

        Log::create([
            'user_id' =>  Auth::id(),
            'article_id' => $article->id,
            'action' => 'Create Article',
        ]);

        //['user_id', 'article_id', 'action', 'created_at', 'updated_at']

         return $article;
    }

    public function newArticle(Request $request){

        $this->validator($request->all())->validate();


        return $this->create($request->all());

    }

    public function updateTitleArticle($request){

        $this->titleValidator($request)->validate();

        $id = $request['id'];

        $article = Article::find($id);
        $article->title = $request['title'];
        $article->save();

        Log::create([
            'user_id' =>  Auth::id(),
            'article_id' => $article->id,
            'action' => 'Update Article Title',
        ]);

        //['user_id', 'article_id', 'action', 'created_at', 'updated_at']

        return $article;
    }

    public function updateArticle(Request $request){
        //return $request->input('title');
        $inputContent = file_get_contents("php://input");
        //return $inputContent;
        $put_vars = json_decode($inputContent, true);
        //parse_str($inputContent,$put_vars);
        //return $put_vars['body'];
        $retVal = null;
        if ($put_vars['title']){
            $retVal = $this->updateTitleArticle($put_vars);
        }

        if ($put_vars['body']){
            $retVal = $this->updateBodyArticle($put_vars);
        }

        if ($put_vars['object']){
            $retVal = $this->updateObjectArticle($put_vars);
        }

        return $retVal;
    }

    public function updateBodyArticle($request){

        $this->bodyValidator($request)->validate();

        $id = $request['id'];

        $article = Article::find($id);
        $article->body = $request['body'];
        $article->save();

        Log::create([
            'user_id' =>  Auth::id(),
            'article_id' => $article->id,
            'action' => 'Update Article Body',
        ]);

        //['user_id', 'article_id', 'action', 'created_at', 'updated_at']

        return $article;
    }

    public function updateObjectArticle($request){

        $this->objectValidator($request)->validate();

        $id = $request['id'];

        $article = Article::find($id);
        $article->object = $request['object'];
        $article->save();

        Log::create([
            'user_id' =>  Auth::id(),
            'article_id' => $article->id,
            'action' => 'Update Article Object',
        ]);

        //['user_id', 'article_id', 'action', 'created_at', 'updated_at']

        return $article;
    }

    public function publishArticle($id){
        $article = Article::find($id);
        $article->published_on = date("Y-m-d H:i:s", time());
        $article->published_by =  Auth::id();
        //return $article;
        return $this->sendMail($article);
    }

    public function sendMail($article){
        $headers = "From: idea-cm.club <didnkallaehawe@idea-cm.club>\r\n";
        $headers .= "Reply-To: idea-cm.club <didnkallaehawe@idea-cm.club>\r\n";
        $headers .= "Return-Path: didnkallaehawe@idea-cm.club\r\n";
        $headers .= "CC: Felicien <felicien.fotiomanfo@gmail.com>\r\n";
        $headers .= "BCC: Nkalla <didnkallaehawe@gmail.com>\r\n";
        $headers .= "Organization: idea-cm.club\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "X-Priority: 3\r\n";
        $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;

        $message = "<html><body style=\"font-family: 'DejaVu Sans'\">";
        $message .= "<img src=\"{{asset('images/logo/bureauvalleelogo.png')}}\" alt=\"BUREAU VALLEE\" />";
        $message .='<div>
                    <h3><strong>' . $article->title . '</strong></h3>
                    <hr>
                    ' . $article->body . '
                    </div></body></html>';
            /*$message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
            $message .= "<tr style='background: #eee;'><td><strong>Name:</strong> </td><td>" . strip_tags($_POST['req-name']) . "</td></tr>";
            $message .= "<tr><td><strong>Email:</strong> </td><td>" . strip_tags($_POST['req-email']) . "</td></tr>";
            $message .= "<tr><td><strong>Type of Change:</strong> </td><td>" . strip_tags($_POST['typeOfChange']) . "</td></tr>";
            $message .= "<tr><td><strong>Urgency:</strong> </td><td>" . strip_tags($_POST['urgency']) . "</td></tr>";
            $message .= "<tr><td><strong>URL To Change (main):</strong> </td><td>" . $_POST['URL-main'] . "</td></tr>";
            $addURLS = $_POST['addURLS'];
            if (($addURLS) != '') {
                $message .= "<tr><td><strong>URL To Change (additional):</strong> </td><td>" . strip_tags($addURLS) . "</td></tr>";
            }
            $curText = htmlentities($_POST['curText']);
            if (($curText) != '') {
                $message .= "<tr><td><strong>CURRENT Content:</strong> </td><td>" . $curText . "</td></tr>";
            }
            $message .= "<tr><td><strong>NEW Content:</strong> </td><td>" . htmlentities($_POST['newText']) . "</td></tr>";
            $message .= "</table>";
            $message .= "</body></html>";*/

            $retval = array();
            $success = array();
            $failed = array();
        $customers = Customer::all();
        //return $customers;
        foreach($customers as  $customer){
            if (mail($customer->email,$article->object,$message,$headers)) {
                array_push($success, $customer);
            } else {
                array_push($failed, $customer);
            }
        }

        $article->save();

        Log::create([
            'user_id' =>  Auth::id(),
            'article_id' => $article->id,
            'action' => 'Publish Article',
        ]);

        //['user_id', 'article_id', 'action', 'created_at', 'updated_at']

        return array('success' => $success, 'failed' => $failed);

    }

}
