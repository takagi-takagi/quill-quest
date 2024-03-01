<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jfcherng\Diff\Differ;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Factory\RendererFactory;
use Jfcherng\Diff\Renderer\RendererConstant;
use App\Models\Text;

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

    public function test() {
        $old = 'This is the old string.'."\n".'aaaaaaaaa'."\n".'konnichiwa';
        $new = 'And this is the new one.'."\n".'aaaaaaaaab'."\n".'konnichiwa';
        return $this->diff($old,$new);
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

    public function store(Request $request) {
        $validated = $request->validate([
            'body' => 'required|max:400'
        ]);
        $validated['project_id'] = 1;
        $validated['is_posted'] = 1;
        $text = Text::create($validated);
        $old = 'こんにちは。'."\n".'aaabbb';
        $new = $validated['body'];
        $data = ['html' => $this->diff($old,$new)];
        return view('diff.diff', $data);
    }

}
