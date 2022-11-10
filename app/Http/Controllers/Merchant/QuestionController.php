<?php

namespace App\Http\Controllers\Merchant;
use App;
use App\Models\Configuration;
use App\Models\LanguageQuestion;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function index()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $questions = Question::where([['merchant_id', '=', $merchant_id]])->get();
        return view('merchant.question.index', compact('questions', 'config'));
    }

    public function create()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        return view('merchant.question.create');
    }

    public function store(Request $request)
    {
        $merchant_id = get_merchant_id();
        $question = Question::create([
            'question' => $request->question,
            'merchant_id' => $merchant_id,
        ]);
        $this->SaveLanguageQuestion($merchant_id, $question->id, $request->question);
        return redirect()->back()->with('questionadded', 'Question Added');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $question = Question::where([['id', '=', $id]])->first();
        return view('merchant.question.edit', compact('question', 'config'));
    }

    public function update(Request $request, $id)
    {
        $merchant_id = get_merchant_id();
        $question = Question::where([['id', '=', $id]])->first();
        // print_r($question); die();
        $question->question = $request->question;
        $question->save();
        $this->SaveLanguageQuestion($merchant_id, $question->id, $request->question);
        return redirect()->back()->with('questionadded', 'Question Updated');
    }

    public function destroy($id)
    {
        //
    }
    
    public function SaveLanguageQuestion($merchant_id, $question_id, $question)
    {
        LanguageQuestion::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'question_id' => $question_id
        ], [
            'question' => $question,
        ]);
    }
}
