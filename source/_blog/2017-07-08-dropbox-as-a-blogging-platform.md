---
extends: _layouts.post
section: content
date: 2017-07-08
title: Dropbox as a blogging platform
---
# Dropbox as a blogging platform

This blog is powered by a lightweight bespoke platform built on Laravel.  Whilst there are many great blogging platforms out there, I decided to build my own to meet my own specific requirements, and to scratch the itch of building something that I would use everyday.

All of the posts are written using Markdown and then rendered out using the fantastic [Commonmark](http://commonmark.thephpleague.com/) library from the [League of Extraordinary Packages](https://thephpleague.com).  There's nothing particularly complex about what I'm doing up until this point - however I really wanted to improve my writing experience.

Typically I will write a post either in the pretty crude web-based editor that I built into the blog's backend (about the only thing that elevates it above a standard textarea is the ability to upload images and automatically embed the corresponding Markdown), or compose something using a desktop Markdown client (currently [MacDown](http://macdown.uranusjr.com/)) and then copy-paste it onto the site.  I wanted to improve this workflow so played about with a number of options to build a Markdown editor with a side-by-side live preview _a la_ [Ghost](https://ghost.org), but nothing was quite as good as the desktop experience so I went back to basics.

A couple of years ago I created a web-based photo gallery that automatically generated itself using the contents of a corresponding Dropbox folder.  I used Dropbox webhooks to automatically sync the contents in realtime - and figured I could do the same thing with my blog posts.

A little tinkering with the syncing code from that old project, and all of a sudden I had a rudimentary - but completely functional - system that uses the contents of a particular folder on my Dropbox to control published content.

I quickly reproduced all of my blog posts up until now as separate Markdown documents and incredibly it all worked perfectly.  I was even able to make a minor adjustment to the blog's image manipulation mechanism to source the original files from Dropbox instead of the server's local filesystem, so I can easily embed images in my Markdown documents and see real live previews.

Moving my authoring and publishing mechanism over to Dropbox has opened up other opportunities - essentially, any device that is synced with my Dropbox account can become a writing environment.

A couple of months ago I purchased an iPad Pro with smart keyboard, and have been keen to put it to more use than my regular note-taking device.  A quick review of the Markdown editors available for iOS revealed a number of clear favourites - I opted for [Byword](https://bywordapp.com/), although I'm keen to give [iA Writer](https://ia.net/writer/) a go as well.  Both of these offer great writing environments on the iPad, as well as the ability to use your Dropbox filesystem.

This is the first post that I have fully written from scratch using the new publishing workflow, and so far it has been refreshing.  I'm going to continue to stick with this to see how it pans out and whether it not it encourages me to write with greater frequency.  I've got heaps of ideas on how to further improve and iterate on the system that I have, admittedly, thrown together in a couple of hours, and I will document that process along the way.
