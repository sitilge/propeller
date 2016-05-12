# curdle

A tool for simple [CRUD] operations.

## Demo

See a live demo here: http://curdle.sitilge.id.lv

## Features

- Adapt database easily using .json skeleton files
- [CRUD] tables, rows, and columns in an elegant UI
- Search & order rows instantly
- Use built-in plugins or create custom
- Join multiple tables & columns

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

The respective ```.json``` files for the project are located under ```app/Misc/Admin```. File names must match the table names.
```
{
  "key": "id",              //required, the table primary key
  "name": "Users",          //optional, the table display name
  "create": true,           //optional, allow to create a new row
  "update": true,           //optional, allow to update the row
  "delete": true,           //optional, allow to delete the row
  "order": {                //optional, allow to change the order of rows in table view
    "column": "sequence",   //required, the name of the column
    "direction": "ASC"      //optional, the direction of order
  },
  "columns": {              //required, the columns array
    "name": {               //required, the column name
      "name": "Name",       //optional, the column display name
      "view": "row",        //optional, the column display in table or row view
      "attributes": {       //optional, the input/select field attributes
        "required": "true",
        "min": "3",
        "max": "89"
        ...
      },
      "plugin": "text",     //optional, the plugin (slug, price, text, image, date, time, datetime, custom)
      "values": {           //optional, join with a set of values
        "0": "No",
        "1": "Yes"
        ...
      },
      "join": {             //optional, join with a table
        "icons": {          //required, the join table name
          "key": "id",      //required, the join table primary key
          "columns": {      //required, the join columns array
            "id": {},       //required, the join column name
            "name": {}
            ...
          }
        }
      }
    }
  }
}
```

## Troubleshooting

It may be beneficial to change the value of `development` under `app/Config/Throwable.php` to `true` to see the respective errors.

## Contributing

It is more than welcome to contribute to the project - feel free to send pull requests. Also, I try to keep the issue section updated but don't feel limited only by that. I would happily accept all critics/opinions/praises to ```sitilge@gmail.com```.

[CRUD]: <https://en.wikipedia.org/wiki/Create,_read,_update_and_delete>
[composer]: <https://getcomposer.org/download/>
[abimo]: <https://github.com/sitilge/abimo>
