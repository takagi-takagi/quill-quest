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

    public function test() {
        $old = 'This is the old string.'."\n".'aaaaaaaaa'."\n".'konnichiwa';
        $new = 'And this is the new one.'."\n".'aaaaaaaaab'."\n".'konnichiwa';
        return $this->generateResponse('「Aさん！ごめんね」を文章の形式「招待状」として添削して下さい。出力は本文のみでお願いします');
    }

    public function testDiff() {
        $data = ['html' => $this->test()];
        return view('diff.diff', $data);
    }

    public function test3(Request $request) {
        $old = 'こんにちは。'."\n".'aaabbb';
        $new = $request->post;
        $data = ['html' => $this->diff($old,$new)];
        return view('diff.diff', $data);
    }

    public function index(Request $request) {
        $projects = Project::where('user_id', auth()->id())->get();
        return view('diff.indexProject', compact('projects'));
    }

    public function storeProject(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:100'
        ]);
        $validated['user_id'] = auth()->id();
        $project = Project::create($validated);
        return redirect()->route('project.index');
    }

    public function storeText($data,$projectName,$queryNewId) {
        $projectId = Project::where('name',$projectName)->first()->id;
        $data['project_id'] = $projectId;

        $maxUserTextId = Text::where('project_id', $projectId)->max('project_text_id');
        $data['project_text_id'] = $maxUserTextId + 1;
        
        $newId = Text::create($data)->project_text_id;
        
        $showData = ['projectName' => $projectName];
        if (isset($queryNewId)) {
            $queryData = [
                'old' => $queryNewId,
                'new' => $newId
            ];
            $showData = array_merge($showData, $queryData);
        }
        
        return redirect()->route('project.show', $showData);
    }

    public function storePlainText(Request $request,$projectName) {
        $validated = $request->validate([
            'body' => 'required|max:400'
        ]);
        $validated['is_posted'] = 1;
        $queryNewId = $request->query('new');
        return $this->storeText($validated,$projectName,$queryNewId);
    }

    public function showProject(Request $request,$projectName) {
        $data = [];
        $projectId = Project::where('name',$projectName)->first()->id;
        $texts = Text::where('project_id', $projectId)->orderBy('created_at', 'desc')->get();
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
        $newData = ['texts' => $texts,'projectName' => $projectName];
        $data = array_merge($data, $newData);
        return view('diff.diff', $data);
    }

    public function setQuery(Request $request,$projectName) {
        $data['projectName'] = $projectName;
        if ($request->has('setToOld')) {
            $data['old'] = $request->setToOld;
        }
        if ($request->has('setToNew')) {
            $data['new'] = $request->setToNew;
        }
        return redirect()->route('project.show', $data);
    }

    public function storeChatText(Request $request,$projectName) {
        if ($request->has('newId')) {
            $queryNewId = $request->newId;
            $body = Text::find($queryNewId)->body;
        } else {
            $body = $request->body;
            $queryNewId = null;
        }
        if(empty($request->type)||$request->typeNull == true) {
            $inputText='`'.$body.'`を添削して下さい。出力は本文のみでお願いします';
        } else {
            $validated = $request->validate([
                'type' => 'required|max:400'
            ]);
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
        return $this->storeText($data,$projectName,$queryNewId);
    }

}
