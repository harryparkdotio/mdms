# mdms
mdms - Markdown Management System; a simple markdown (the most awesome text file you will hear about. period.) based flat file (no databases here!) content management system.

## User friendly URL's
mdms automagically makes all url's friendly for the user.
say a file is located at *content/hello/world.md* the url would be *example.com/hello/world*
this is all done within php and a single .htaccess file.

## Custom 404's
mdms filters all URL's and will return a 404 error page (404.md)

## Twig Templating
mdms uses the popular twig templating system, so users of Django or Jinja rejoice! You should feel right at home with Twig.

## md YAML Headers
Every markdown file has it's own header, whether its a title, a description or the specified template, its easy to link markdown with templating with mdms.

## No composer here! (Awesome for shared hosting installations)
All needed plugins for mdms to work are included and don't require a seperate composer install.
