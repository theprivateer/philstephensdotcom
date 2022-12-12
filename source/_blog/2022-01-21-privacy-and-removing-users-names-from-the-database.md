---
extends: _layouts.post
section: content
date: 2022-01-21
title: "Subworthy: Privacy, and removing user's names from the database"
---
# Subworthy: Privacy, and removing user's names from the database

_**Since writing this I have chosen to [shut down Subworthy](/calling-time-on-subworthy/).**_

Privacy is a big thing for [Subworthy](/introducing-subworthy/).  Whilst there _is_ a form of user-tracking implemented into the platform, it is the absolute bare minimum required to be useful.

Subworthy does not track user users across the system - only the timestamp of the last meaningful interaction a user made with the service.  This could be logging in, or viewing their daily issue or clicking a link in their daily email. There is not record of the details of _what_ a user did (or anything about the user, such as IP address etc) - simply that they did _something_.

The reason for doing this is simple - Subworthy is a bootstrapped service, offered free-of-charge.  If a user isn't using Subworthy any more, but an email is being sent to them every day, there is a potential cost-saving there.

Subworthy uses [Postmark](https://postmarkapp.com) to reliably process and deliver it's daily emails.  Postmark has the ability to track opens and clicks in the emails it delivers, but the first thing I did was switch off this functionality.  There is a growing trend for email software to block pixel trackers (the means by which the email platform can detect opens) - I just didn't want to get into that, so just disabled it on my end before the first email was even sent.

The same goes for high-level website analytics.  I don't use Google Analytics _at all_. Instead I leverage [Fathom Analytics](https://usefathom.com/ref/EVGUCG) - a privacy-first service that we _pay for_ to ensure that our visitor data is secure and never sold or shared.

## User's names

Subworthy is built on top of the popular [Laravel](https://laravel.com) framework, so a lot of the common parts of a system like user management are provided out-of-the-box. By default, when I created Subworthy it stored user's names in the database.  But here's the thing - the user's name is never actually _used_ in the system.  Daily emails aren't personalised with a "Hi Bob!" intro, and it is never displayed internally. Yet, there was a database field and forms that accepted a user's real world name.

After launching on Product Hunt I noticed a number of users bypassing the `Name` field on the registration form by either entering a random character or mashing the keyboard _à la_ `dgsgdfdg`.  That's fine with me - it made me realise that I don't need to know what my users are called.

Today I've pushed a minor update that removes any trace of user's real-life names from the system.  My next step will be to recommend one of a growing number of email-aliasing services at registration.  Whilst users are not paying for Subworthy at this stage, they are _not_ the product, so I have no need to know anything about you. All I care is that you are finding Subworthy useful and want to see it grow.
