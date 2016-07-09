---
title: Documentation
description: mdms documentation
author: Harry Park
date: 7 July, 3:01PM AESDT
template: documentation
---

## <span id="install">Install</span>
To install mdms, make sure you have a server which can run PHP, and also allows the use of `.htaccess` files. A good example of this is a LAMP (linux, apache, mysql, php) server, or a shared hosting server from a provider such as Namecheap or GoDaddy. You may also be able to run this using a local instance PHP server. This is included in some Linux and Mac OS X installations.

#### For Users

Download the [latest version of mdms](https://github.com/harryparkdotio/mdms/releases) (github) and upload the zip file to your server/desired directory. Unzipping the mdms folder before uploading it to the server will still work, however, it is a much slower transfer when using ftp, it may also cause unknown/unexpected errors.

Make sure the `.htaccess` files have been loaded correctly as this is **essential** for mdms to work properly. Most ftp clients will not transfer these files by default, therefore you need to turn on 'show hidden files', both in your ftp client, and operating system.

I recommend [Transmit](https://panic.com/transmit/) (Mac OS X), [Cyberduck](https://cyberduck.io/) (Windows & Mac OS X), or [FileZilla](https://filezilla-project.org/) (All Platforms) for a solid FTP client.

#### For Developers

Open a shell and cd into the directory you want to install mdms within. Clone the mdms repository with the command below:

```
$ git clone https://github.com/harryparkdotio/mdms.git
```

Note that this gives you the current version of mdms, which may be unstable.

- - -
## <span id="content">Content</span>
### File Markup
mdms uses the highly popular markdown file format. It also uses a yaml header for page specific variables, because of this, each file must have a *'header'* of sorts. Shown below is the correct layout for the header.

```
---
title: TITLE
description: DESCRIPTION
template: TEMPLATE
author: AUTHOR
childpages: page1 page2 page3
status: published/draft/archived
---
CONTENT
```
These files are saved with the standard markdown extension (`.md`)

[Markdown Reference Guide](https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet)<br>

### Assets
Add all assets to the `assets` folder, images will only work if they use the `.jpg` file format for an unknown reason. just link files with `{{ urldepth }}assets/FILENAME.EXTENSION`. You can use the markdown or html variants for linking as markdown is parsed into html.

- - -
### URL file pointing

mdms will check if a file exists, but mdms will choose top level files over the directory based ones.

- `http://example.com` 			== `content/index.md`
- `http://example.com/` 		== `content/index.md`
- `http://example.com/about` 	== `content/about.md` but if the file doesn't exist; `content/about/index.md`
- `http://example.com/about/` 	== `content/about/index.md` but if the file doesn't exist; `content/about.md`

---

## <span id="customization">Customization</span>
### Themes
#### Installing Themes
To install a theme, put the theme folder in the `themes/` directory. Then initialize it within the `config/config.php` file. Change `$config['theme'] = 'default';` to `$config['theme'] = 'THEME NAME';`.

#### Creating Themes
Themes are easily created and installed for mdms, it's highly suggested that you create your theme in raw html form, like a normal website, and then convert over to mdms.<br>
Converting from html over to a theme that can be used within mdms is actually relatively easy, mainly because of the **Twig templating** system.

[Twig Templating Documentation](http://twig.sensiolabs.org/documentation)

Useable 'tags' within themes can be added like so
```
{{ page }} the array with all page information enclosed.
	{{ page.title }}
	{{ page.description }}
	{{ page.author }}
	{{ page.content|raw }} //'|raw' is extremely important, because the markdown is parsed to html, it musn't be autoescpaed.
{{ config }} encloses all data within the config array
{{ theme.dir }} required to send the location of the themes assets folder.
{{ base_url }} must be added for loading styling correctly, adds '../' for the amount of times a slash occurs in the url.
```

The folder structure of a theme should look like so
```
theme/
├── theme.yaml
├── assets/
|   ├── style.css
|   └── scripts.js
├── functions/
|	└── functions.php
├── partials/
|   ├── _header.html
|	├── _footer.html
|   └── _nav.html
└── templates/
    └── index.html
```
#### Explanation:
- **theme** - the folder enclosing all items required for the theme. Rename this folder to the name of your theme.
- **theme.yaml** - this yaml file is named 'theme.yaml', do not rename it. It stores things like the name of the theme, the author, author url, and a few other bits and pieces. This is not required but highly recommend.
- **assets** - the folder enclosing all assets which can be used with your template
- **functions** - the folder enclosing specific php files which are used only by your theme. These should realistically be loaded as a plugin.
- **partials** - It is recomended that you use the partials folder to store your header, footer, and navbar seperate from your templates.
- **templates** - the folder where your templates are stored. Name these as 'templatename.html', templatename can then be specified in the users .md content files. As well as this, there **must** be a 'index.html' template. Even if it just has the bare essentials, it is required.

### Plugins
#### Installing Plugins
To install a plugin, place the `PLUGINNAME.php` file in the `plugins/` directory.

Then optionally, initalialize the plugin by opening the `config/config.php` file, add a new line under `// PLUGINS`, the new line should be as follows, `$config['THEMENAME.enabled'] = 'true';`

#### Creating Plugins
Making and installing a plugin for mdms is actually quite simple, it only requires a basic to intermediate knowlege of PHP.
The first requirement your plugin must meet is that the filename, and the classname must be case for case, exactly the same. This is how mdms can autoload your plugin.

It is highly recommended that you open the `plugins/TEMPLATE.php` file and use it as a guideline for your own plugin. The only plugin to ship with mdms is the GoogleAnalytics Plugin. The `plugins.php` file is the loader an **SHOULD NOT** be modified in any way.

mdms also uses an event based loader for plugins. This means that depending on which stage of the main `mdms.php` program, your plugin can add to the base functionality at the correct stage in the process.

Each event has its own name, and the function should be named the same as the event. These events are listed below
```
onConfigLoad
onPluginLoad
request_url
onRender
```

- - -

## <span id="config">Config</span>
All default settings can be overrided within the `config/config.php` file.