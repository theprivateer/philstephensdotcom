---
extends: _layouts.post
section: content
date: 2021-09-06
title: My Visual Studio Code Setup
categories: [development]
---
# My Visual Studio Code Setup

I mainly write code in PHP, and for my day job I predominantly lean on [PhpStorm](https://www.jetbrains.com/phpstorm/). However more recently I have been making concerted effort to try and replicate my workflow in [Visual Studio Code](https://code.visualstudio.com) for personal projects.

With a sprinkling of extensions and configurations I have more or less been able to maintain the muscle-memory built up from years of PhpStorm use.

## Appearance

![](/assets/img/snapstack/1/sG5yVhJKf9aFZhL6rkMoKxbsHZwvIJFIycA4yE7Y.png)

For years I have been a fan of the default dark IDE themes - but about a year ago I [watched this video](https://www.youtube.com/watch?v=rDMI1dpNfdw&t=353s) about the benefits of using a light based theme I decided to give [phpstorm-light-lite-theme](https://github.com/brendt/phpstorm-light-lite-theme) a whirl.

With VS Code I haven't really had time to explore different custom themes, so I've stuck to the **Quiet Light** theme that ships with the editor.  Whilst it doesn't have as much contrast as other light themes I find it isn't as harsh on the eyes as the more standard light themes.

When it comes to font size, I don't really feel like squinting to read what I am typing so I opt for a reasonably large font-size (16px) with plenty of space around it (28px line height).

I've been spending a fair bit of time using [iA Writer](https://ia.net/writer) recently, aiming to make it my main editor or choice for writing, and I really like the font family it uses (in particular [iA Writer Duospace](https://github.com/iaolo/iA-Fonts/tree/master/iA%20Writer%20Duospace)), so I have opted to use the same font in VS Code.

I like to keep my workspace fairly minimal so I hide both the status bar and the activity bar by default.  I also hide the editor mini-map - whilst it's a pretty neat little tool I don't find it adds to much to my productivity and introduces more clutter to the screen real-estate.

One of the time-saving features from PhpStorm that has caught me out a few times in other IDEs/code-editors is the auto-save function.  Since I tend to work within Laravel projects with lots of separate files I like to have auto-save on focus-change (i.e. move out of a tab) enabled.

For reference here is my user settings JSON:

```json
{
    "security.workspace.trust.untrustedFiles": "open",
    "editor.fontSize": 16,
    "editor.fontFamily": "'iA Writer Duospace', Menlo, Monaco, 'Courier New', monospace",
    "workbench.colorTheme": "Quiet Light",
    "files.autoSave": "onFocusChange",
    "editor.lineHeight": 28,
    "editor.minimap.enabled": false,
    "workbench.statusBar.visible": false,
    "workbench.activityBar.visible": false
}
```

## Extensions

Unlike PhpStorm, when you open up the default installation of VS Code it isn't particularly geared towards PHP development. That's where the platform's vibrant extension ecosystem comes in.

I've deliberately tried to limit the number of extensions I use to keep everything as streamlined as possible.

Here are the ones I currently have installed (in alphabetical order):

### ENV

Working with Laravel means working with `.env` files.  This plugin sprinkles a little syntax highlighting and formatting to make these files a little more usable.

[View package](https://marketplace.visualstudio.com/items?itemName=IronGeek.vscode-env)

### Laravel Blade Snippets

I mainly use this for the superior syntax highlighting of Laravel Blade templates, but there are a couple snippets that are bundled with it that I occasionally reach for.

[View package](https://marketplace.visualstudio.com/items?itemName=onecentlin.laravel-blade)

### Laravel Extra Intellisense

Some great autocomplete tools for working with Laravel.

[View package](https://marketplace.visualstudio.com/items?itemName=amiralizadeh9480.laravel-extra-intellisense)

### PHP Intelephense

Probably the best code intelligence package for PHP with some great code completions.  I'm currently only using the free version, but will likely grab a license at some point to take advantage of the premium features (and support the developer).

[View package](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client)

### PHP Namespace Resolver

Quite possibly one of my most-used extensions (and maybe one of the things I missed most about PhpStorm), this makes namespace resolution and auto-import of classes effortless.

[View package](https://marketplace.visualstudio.com/items?itemName=MehediDracula.php-namespace-resolver)

## Always be iterating

One of the great things about VS Code (and PhpStorm of course) is it's configurability. As I use VS Code more and more I will likely change things up to improve my workflow to make my development as effortless as possible.  When I inevitably do I will update this page accordingly.

If you have any suggestions for some great extensions/workflow hacks for PHP developers feel free to drop me an email at [phils@hey.com](mailto:phils@hey.com).
