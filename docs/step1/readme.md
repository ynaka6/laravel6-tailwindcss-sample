## 📝 Tailwind CSS セットアップ

Laravelのプロジェクト作成からTailwindCSSのセットアップを行います。

### [前提として]
- composerコマンドが利用できること
- npm (or yarn)コマンドが利用できること

### 1. Laravelプロジェクト作成

```
$ composer create-project --prefer-dist "laravel/laravel:6" laravel6-tailwind-sample
$ cd laravel6-tailwind-sample
```

### 2. tailwindcssのインストール

```
$ npm install --save-dev tailwindcss
```

### 3. 関連するライブラリ、利用するライブラリをインストール

```
npm install --save-dev @tailwindcss/custom-forms laravel-mix-purgecss laravel-mix-tailwind
```

- @tailwindcss/custom-forms : Tailwind CSSのフォームで利用するclassを定義
- laravel-mix-purgecss, laravel-mix-tailwind : webpack.mix.jsでビルドに利用

### 4. Tailwind CSS の初期化

tailwind.config.jsを作成します。Tailwind CSSの設定ファイルです。

```
npx tailwind init
```

作成されたファイルは以下になります。

```
module.exports = {
  theme: {
    extend: {}
  },
  variants: {},
  plugins: []
}
```

以下のように修正します。

```
module.exports = {
  purge: [
    './resources/views/**/*.blade.php',
    './resources/css/**/*.css',
    './resources/sass/**/*.scss',
  ],
  theme: {
    extend: {}
  },
  variants: {},
  plugins: [
    require('@tailwindcss/custom-forms')
  ]
}
```

### 5. webpack.mix.jsを修正

webpack.mix.jsを以下のように変更します。

```
const mix = require('laravel-mix');
require('laravel-mix-tailwind');
require('laravel-mix-purgecss');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
   .tailwind('./tailwind.config.js');
if (mix.inProduction()) {
  mix
   .version()
   .purgeCss();
}
```

### 6. app.scssを修正

`/resources/sass/app.scss`を以下のように、Tailwind CSSを読み込むように変更します。

```
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### 7. Laravel Mixをビルド

ビルドして、public/js/app.js, public/css/app.cssを作成します。

```
$ npm install
$ npm run dev
```