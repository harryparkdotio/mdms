# mdms
mdms - Markdown Management System; a simple markdown (the most awesome text file you will hear about. period.) based flat file (no databases here!) content management system.

## User friendly URL's
mdms automagically makes all url's friendly for the user.
say a file is located at *content/hello/world.md* the url would be *example.com/hello/world*
this is all done within php and a single .htaccess file.

## Custom 404
mdms filters all URL's and will return a 404 error page (404.md) if no file is found.
You might ask yourself, 'Can't I just do this with the .htaccess'? Well, Yes and No. Yes technically it would work, but no as it's more likely to return an error, plus it means you can't edit the 404 page, what is the point of a page you can't edit with a CMS?

## Twig Templating
mdms uses the popular twig templating system, so users of Django or Jinja rejoice! You should feel right at home with Twig.

## YAML Headers
Every markdown file has it's own header, whether its a title, a description, or the specified template, its easy to link markdown with templating with mdms.

## No composer here! (Awesome for shared hosting installations)
All needed plugins for mdms to work are included and don't require a seperate composer install.
### Reason
The reason I opted not to use composer was because the fact that I actually was testing this on a shared hosting server, and if you've ever worked wth one, you'll know how limiting they can be. You really can't install composer easily, and I wanted this project to be as user friendly as possible, therefore, no composer. Everything you need is already linked.


### Examples of MDMS being used
http://jiggleymedia.com

## Demo coming soon!
