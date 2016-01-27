# curdle

> curdle - separate or cause to separate into curds or lumps

curdle is a simple database administration tool for simple [CRUD] operations using almost any relational database you prefer.

## Features

- Adapt an existing or new database with ease
- [CRUD] the tables, rows, and columns in an elegant interface
- Search & order the rows instantly
- Customize columns to fit your needs - simple value, dropdown, editor, image, and more
- Join tables to get the best of multiple sources
- Use the image gallery to manage your media files

## Tech

- small PHP footprint, < 700 LOC
- implies PDO abstraction layer - MySQL, sqlite, etc.
- built with [abimo] framework, composer packages
- front-end stack - fully responsive, Bootstrap, SASS

## Installation

Clone the project
```
git clone https://github.com/sitilge/curdle
```
Install [composer] on your system and run
```
composer install
```

## Configuration

The respective ```.json``` files for the project are located under ```app/Misc/Admin```. File names must match the table names; let us examine one of them the ```menu.json``` that corresponds to ```menu``` table in the database
```
{
  "key": "id",                  //required, the table primary key
  "name": "Menu",               //optional, the table display name, defaults to table name
  "insert": true,               //optional, allow to insert new rows, defaults to false
  "order": {                    //optional, change the order of rows in table view, defaults to false
    "column": "sequence",       //required, the name of the column
    "direction": "ASC"          //optional, the direction of order, defaults to ASC
  },
  "columns": {                  //required, the columns array
    "name": {                   //required, the column name
      "disabled": true,         //optional, disable the field, defaults to false
      "name": "Name",           //optional, the column display name, defaults to column name
      "display": "edit",        //optional, display the column in view or edit, defaults to false
      "type": "slug",           //optional, set the type to slug, text, image, length, or price, defaults to false
      "values": {               //optional, make dropdown for the values, defaults to false
        "0": "No",
        "1": "Yes"
      },
      "join": {                 //optional, join with a table
        "icons": {              //required, the join table name
          "key": "id",          //required, the join table primary key
          "columns": {          //required, the join columns array
            "id": {},           //required, the join column name
            "name": {}
          }
        }
      }
    }
  }
}
```

## Contributing

It is more than welcome to contribute to the project - feel free to send pull requests. Also, I try to keep the issue section updated but don't feel limited only by that. I would happily accept all critics/opinions/praises to ```sitilge@gmail.com```.

[CRUD]: <https://en.wikipedia.org/wiki/Create,_read,_update_and_delete>
[composer]: <https://getcomposer.org/download/>
[abimo]: <https://github.com/sitilge/abimo>