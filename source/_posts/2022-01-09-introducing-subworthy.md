---
extends: _layouts.post
section: content
date: 2022-01-09
title: Introducing Subworthy
---
# Introducing Subworthy

_**Since writing this I have chosen to [shut down Subworthy](/calling-time-on-subworthy).**_

In 2021 I made an effort to embrace the ideas laid out in Cal Newport's book [Digital Minimalism](https://www.calnewport.com/books/digital-minimalism/) to reduce the amount of time I waste getting distracted online. In the first instance I cut down on my social media use, completely removing Facebook and Twitter from my life (unfortunately I'm still a bit of a sucker for Instagram).

However, I soon found myself replacing one form of doom-scrolling with another, by regularly visiting a range of websites I check for news and inadvertently disappearing down rabbit holes of featured links and related articles.

I wanted to create a habit of only checking these sites once or twice a day - and for that I would need a way to collate the various websites I visit into some sort of unified timeline to try and cut out the distractions and the link bait that so easily pulls me off course.

[RSS](https://en.wikipedia.org/wiki/RSS) does just this, allowing you to subscribe to any website with a public feed by using an RSS reader to regularly poll for updates.  There are tonnes of brilliant RSS readers available, but my main issue is that the temptation is still there to constantly check for new updates.

## Enter Subworthy

[Subworthy](https://subworthy.com) is an online RSS reader that 'hides' all the stories from that day until a predetermined time, then it collates those new articles into a daily 'issue' and emails me an overview.  I can click through directly to each article from the email, or visit a page online to view all full stories in one place.

![](/assets/img/snapstack/1/18NR7OuoLsIEVJI3Xfnsqgsbhs6CpkISt9GaSAtg.png)

So what makes Subworthy different?

- **It's free (for now, at least)**  
  My costs are relatively low - the only external service I require (other than hosting) is email delivery, which I use [Postmark](https://postmarkapp.com) for.  One email per day per user is a cost I will happily absorb for now.
- **It standardises the reading experience**  
  Regardless of the source, Subworthy strips back the styling of each story giving everything equal presentation (a little like the 'Reader' feature in Safari).
- **It does not replace one form of FOMO with another**  
  Subworthy does not give you the option to check your feeds whenever you like.  You have to wait and it will reliably send you everything daily at a time that you define (not multiple different emails like other similar services).
- **Proper code syntax highlighting**  
  I subscribe to a number of developer blogs, so proper syntax highlighting of code samples is a big win for me.

## What's next?

Subworthy currently supports RSS and [Atom](https://en.wikipedia.org/wiki/Atom_(Web_standard)), with [JSON Feed](https://www.jsonfeed.org) support arriving in the coming weeks.  I am deliberately steering clear of OpenGraph/social feeds - this is for long(er) form writing only.

By building my own platform I also have the opportunity to add some extra features that are missing from most RSS readers:

- **Filters**  
  A lot of the sites I subscribe to post daily or weekly recap articles that I would like to be able to filter out.
- **Custom formatting**  
  Different sites format the HTML in their RSS differently, which can give rise to some unexpected results when it is cleaned up by Subworthy. Custom formatters for popular feeds is my way around this.
- **Fetch full articles**  
  Some RSS feeds only contain a short excerpt of the full article - custom fetchers would retrieve and parse the full article from the source website.

These features are still to come, but the foundations have been laid ready for me to implement. I also have some grander ideas about making it easier for authors to build their audience, but let's learn to walk before we try to run, shall we?

## The proof is in the pudding

At the time of writing I have been using Subworthy myself for 248 days and it has truly become a habit. During the day I am no longer distracted by idly checking sites like TechCrunch and The Verge, but every evening I set aside 5-10 minutes to scroll through my latest 'issue' of Subworthy, catching up on the latest industry news (as well as the writings of various authors I am interested in) in one place.

If you would like to try a different way of consuming online stories, why not sign up now and give it a whirl at [https://subworthy.com/register](https://subworthy.com/register) (did I mention it's free?!).

**P.S. Right before writing this I saw an opportunity to create a feature to share the feeds that you subscribe to.  This is completely optional, but I figured it to be a simple lo-tech way of aiding feed discovery. Find my subscriptions at [https://subworthy.com/@phil](https://subworthy.com/@phil).**
