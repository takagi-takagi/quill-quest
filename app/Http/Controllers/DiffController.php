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


    public function storeText(Request $request,$projectName) {
        $projectId = Project::where('name',$projectName)->first()->id;
        $validated = $request->validate([
            'body' => 'required|max:400'
        ]);
        $validated['project_id'] = $projectId;
        $validated['is_posted'] = 1;
        $text = Text::create($validated);
        $data = ['projectName' => $projectName];
        if ($request->has('new')) {
            $oldId = $request->query('new');
            $newId = $text->id;
            $newData = [
                'old' => $request->new,
                'new' => $text->id
            ];
            $data = array_merge($data, $newData);
        }
        
        return redirect()->route('project.show', $data);
    }

    public function showProject(Request $request,$projectName) {
        $projectId = Project::where('name',$projectName)->first()->id;
        $texts = Text::where('project_id', $projectId)->orderBy('created_at', 'desc')->get();
        if ($request->has('old')) {
            $oldId = $request->query('old');
            $old = $texts->firstWhere('id', $oldId)->body;
        } else {
            $old = $texts[1]->body;
        }
        if ($request->has('new')) {
            $newId = $request->query('new');
            $new = $texts->firstWhere('id', $newId)->body;
        } else {
            $newId = $texts[0]->id;
            $new = $texts[0]->body;
        }
        $data = ['html' => $this->diff($old,$new), 'texts' => $texts,'projectName' => $projectName, 'newId' => $newId];
        return view('diff.diff', $data);
    }
    public function storeChatText(Request $request) {
        if(empty($request->type)||$request->typeNull == true) {
            $inputText='`'.$request->body.'`を添削して下さい。出力は本文のみでお願いします';
        } else {
            $inputText='`'.$request->body.'`を文章の形式`'.$request->type.'`として添削して下さい。出力は本文のみでお願いします';
        }
        $request->merge(['body' => $this->generateResponse($inputText)]);
        $this->storeText($request);
        return back();
    }

}
