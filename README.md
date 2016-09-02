# Propeller

Propeller is a graphical DBMS tool based on [Propel].

## Installation

Clone the project, install the dependencies

````
git clone https://github.com/sitilge/propeller.git propeller && \
cd propeller && \
composer install
````

Then add `propel` command to the excecution path (adjust the `/path/to/propeller` respectively)

````
export PATH=$PATH:/path/to/propeller/vendor/bin/
````

## Configuration

By default, settings are stored under `src/Config/Database/Config`

````
cd src/Config/Database/Config
````

Next, initialize ([the easy way]) or setup your own config ([the hard way]). Let us use the easy way; run

````
propel init
````

And use your settings to setup the connection to the DB. The following Q/A will lead you through the questions asked by the final steps of the initializer

* **Q:** Where do you want to store your schema.xml? [/var/www/propeller/src/Config/Database/Config]: **A:** .
* **Q:** Where do you want propel to save the generated php models? [/var/www/propeller/src/Config/Database/Config]: **A:** ./../
* **Q:** Which namespace should the generated php models use?: **A:** Models
* **Q:** Please enter the format to use for the generated configuration file (yml, xml, json, ini, php) **A:** php

Next, it is important to edit the freshly generated `schema.xml` and add the behavior Propeller is relying on. Just add the following line after the opening `database` tag

````
<database ...>
  <behavior name="Propeller\Models\BehaviorModel"/>
  ...
</database>
````

Now it is time to build models - the backbone of Propel and Propeller. Models will be stored under `src/Config/Database/Models`

````
propel model:build
````

Then create the runtime configuration file by executing

````
propel config:convert
````

Finally, inform composer about the generated `Models` and run

````
cd ../../../../ && \
composer dump-autoload
````

## Usage

Propeller uses the convenient concept of Propel [behaviors]. The default settings can be overriden by editing the respective `***Query` model, residing under `src/Config/Database/Models/Models`. The method that Propeller invokes is `init` and edits must be introduced there

````
class ***Query extends BaseUsersQuery
{
    public function init()
    {
        //disable row delete
        $this->setPropellerTableDelete(false);

        //show the column in table view
        $this->setPropellerTableColumnShow('email');

        ...
    }
}
````

[Propel]: <https://github.com/propelorm/Propel>
[the easy way]: <http://propelorm.org/documentation/02-buildtime.html#the-easy-way>
[the hard way]: <http://propelorm.org/documentation/02-buildtime.html#the-hard-way>
[behaviors]: <http://propelorm.org/documentation/06-behaviors.html>
