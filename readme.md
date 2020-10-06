# Simple Social Previews WordPress Plugin

Adds meta tags to header to create Twitter summary cards and Facebook previews. Site wide default settings can be applied as well
as selecting a default image to use when posts or pages do not have a featured image. Additionally, you can override the
default settings on a post by post basis.

After installing, you can validate your [Twitter cards here](https://cards-dev.twitter.com/validator) and 
your [Facebook previews here](https://developers.facebook.com/tools/debug/).

## How to Install

Download the most recent version of the plugin from the [plugin homepage](https://code.jonathanbriehl.com) or 
from this repository.

In your WordPress administration panel, go to `Plugins`, select `Add New` then `Upload Plugin` to upload the 
zip file. After the upload is complete, click `Activate`.

## How to Use

#### Default Site Settings

After activating the plugin, set your default preferences by going to `Settings > Social Previews`.

Your options are...

__Turn on Twitter Cards__
* _Use Twitter cards on your site?_ — Checking this will tell the plugin to include the meta tags for Twitter summary cards.
* _Use large cards? By default, small cards are used._ — Checking will make the Twitter summary cards use a large image as the default.
  
__Site Twitter username__
The default Twitter account that will be referenced with the summary cards.
  
__Turn on Facebook Previews__
* _Use Facebook previews on your site?_ — Checking this will tell the plugin to include the meta tags for Facebook previews.

__Default options__
* _Use site title in shared link title?_ — Checking will add the site title/name to the cards and previews. Example "Post Title" would become "Post Title - Site Title".
* _Default Share Image_ — You may set a default image that will be displayed if the page or post being shared does not have a featured image.
