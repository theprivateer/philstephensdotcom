---
extends: _layouts.post
section: content
date: 2017-06-26
title: Micro-blogging
---
# Micro-blogging

Blogging is hard - at least, it is for me.  I much prefer the micro-blog format - short, to-the-point posts with no title.  Like Twitter, but without the strict 140 character limit and all of the _noise_ of Twitter.  There's no real self-hosted solutions out there, other than particular themes for the likes of WordPress, so a few months ago I decided to hack something together.  The concept behind it wasn't perfectly formed, but I wanted a system where I could quickly post something in a sort of timeline format, and have some of the neat features of social networking such as hashtags and location. And it had to support Markdown (BIG fan of Markdown these days) as well as the single hero image format of Instagram (before Instagram added support for multiple images).

And so, [Shortform](https://github.com/theprivateer/shortform) was born - I even bought a domain name for it!  I got a little carried away and tried to make it a little bit too much like a social network (multiple accounts, and an Oauth-powered mechanism to cross-post to other Shortform installations), which were probably a waste of time at that point, but the core features I mentioned above were a lot of fun to make so I'll probably write a little bit about them at some point.

Anyways, fast-forward to last week and I was listening to [John Gruber's podcast](https://daringfireball.net/thetalkshow/) from a few weeks ago and it featured a guy called [Manton Reece](http://www.manton.org/), who had launched his own micro-blogging platform _Micro.blog_ off the back of [a successful Kickstarter](https://micro.blog/).  Driven by the beliefs of the IndieWeb and [POSSE](https://indieweb.org/POSSE), this platform brings together the self-hosted micro blog with a familiar social-network style timeline, all powered by familiar and well-established open technologies such as RSS.

Whilst it's still in closed beta (kind of bummed I missed out on the Kickstarter) it looks to be a really interesting project that echoes a lot of my original thinking around Shortform (only better organised and articulated!).  I'm on the waitlist for when it comes out of private beta, but until then I'm resurrecting Shortform and putting it to use in my own domain.  I'll be stripping out a heap of the initial 'community' stuff and taking it right back to basics - hopefully I'll be able to use it as my own micro-blog to power my feed on Micro.blog, so I'll be adding in a heap of extra features to support things like feeds and webmentions.

In time I'll have it automatically cross-post over to [my Twitter feed](https://twitter.com/mrphilstephens), and use it as the source of truth for all of my posting online.  So go take a look at [https://shortform.philstephens.io](https://shortform.philstephens.io) and see what you think!
