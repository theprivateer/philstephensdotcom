---
extends: _layouts.post
section: content
date: 2020-11-12
title: Getting back to writing
---
# Getting back to writing

Earlier this year whilst idly searching for domain at work I discovered that **philstephens.com** was available.  I promptly snatched it up, and recently decided it was time to resurrect the blog that I used to host at an old domain.

I want something that was easy to maintain and cheap to host. The previous incarnation of this blog was managed by a custom content management system [powered by Dropbox](/dropbox-as-a-blogging-platform).  All content was stored in Markdown flat files, with updates triggered using Dropbox webhooks.  It wasn't a static website as all content was stored in a simple relational database, but the ease of update was there.

This time round I wanted to take a similar approach, but go for a static site generator.  There are heaps of options out there, but as a PHP (and Laravel) developer I naturally gravitated towards [Jigsaw](https://jigsaw.tighten.co/), the static site generator from [Tighten](https://tighten.co/).  Since all of my content was already in separate Markdown files, all backed up on Dropbox, it was a simple task to repurpose some of the front-matter in the documents and rebuild the new site using one of Jigsaw's default templates.

For hosting I have opted (so far) for [Netlify](https://www.netlify.com/), as I can build the site on push to Github - for what I need at the moment my Netlify hosting is free, but that may change as I expand the site.

All that is left for me to do now is cleanup some of the dead links in some of my older posts ([drop me a line](/contact) if you find any!), start playing with the website templates and, perhaps most importantly, start writing again. 
