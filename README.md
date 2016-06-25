# mdms
mdms - Markdown Management System; a simple markdown (the most awesome text file you will hear about. period.) based flat file (no databases here!) content management system.
<hr>

## Fully Featured Admin Panel
- File editing, creation, deletion & relocation

## User friendly URL's
mdms automagically makes all url's friendly for the user.
say a file is located at *content/hello/world.md* the url would be *example.com/hello/world*
this is all done within php and a single ***.htaccess*** file.

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
mdms is fairly easy to install. Just edit a couple files, drag and drop and its deployed!

## No admin panel
If you don't want the awesome admin panel designed specifically for mdms, just delete the admin folder*.

Note: this installation type is still mostly untested, for this reason, you might want to have a look through the code before you attempt to deploy this installation type.

## Admin Panel
create a new databse by the name of 'login'.
run the SQL script below to create a new table:

```CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `access_level` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'defines user access level for associated rights',
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email, unique',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data' AUTO_INCREMENT=1 ;
```

Then browse to admin/config/db.php, and edit the file to your specific settings

That should be about it; There may be some errors, create an issue if you come across one.

<hr>
### Help Needed!
Do you want to help make mdms something truly spectactular?
Feel free to fork this repo, and let me know of any changes/additions you think are better than the currently used implementation.

#### What you can help with:
- Documentation
- Theming
- Login (move to sqlite)
- General cleanup & Optimization
- Making awesome themes!
- Installation (instructions & drag/drop install)