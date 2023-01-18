---
extends: _layouts.post
section: content
date: 2022-09-01
title: "Using Micropub, Netlify and Jigsaw to publish new blog posts"
categories: [development]
---
# Using Micropub, Netlify and Jigsaw to publish new blog posts

I've been wanting to find ways to reduce the friction involved in writing and publishing posts to this blog.

This website is built using [Jigsaw](https://jigsaw.tighten.com), a PHP/Laravel-based static website builder. I host the site on [Netlify](https://www.netlify.com), which automatically builds and publishes the site whenever I push a commit to GitHub.

I typically do my writing in [iA Writer](https://ia.net/writer) and then copy the content across to my IDE before pushing the update to version control.  One of the lesser-documented features of iA Writer is its ability to publish content to a number of publishing platforms, namely Ghost, Medium and WordPress - but it also supports a generic [Micropub](https://indieweb.org/Micropub) format.

After a little tinkering I was able to use [Netlify Functions](https://www.netlify.com/products/functions/) to create a Micropub-compatible endpoint on my static website that could commit a new file to my GitHub repository, thus triggering a full site rebuild.  This method is not restricted to websites built with Jigsaw - you can use it for pretty much any static site builder, and even swap out Netlify Functions for another lambda-based system such as [AWS Lambda](https://aws.amazon.com/lambda/).

## Github API

In order to write a file on GitHub we need a personal token with repo write access. Unfortunately GitHub's authorisation framework is not great and it's an all-or-nothing kind of deal regarding personal tokens, so make sure you keep it in a safe place and NOT in code.

Under your GitHub profile go to settings and `Developer settings` > `Personal access tokens`.

Create a new token with a descriptive name and only the `repo` scope checked.

![](/assets/img/micropub/github.png)

## Netlify setup

In order to use the token in our Netlify function, we need to expose it to the function, we can do this through environment variables on Netlify. You can find this under the `Build & Deploy` tab of the `Settings` of your application.

Let's add a new environment variable called `GITHUB_ACCESS_TOKEN` with the token from GitHub as the value. While we're here let's also add a token we can use to authenticate iA Writer when it posts to our Netlify function.

Create a second environment variable called `TOKEN` with a [random value](https://www.random.org/strings/).

![](/assets/img/micropub/netlify.png)

## iA Writer publishing flow

iA Writer has a bit of a weird flow when adding a Micropub endpoint to its configuration. It follows these steps:

**1. Parse the HTML page from the config and discover a `<link>` tag.** Instead of providing an URL to the endpoint directly, iA Writer expects an url to the root of your site, where it will attempt to detect a `<link>` in the `<head>` of your html with a `rel` of `micropub`, for example:

```html
<link rel="micropub" href="https://<blog url>/.netlify/functions/micropub">
```

**2. Call the endpoint with a  GET request with the authentication token provided - this should return the config for the Micropub API.** Once the Micropub endpoint is discovered, iA Writer makes a GET request to the endpoint, expecting a JSON body in return, where it can detect the features of your Micropub service.

It's perfectly fine to return an empty JSON body in return.

**3. When posting, call the endpoint with a POST request with the title/markdown as JSON**

iA Writer expects a "redirect" header as a successful response and will open a browser window to this redirect target to show you the posted content.

(This works if you have a CMS that posts the content instantly, in our case you'll see a page without the post, since Netlify still has to build the new site).

## Micropub endpoint

In order to satisfy the first step for the setup flow, let's add the required metadata tag to the `<head>` of our website.

```html
<link rel="micropub" href="https://<blog url>/.netlify/functions/micropub">
```

This link in the header should point to the Netlify function we're about to create.

## Netlify function

In order to accept Micropub content from iA Writer, we need to write a Netlify function that can handle both a GET request to return the config and a POST request to handle a new article.

For more information on how to setup functions, see the [Netlify functions docs](https://docs.netlify.com/functions/overview/).

In order to create a new page on GitHub, we only have one dependency the `@octokit/rest` package, which you can install in the root of your Netlify app with your favourite package manager:

```bash
yarn add @octokit/rest
```

The function itself is pretty simple Javascript (Netlify also supports Typescript and Go if those are more your thing).

```javascript
// ./netlify/functions/micropub/micropub.js

// Our only dependency is @octokit/rest
// We use the token/GitHub auth we've set in the ENV vars.
const { Octokit } = require("@octokit/rest");
const octokit = new Octokit({
  auth: process.env.GITHUB_ACCESS_TOKEN,
})

exports.handler = (event, context, callback) => {
  // Verify the token we will use in iA Writer,
  // set in Netlify ENV settings on netlify.com
  if (
      !event.headers["authorization"] ||
      event.headers["authorization"] != "Bearer " + process.env.TOKEN
  ){
    return callback(null, {
      statusCode: 401,
      body: "{}"
    })
  }

  // GET request, used by iA Writer to get the Micropub config - it's not
  // necessary to provide anything so we can return an empty JSON object here
  if (event.httpMethod === 'GET') {
    return callback(null, {
      statusCode: 200,
      body: "{}"
    })
  }

  // Parse the JSON payload from iA Writer
  const data = JSON.parse(event.body)

  // The format is a bit weird,
  // where title and content are array values with a single entry
  const title = data["properties"]["name"][0]
  const content = data["properties"]["content"][0]

  // I want the format of the filename to be yyyy-mm-dd-title-as-slug.md
  // Javascript date handling is poor, (no strftime),
  // lets hack something with the default date functions
  // This saves us a library to import, also use a poor-mans slug generator
  const date = new Date()
  const filename = [
    date.toISOString().split('T')[0], // the date
    title.replace(/[\W]+/g,"-").toLowerCase() // the slug
  ].join("-")
  var fileContent = []

  // If we've written a post without frontmatter, insert default frontmatter
  // this allows us to override the frontmatter in iA Writer if we want, but
  // we can also just throw out a quick article without worrying about this.
  // This frontmatter is fairly specific to Jigsaw - replace with whatever you want
  if (!content.includes("---")) {
    fileContent.push("---")
    fileContent.push('extends: _layouts.post')
    fileContent.push('section: content')
    fileContent.push('date: ' + date.toISOString())
    fileContent.push('title: ' + title)
    fileContent.push('---')
  }

  fileContent.push(content)

  // Create a new file on GitHub with the octokit library
  // owner/repo and message/path are hardcoded here,
  // you might want to change those to your own likings.
  return octokit.repos.createOrUpdateFileContents({
    owner: "[GITHUB_USERNAME]",
    repo: "[GITHUB_REPO_NAME]",
    message: ("New blog post: " + title),
    path: "source/_blog/" + filename + ".md", // update path to point at your actual source path
    content: Buffer.from(fileContent.join("\n")).toString("base64")
  }).then((response) => {
    // Redirect iA Writer to the blog page, where the post will eventually show up.
    callback(null, {
      statusCode: 201,
      headers: {
        // redirect to whatever location you want here
        Location: "/blog",
      }
    });
  }).catch((error) => {
    // Log any errors, so we can debug later.
    console.log("error", error)
    return callback(null, {
      statusCode: 400,
      body: JSON.stringify(error)
    })
  })
}
```

## iA Writer setup

Finally, let's set up iA Writer to post to our Netlify function.

Under `Preferences` there's an `Accounts` section, where we can add a new `Micropub` account.

![](/assets/img/micropub/ia-micropub.png)

To make it a bit easier for ourselves, we'll use a token to authenticate the endpoint and keep away from oAuth for now. In the "URL" field, fill in the root of your blog, not the API endpoint for Micropub.  As previously discussed iA Writer will attempt to discern the actual API endpoint itself - putting the API endpoint in this field here will cause iA Writer to hang until you force-close it.

Once complete, we need to change one setting, we want iA Writer to send us the raw markdown, and not the content rendered by iA Writer in HTML. You can change this under the settings for the Micropub account.

![](/assets/img/micropub/ia-markdown.png)

## Publish an article

With everything setup and deployed we can try publishing an article. After writing some content, click `File` > `Publish` and select your newly added Micropub endpoint.

It should show a loading indicator and then open a new browser window pointing to the article url. (which might return a 404, because Netlify is still busy building your site).
