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

  // GET request, used by iA Writer to get the micropub config - it's not
  // necessary to provide anyting so we can return an empty JSON here
  if (event.httpMethod === 'GET') {
    return callback(null, {
      statusCode: 200,
      body: "{}"
    })
  }

  // Parse the JSON event body from iA Writer
  const data = JSON.parse(event.body)
  console.log("Data: ", data);

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
    owner: "theprivateer",
    repo: "philstephensdotcom",
    message: ("New blog post: " + title),
    path: "source/_posts/" + filename + ".md",
    content: Buffer.from(fileContent.join("\n")).toString("base64")
  }).then((response) => {
    // Redirect iA Writer to the blog page, where the post will eventually show up.
    callback(null, {
      statusCode: 201,
      headers: {
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
