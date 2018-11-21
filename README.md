# FICHIERS
Joomla plugin that lists files and subdirectorys recursively from a given directory. It can be used with samba sharing and also has file type filtering

## Getting Started
These instructions will show you how to install the plugin, configure Joomla and, if you want, SAMBA Share to use the FICHIERS plugin

### Prerequisites
To use the plugin, you need to:
```
A joomla 3.x website;

A directory in the root site to put the files to be published, can be a directory of samba sharing;

Permission to install joomla plugins
```
### Installing
Install like any joomla plugin.

### Enabling
In Plugin Manager enable the FICHIERs plugin and set de root directory.

## USAGE
Create a article, insert in body text:
```
{fichiers}DIRECTORY|RESPONSIBLE|EXTENSIONS|SORT{/fichiers}
```
### PARAMS
#### DIRECTORY
  Is a sub-directory of the root directory.
#### RESPONSIBLE
  Is the responsible to publish by files into directory.
#### EXTENSIONS 
  Is possible select which are extensions will be visible in the article. Can be 
  ```
  a =  list all files in directory
  text = list all text files like doc, docx, odt, ods, xls, slsx, pdf...
  image = list all image files
  ```
  
  


