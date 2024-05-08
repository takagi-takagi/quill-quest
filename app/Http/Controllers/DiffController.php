<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jfcherng\Diff\Differ;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Factory\RendererFactory;
use Jfcherng\Diff\Renderer\RendererConstant;
use App\Models\Text;
use App\Models\Project;
use OpenAI\Laravel\Facades\OpenAI;

class DiffController extends Controller
{
    private function diff($oldBody,$newBody,$oldId,$newId) {
        $rendererName = 'SideBySide';
        $differOptions = [
            'context' => 3,
            'ignoreCase' => false,
            'ignoreLineEnding' => false,
            'ignoreWhitespace' => false,
            'lengthLimit' => 2000,
        ];
        $rendererOptions = [
            'detailLevel' => 'char',
            'language' => ['eng',['old_version' => $oldId],['new_version' => $newId]],
            'lineNumbers' => true,
            'separateBlock' => true,
            'showHeader' => true,
            'spacesToNbsp' => false,
            'tabSize' => 4,
            'mergeThreshold' => 0.8,
            'cliColorization' => RendererConstant::CLI_COLOR_AUTO,
            'outputTagAsString' => false,
            'jsonEncodeFlags' => \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
            'wordGlues' => [' ', '-'],
            'resultForIdenticals' => null,
            'wrapperClasses' => ['diff-wrapper'],
        ];
        $result = DiffHelper::calculate($oldBody, $newBody, $rendererName, $differOptions, $rendererOptions);
        return $result;
    }
 
    private function generateResponse($inputText) {
        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $inputText],
            ],
        ]);
        return $result['choices'][0]['message']['content'];
    }

    public function index(Request $request) {
        $projects = Project::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
        return view('diff.indexProject', compact('projects'));
    }

    public function storeProject(Request $request) {
        $messages = [
            'name.required' => 'イベント名は必須です。',
            'name.max' => 'イベント名は最大100文字までです。',
        ];
        $validated = $request->validate([
            'name' => 'required|max:100'
        ], $messages);
        $validated['user_id'] = auth()->id();
        $maxUserProjectId = Project::where('user_id', auth()->id())->max('user_project_id');
        $validated['user_project_id'] = $maxUserProjectId + 1;
        $project = Project::create($validated);
        return redirect()->route('project.index')->with('message', 'イベントを作成しました。');
    }

    public function storeText($data,$id,$queryNewId) {
        $projectId = Project::where('user_id', auth()->id())->where('user_project_id', $id)->first()->id;
        $data['project_id'] = $projectId;

        $maxProjectTextId = Text::where('project_id', $projectId)->max('project_text_id');
        $data['project_text_id'] = $maxProjectTextId + 1;
        
        $newId = Text::create($data)->project_text_id;
        
        $showData = ['id' => $id];
        if (isset($queryNewId)) {
            $queryData = [
                'old' => $queryNewId,
                'new' => $newId
            ];
            $showData = array_merge($showData, $queryData);
        }
        
        return redirect()->route('project.show', $showData);
    }

    public function storePlainText(Request $request,$id) {
        $messages = [
            'body.required' => 'テキストは必須です。',
            'body.max' => 'テキストは最大100文字までです。',
        ];
        $validated = $request->validate([
            'body' => 'required|max:100'
        ], $messages);
        $validated['is_posted'] = 1;
        $queryNewId = $request->query('new');
        return $this->storeText($validated,$id,$queryNewId)->with('message', 'テキストを作成しました。');
    }

    public function showProject(Request $request,$id) {
        // $dropdownOldId = $request->input('dropdownOldId', session('dropdownOldId'));
        // $dropdownNewId = $request->input('dropdownNewId', session('dropdownNewId'));
        // session(['dropdownOldId' => $dropdownOldId]);
        // session(['dropdownNewId' => $dropdownNewId]);

        $project = Project::where('user_id', auth()->id())->where('user_project_id', $id)->first();
        $data = [];
        $texts = Text::where('project_id', $project->id)->orderBy('created_at', 'desc')->get();
        $textsWithPagination = Text::where('project_id', $project->id)->orderBy('created_at', 'desc')->paginate(50);
        if ($request->has('old')) {
            $oldId = $request->query('old');
            $data['oldId'] = $oldId;
            $oldBody = $texts->firstWhere('project_text_id', $oldId)->body;
        } 
        // elseif (isset($texts[1])) {
        //     $old = $texts[1]->body;
        // }
        if ($request->has('new')) {
            $newId = $request->query('new');
            $newBody = $texts->firstWhere('project_text_id', $newId)->body;
            $data['newId'] = $newId;
        }
        // elseif (isset($texts[0])) {
        //     $new = $texts[0]->body;
        // }
        if (isset($oldBody, $newBody)){
            $data['html'] = $this->diff($oldBody,$newBody,$oldId,$newId);
        } else {
            $data['html'] = '<p class="border-2 border-gray-500 rounded p-2">ここに変更点が表示されます</p>';
        }
        $uniqueTypeTexts = Text::select('type', DB::raw('MAX(created_at) as latest_created_at'))
        ->whereNotNull('type')
        ->where('type', '<>', '')
        ->groupBy('type')
        ->orderBy('latest_created_at', 'desc')
        ->get();

        $newData = ['texts' => $texts,'project' => $project, 'textsWithPagination' =>$textsWithPagination, 'uniqueTypeTexts' => $uniqueTypeTexts];
        $data = array_merge($data, $newData);
        return view('diff.diff', $data);
    }

    public function setQuery(Request $request,$id) {
        $data['id'] = $id;
        if ($request->has('setToOld')) {
            $data['old'] = $request->setToOld;
        }
        if ($request->has('setToNew')) {
            $data['new'] = $request->setToNew;
        }
        if ($request->has('oldButton')) {
            $request->session()->flash('message','旧にID:'.$data['old']. 'をセットしました。');
        }
        if ($request->has('newButton')) {
            $request->session()->flash('message','新にID:'.$data['new']. 'をセットしました。');
        }
        return redirect()->route('project.show', $data);
    }

    public function storeChatText(Request $request,$id) {
        if ($request->has('newId')) {
            $queryNewId = $request->newId;
            $body = Text::find($queryNewId)->body;
        } else {
            $body = $request->body;
            $queryNewId = null;
        }
        if($request->typeNull == true) {
            $inputText='`'.$body.'`を添削して下さい。出力は本文のみでお願いします';
        } else {
            $messages = [
                'type.required' => '形式が入力されていません。',
                'type.max' => '形式は最大100文字までです。',
            ];
            $validated = $request->validate([
                'type' => 'required|max:100'
            ],$messages);
            $inputText='`'.$body.'`を文章の形式`'.$validated['type'].'`として添削して下さい。出力は本文のみでお願いします';
        }
        $newBody = $this->generateResponse($inputText);
        $data = [
            'body' => $newBody,
            'is_posted' => 0,
        ];
        if(isset($validated['type'])) {
            $data['type'] = $validated['type'];
        }
        return $this->storeText($data,$id,$queryNewId)->with('message', 'テキストを生成しました。');
    }

    public function destroyProject(Project $project) {
        $project->delete();
        return back()->with('message', 'ID:' . $project->user_project_id . ' ' . $project->name . 'を削除しました。');
    }

    public function destroyText(Text $text) {
        $text->delete();
        return back()->with('message', 'ID:' . $text->project_text_id . 'を削除しました。');
    }

    public function checkAndStore(Request $request,$id) {
        $messages = [
            'textFormType.required' => '形式が選択されていません。',
            'textFormType.in' => '不正な形式が指定されています。',
            'storeType.required' => '形式が選択されていません。',
            'storeType.in' => '不正な形式が指定されています。'
        ];
        
        $request->validate([
            'textFormType' => 'required|in:history,textarea',
            'storeType' => 'required|in:transform_dropdown,transform_input,proofread'
        ], $messages);
        
        if ($request->textFormType == 'history') {
            $body = $this->formHistory($request,$id);
        } elseif($request->textFormType == 'textarea') {
            $body =  $this->formTextarea($request);
            $this->storePlainText2($body,$id);
        }
        
        if ($request->storeType == 'transform_dropdown') {
            $this->storeTransformDropdown($request,$id,$body);
        } elseif($request->storeType == 'transform_input') {
            $this->storeTransformInput($request,$id,$body);
        } elseif($request->storeType == 'proofread') {
            $this->storeProofred($request,$id,$body);
        }
        return $this->showNew($id)->with('message', 'テキストを生成しました。')->with('createText',true);
    }

    public function formHistory(Request $request,$id) {
        $messages = [
            'dropdownTextHisoty.required' => '形式が選択されていません。',
        ];
        
        $request->validate([
            'dropdownTextHisoty' => 'required',
        ], $messages);
        $project = Project::where('user_id', auth()->id())->where('user_project_id', $id)->first();
        return Text::where('project_id', $project->id)->orderBy('created_at', 'desc')->firstWhere('project_text_id', $request->dropdownTextHisoty)->body;
    }

    public function formTextarea(Request $request) {
        $messages = [
            'body.required' => 'テキストは必須です。',
            'body.max' => 'テキストは最大100文字までです。',
        ];
        $request->validate([
            'body' => 'required|max:100'
        ], $messages);
        return $request->body;
    }
    
    public function storeTransformDropdown(Request $request,$id,$body) {
        $messages = [
            'dropdownTextType.required' => '形式が選択されていません。',
            'dropdownTextType.not_in' => '形式が選択されていません。',
        ];
        $request->validate([
            'dropdownTextType' => 'required|not_in:選択してください'
        ],$messages);
        $textType = $request->dropdownTextType;
        $this->storeChatText2($body, $id,$textType);
    }
    public function storeTransformInput(Request $request,$id,$body) {
        $messages = [
            'inputTextType.required' => '形式が選択されていません。',
            'inputTextType.not_in' => '形式が選択されていません。',
            'inputTextType.max' => '形式は最大100文字までです。',
        ];
        $request->validate([
            'inputTextType' => 'required|max:100'
        ],$messages);
        $textType = $request->inputTextType;
        $this->storeChatText2($body, $id,$textType);
    }
    public function storeProofred(Request $request,$id,$body) {
        $textType = null;
        $this->storeChatText2($body, $id,$textType);
    }

    public function storePlainText2($body,$id) {
        $data = [
            'body' => $body,
            'is_posted' => 1,
        ];
        $this->storeText2($data,$id);
    }

    public function storeChatText2($body,$id, $textType) {
        if($textType == null) {
            $inputText='`'.$body.'`を校正して下さい。出力は本文のみでお願いします';
        } else {
            $inputText='`'.$body.'`を文章の形式`'.$textType.'`風に変換して下さい。出力は本文のみでお願いします';
            $data['type'] = $textType;
        }
        $newBody = $this->generateResponse($inputText);
        
        $data['body'] =$newBody;
        $data['is_posted'] = 0;
        $this->storeText2($data,$id);
    }

    public function storeText2($data,$id) {
        $projectId = Project::where('user_id', auth()->id())->where('user_project_id', $id)->first()->id;
        $data['project_id'] = $projectId;

        $maxProjectTextId = Text::where('project_id', $projectId)->max('project_text_id');
        $data['project_text_id'] = $maxProjectTextId + 1;
        
        Text::create($data);
    }


    public function showNew($id) {

        $project = Project::where('user_id', auth()->id())->where('user_project_id', $id)->first();
        if (Text::where('project_id', $project->id)->exists()) {
            $texts = Text::where('project_id', $project->id)
                         ->orderBy('created_at', 'desc')
                         ->take(2)
                         ->get();
            $showData = [
                'old' => $texts[1]->project_text_id,
                'new' => $texts[0]->project_text_id,
            ];
        }

        $showData['id'] = $id;
        
        return redirect()->route('project.show', $showData);
    }

    public function showChange(Request $request,$id) {

        $data = [
            'id' => $id,
            'old' => $request->dropdownOldId,
            'new' => $request->dropdownNewId,
        ];
        return redirect()->route('project.show', $data);
    }

    public function setQuery2(Request $request,$id) {
        $messages = [
            'dropdownOldId.required' => '前:IDが選択されていません。',
            'dropdownNewId.required' => '後:IDが選択されていません。',
            'dropdownNewId.different' => 'IDは異なる必要があります。'
        ];
        
        $request->validate([
            'dropdownOldId' => 'required',
            'dropdownNewId' => 'required|different:dropdownOldId'
        ], $messages);
        return $this->showChange($request,$id);
    }

}
