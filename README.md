![ヘッダー画像](https://github.com/takagi-takagi/quill-quest/assets/125945940/f2f093c9-2d59-4e3e-8649-026ae8f5f5f5)

## 概要

テキストのバージョン(変更履歴)管理、比較、ChatGPTを通した校正・変換が出来るサイトです。

## 思い

私は文章を考えるのが苦手で、文章を書かなければならない場面では、ネットで書き方を調べたり、ChatGPTに校正してもらったり、手動で修正することを繰り返していました。文章の履歴を記録しながら、変更点を色付きで表示してくれるアプリがあれば、実際に使用する文章を考えるうえでも、文章を書く練習をするうえでも便利だなと思い、アプリを作成しました。

## 機能

| トップ画面 | 新規登録 |
|------------|------------|
| ![トップ画面](https://github.com/takagi-takagi/quill-quest/assets/125945940/6d8e0a15-40a6-46c1-ad4f-f74f36abcde6) | ![新規登録](https://github.com/takagi-takagi/quill-quest/assets/125945940/6d19529c-c2ed-4d0c-b89a-00b34184af26) |
| こちらからログイン、新規登録、イベント一覧、ログアウトのページに遷移します。 | 名前、メールアドレス、パスワードを入力して登録できます。 |

| ログイン | パスワードリセット |
|------------|------------|
| ![ログイン](https://github.com/takagi-takagi/quill-quest/assets/125945940/1b28d6fd-c0c9-486c-9e97-0416fba0f1a8) | ![パスワードリセット](https://github.com/takagi-takagi/quill-quest/assets/125945940/815c46c5-3e64-46c1-afee-fe24e4194f80) |
| メールアドレスとパスワードを入力してログインします。ブラウザに認証情報を記憶する機能、パスワードリセット機能があります。 | 登録時のメールアドレスに、パスワードリセットのためのリンクをメールで送信します。 |

| イベント作成 | イベント管理画面 |
|------------|------------|
| ![イベント作成](https://github.com/takagi-takagi/quill-quest/assets/125945940/236198ad-00a3-46b6-8498-8c5578e8bbc1) | ![イベント管理画面](https://github.com/takagi-takagi/quill-quest/assets/125945940/7db9581c-49e1-457c-8617-2dc3d2fe0e23) |
| テキストの履歴を記録する「イベント」を作成または削除することができます。クリックすると閲覧ページに移動します。 | イベント内のテキストを管理する画面です。テキストを入力・生成すること、履歴のテキストを比較・閲覧することができます。 |

| 変換のもととなるテキストを入力もしくは選択 | テキストを変換もしくは校正 |
|------------|------------|
| ![変換のもととなるテキストを入力もしくは選択](https://github.com/takagi-takagi/quill-quest/assets/125945940/69a40761-dce3-4841-9f49-874d68a1fa54) | ![テキストを変換もしくは校正](https://github.com/takagi-takagi/quill-quest/assets/125945940/9625cb99-4e6e-4291-9f94-e2cfc265ad61) |
| 変換のもととなるテキストを入力するか、履歴から選択します。入力する場合、過去の履歴から最新のものがフォームに挿入されていますので、編集して使用します。入力されたテキストは履歴に保存されます。履歴のテキストから選択した場合、同じものが履歴に保存されることはありません。 | テキストを変換または校正します。変換する場合は、「○○風に変換する」の○○の部分を選択するか、入力します。選択する場合は、選択肢にはいくつかのサンプルと過去に入力されたものがあります。テキストが変換・校正されたあとは、テキスト比較機能で変換前と変換後のテキストを比較します。 |

| テキスト比較機能 | テキスト履歴機能 |
|------------|------------|
| ![テキスト比較機能](https://github.com/takagi-takagi/quill-quest/assets/125945940/6ad354c8-f685-4db6-8001-267fe07cb232) | ![テキスト履歴機能](https://github.com/takagi-takagi/quill-quest/assets/125945940/3425911f-ecf8-42c6-a360-cc19a7058152) |
| 履歴から二つのテキストを選んで比較します。削除・追加された箇所の色が変わります。 | テキストの履歴を表示します。ボタンを押すことで、コピー・変換対象にセット・比較機能にセットすることができます。クリックするとモーダルウィンドウに全文が表示されます。 |

| レスポンシブ対応 | 削除機能 |
|------------|------------|
| ![レスポンシブ対応](https://github.com/takagi-takagi/quill-quest/assets/125945940/3d761b51-a435-4e72-9a0a-0a5a6df12782) | ![削除機能](https://github.com/takagi-takagi/quill-quest/assets/125945940/956aba39-e81d-418d-ab1e-f989942dc9c0) |
| PCでは変換前後の入力フォームは左右に表示されていますが、スマホ画面では上下に表示されます。テキスト履歴のボタンはモーダルウィンドウにのみ表示されます。 | イベント・テキスト・アカウントの削除時、モーダルウィンドウで確認画面が表示されます。 |

| アカウント画面 | 成功メッセージ・エラーメッセージ |
|------------|------------|
| ![アカウント画面](https://github.com/takagi-takagi/quill-quest/assets/125945940/5495220c-a775-4d7c-a3d7-ab03ffc95b56) | ![成功メッセージ・エラーメッセージ](https://github.com/takagi-takagi/quill-quest/assets/125945940/2aa93bd9-a14b-46bb-a871-c7d410e7911a) |
| 名前とメールアドレスの変更、パスワードの更新、アカウントの削除ができます。 | 入力チェックが形式に合わない場合はエラーメッセージ、送信が成功した場合は成功メッセージが表示されます。 |

## 使用技術

| パッケージ名                                                                              | バージョン | 説明                                                     |
|------------------------------------------------------------------------------------------|------------|----------------------------------------------------------|
| PHP                                                                                      | 8.2.9      | -                                                        |
| Laravel                                                                                  | 10.45.0    | PHPのフレームワーク                                       |
| [laravel/sanctum](https://github.com/laravel/sanctum)                                    | 3.3        | トークンベースの認証システムを提供する Laravel パッケージ。|
| [laravel/tinker](https://github.com/laravel/tinker)                                      | 2.8        | Laravel の REPL（対話型シェル）パッケージ。               |
| [openai-php/laravel](https://github.com/openai-php/laravel)                              | 0.8.1      | OpenAI の API を Laravel アプリケーションで簡単に使用するためのパッケージ。|
| [laravel/breeze](https://github.com/laravel/breeze)                                      | 1.28       | 認証システムを提供する Laravel パッケージ。               |
| [laravel/sail](https://github.com/laravel/sail)                                          | 1.18       | Laravel の開発環境を Docker コンテナで提供するパッケージ。|
| [askdkc/breezejp](https://github.com/askdkc/breezejp)                                    | 1.8        | Laravel Breeze の日本語対応パッケージ。                  |
| [jfcherng/php-diff](https://github.com/askdkc/breezejp)                                  | 6.15       | テキストやファイルの差分を計算するためのライブラリ。      |
| Alpine.js                                                                                | 3.4.2      | JavaScriptフレームワーク。                               |
| Tailwind CSS                                                                             | 3.1.0      | CSSフレームワーク。                                      |
| vite                                                                                     | 5.0.0      | コンパイルをするツール。                                 |
| Node.js                                                                                  | 17.6.0     | JavaScriptをサーバー側で動作させるプラットフォーム。viteを使うのに必要。|
| Laravel Blade                                                                            | -          | Laravelに含まれているテンプレートエンジン。              |
| gpt-3.5-turbo                                                                            | -          | OpenAIのAIモデル。                                       |

## ER図

![ER図](https://github.com/takagi-takagi/quill-quest/assets/125945940/6a9f6e32-4f3c-4ab5-ab88-42ca71a94723)
