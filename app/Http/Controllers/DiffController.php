<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jfcherng\Diff\Differ;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Factory\RendererFactory;
use Jfcherng\Diff\Renderer\RendererConstant;
use App\Models\Text;
use App\Models\Project;
use OpenAI\Laravel\Facades\OpenAI;

class DiffController extends Controller
{
    private function diff($old,$new) {
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
            'language' => ['eng',['old_version' => '旧'],['new_version' => '新']],
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
        $result = DiffHelper::calculate($old, $new, $rendererName, $differOptions, $rendererOptions);
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
            'name.required' => 'フォルダ名は必須です。',
            'name.max' => 'フォルダ名は最大100文字までです。',
        ];
        $validated = $request->validate([
            'name' => 'required|max:100'
        ], $messages);
        $validated['user_id'] = auth()->id();
        $maxUserProjectId = Project::where('user_id', auth()->id())->max('user_project_id');
        $validated['user_project_id'] = $maxUserProjectId + 1;
        $project = Project::create($validated);
        return redirect()->route('project.index')->with('message', 'フォルダを作成しました。');
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
        $project = Project::where('user_id', auth()->id())->where('user_project_id', $id)->first();
        $data = [];
        $texts = Text::where('project_id', $project->id)->orderBy('created_at', 'desc')->get();
        if ($request->has('old')) {
            $oldId = $request->query('old');
            $data['oldId'] = $oldId;
            $old = $texts->firstWhere('project_text_id', $oldId)->body;
        } elseif (isset($texts[1])) {
            $old = $texts[1]->body;
        }
        if ($request->has('new')) {
            $newId = $request->query('new');
            $new = $texts->firstWhere('project_text_id', $newId)->body;
            $data['newId'] = $newId;
        } elseif (isset($texts[0])) {
            $new = $texts[0]->body;
        }
        if (isset($old, $new)){
            $data['html'] = $this->diff($old,$new);
        } else {
            $data['html'] = '<p>ここに変更点が表示されます</p>';
        }
        $newData = ['texts' => $texts,'project' => $project];
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

}
