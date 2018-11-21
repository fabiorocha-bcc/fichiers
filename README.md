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
Create a article, insert in the text body:
```
{fichiers}DIRECTORY|RESPONSIBLE|EXTENSIONS|SORT{/fichiers}
```
### PARAMS
#### DIRECTORY
  This is a sub-directory of the root directory.
#### RESPONSIBLE
  This is the responsible to publish by files into directory.
#### EXTENSIONS 
  You can select which extensions will be visible in the article. Can be:  
  ```
  a =  list all files in directory
  text = list all text files like doc, docx, odt, ods, xls, slsx, pdf...
  image = list all image files
  ```
 #### SORT  
  You can select the order in which the files are listed in the text body
  ```
  null = sort by name file
  m = sort by list in fichiers.ini
  c = sort by create date of the file
  ```
  Only directory is required, by default this responsible is null, extensions is all and sort is by name.
  
  ### VIEW  
 You can configure the display name and a short description of the file contents through the file fichiers.ini. Copy this file to directory. In the fichiers.ini each line you can put 3 params separated by |. First is the file name with extension, second the display name and the last a description. I.e: Test.docx
 ```
  Test.docx | Document of test | This a document to test the plugin
  ```
  
  ### EXAMPLES
 ```
{fichiers}DIRECTORY|RESPONSIBLE{/fichiers} -> LIST ALL FILES AND SORT BY NAME
{fichiers}DIRECTORY|RESPONSIBLE|text{/fichiers} -> LIST ONLY TEXT FILES: doc, docx, odt, ods, xls, xlxs, pdf AND SORT BY NAME
{fichiers}DIRECTORY|RESPONSIBLE|image{/fichiers} : LIST ONLY IMAGE FILES jpg, bmp, png e gif. AND SORT BY NAME
{fichiers}DIRECTORY|RESPONSIBLE|pdf,jpg{/fichiers} : LIST ONLY SELECTED EXTENSIONS AND SORT BY NAME.
{fichiers}DIRECTORY|RESPONSIBLE|a|m{/fichiers} : LIST ALL FILES AND SORT BY ORDER IN fichiers.ini
{fichiers}DIRECTORY|RESPONSIBLE|a|c{/fichiers} : LIST ALL FILES AND SORT BY NEWER FILES
```
 
  
  


