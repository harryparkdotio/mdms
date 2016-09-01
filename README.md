# mdms
mdms - Markdown Management System; a simple markdown (the most awesome type of text file you will ever hear about. period.) based flat file (no databases here!) content management system.
<hr>

## Fully Featured Admin Panel
- File editing, creation, deletion & relocation

## User friendly URL's
mdms automagically makes all url's friendly for the user.
say a file is located at *content/hello/world.md* the url would be *example.com/hello/world*
this is all done within php and a single ***.htaccess*** file.
#### make sure you copy over the .htacces file

## Twig Templating
mdms uses the popular twig templating system, so users of Django or Jinja rejoice!

## YAML Headers
Every markdown file has it's own header, whether its a title, a description, or the specified template, its easy to link markdown with templating with mdms.

## No composer here! (Awesome for shared hosting installations)
All packages for mdms to work are included and don't require a seperate composer install.

## Child page support
mdms allows child pages; pages which can be *included* in another file. *Hint: the template does need to specifically mention the included child page(s)*
<hr>

# Installation
mdms is fairly easy to install. Just drag and drop and its deployed!

## No admin panel
If you don't want the awesome admin panel designed specifically for mdms, just delete the admin folder in the plugins folder.

Note: this installation type is somewhat tested, for this reason, you might want to have a look through the code before you attempt to deploy this installation.

## Admin Panel
login to the admin panel with:
```
Username: username
Password: password
```

Then create a new user via the 'users' tab. Enter your chosen username and password, and select *'10'* for the access level (admin). Copy and paste the output to ```plugins/Admin/config/users.php``` as well as deleting the current user within the file.

Go ahead and logout, then log in again with your new username and password.


Then browse to admin/config/db.php, and edit the file to your specific settings

That should be about it; There may be some errors, create an issue if you come across one.

#### (Explanatory Documentation)[http://harrypark.io/mdms/documentation]
