---
extends: _layouts.post
section: content
date: 2017-06-27
title: Introducing CloudStage
---
# Introducing CloudStage

I am a constant tinkerer, always messing about with ideas for side-projects and seeing what sticks.  One of the barriers - albeit very small - to trying code out in the wild is the ability to get a quick domain name for testing.  PaaS providers such as [Heroku](https://www.heroku.com/) and [Pagoda Box](https://pagodabox.io/) will give you a simple and memorable default domain when you spin up a new service, but there are plenty other players in the market.

I personally use Amazon Web Services and Digital Ocean to quickly try out ideas - spinning up and down servers all over the show.  Whilst AWS does give all of their services a unique URL, they are dynamically generated and can be pretty unwieldy - also, there's no real allowance for running multiple sites off of one server.

So I came up with [CloudStage](https://github.com/theprivateer/cloudstage) - a quick side-project that came about from playing with the AWS Route 53 (DNS) API.  It's a SUPER simple (and free) service that will give you a custom staging/development subdomain for you to point at your server of choice.  Feed it either an IP address or domain name, and it will resolve your custom domain in no time at all.  Tinkerers and hobbyists rejoice!

![](/assets/img/snapstack/1/iRzQoLbxkltUTpdkx7o98jQauAczhsC905Q9ujTT.png)

I still need to sort out some simple terms and conditions and a few monitoring features, but it's now live for all to try.  I don't expect it to get bombarded with users, so at the moment there is no limit to the number of sites you can set up - only that each domain name will self-destruct after 30 days (but there is nothing stopping you from setting it up again for another 30 days when it does). I've got some ideas of how to develop a slightly more 'pro' option, but my main reason for setting this up is for myself to use when exploring new ideas.
