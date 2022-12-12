---
extends: _layouts.dev
section: content
date: 2022-08-17
title: Run multiple versions of PHP on macOS
---
# Run multiple versions of PHP on macOS

This weekend I wanted to take a look at a Laravel project I worked on back in 2016 (and haven't touched since). I located and checked-out the code to my MacBook Pro and ran `composer install` only to find that the project was not compatible with the version of PHP I had installed (v8.1.5).  Worse still, looking back at the dependencies, this version of Laravel required PHP 5.6.

A quick check of [Homebrew](https://brew.sh) showed that v5.6 was no longer officially supported (via `brew install php@[version]`) so I had to find another source.

## Install PHP 5.6

Since PHP 5.6 is no longer available from the default Homebrew tap, we'll need an alternate source.  For this exercise we will use the custom tap from [Shivam Mathur](https://github.com/shivammathur/homebrew-php).

First fetch the formulae in this tap:

```bash
brew tap shivammathur/php
```

Next we can install PHP 5.6 (full list of [supported versions  available here](https://github.com/shivammathur/homebrew-php#php-support)):

```bash
brew install shivammathur/php/php@5.6
```

## Switching versions

Now that we have both versions of PHP installed, we can easily switch between them some simple commands:

```bash
# Switch to PHP5.6
brew unlink php && brew link --overwrite --force shivammathur/php/php@5.6
```

```bash
# Switch to PHP8
brew unlink shivammathur/php/php@5.6 && brew link --overwrite --force php
```

To make things even easier, add the following aliases to your `.zsh_rc` (or equivalent for your shell) file:

```bash
alias php5="brew unlink php && brew link --overwrite --force shivammathur/php/php@5.6"
alias php8="brew unlink shivammathur/php/php@5.6 && brew link --overwrite --force php"
```
Now you can switch simply by typing `php5` or `php8` into your terminal.

Homebrew's isolation of packages means that you can repeat this process for any number of PHP versions. For example, my work machine has PHP7.3, 7.4 and 8.1 all installed side-by-side.
