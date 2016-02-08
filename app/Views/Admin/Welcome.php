<h1 id="curdle">curdle</h1>

<blockquote>
    <p>curdle - separate or cause to separate into curds or lumps</p>
</blockquote>

<p>curdle is a simple database administration tool for simple <a href="https://en.wikipedia.org/wiki/Create,_read,_update_and_delete">CRUD</a> operations using almost any relational database you prefer.</p>



<h2 id="demo">Demo</h2>

<p>See a live demo here: <a href="http://curdle.sitilge.id.lv">http://curdle.sitilge.id.lv</a></p>



<h2 id="features">Features</h2>

<ul>
    <li>Adapt an existing or new database with ease</li>
    <li><a href="https://en.wikipedia.org/wiki/Create,_read,_update_and_delete">CRUD</a> the tables, rows, and columns in an elegant interface</li>
    <li>Search &amp; order the rows instantly</li>
    <li>Customize columns to fit your needs - simple value, dropdown, editor, image, and more</li>
    <li>Join tables to get the best of multiple sources</li>
    <li>Use the image gallery to manage your media files</li>
</ul>



<h2 id="tech">Tech</h2>

<ul>
    <li>small PHP footprint, &lt; 700 LOC</li>
    <li>implies PDO abstraction layer - MySQL, sqlite, etc.</li>
    <li>built with <a href="https://github.com/sitilge/abimo">abimo</a> framework, composer packages</li>
    <li>front-end stack - fully responsive, Bootstrap, SASS</li>
</ul>



<h2 id="installation">Installation</h2>

<p>Clone the project</p>



<pre class="prettyprint"><code class=" hljs php">git <span class="hljs-keyword">clone</span> https:<span class="hljs-comment">//github.com/sitilge/curdle</span></code></pre>

<p>Install <a href="https://getcomposer.org/download/">composer</a> on your system and run</p>



<pre class="prettyprint"><code class=" hljs cmake">composer <span class="hljs-keyword">install</span></code></pre>



<h2 id="configuration">Configuration</h2>

<p>The respective <code>.json</code> files for the project are located under <code>app/Misc/Admin</code>. File names must match the table names; let us examine one of them the <code>menu.json</code> that corresponds to <code>menu</code> table in the database</p>



<pre class="prettyprint"><code class=" hljs lua">{
<span class="hljs-string">"key"</span>: <span class="hljs-string">"id"</span>,              //required, the <span class="hljs-built_in">table</span> primary key
<span class="hljs-string">"name"</span>: <span class="hljs-string">"Menu"</span>,           //optional, the <span class="hljs-built_in">table</span> display name, defaults to <span class="hljs-built_in">table</span> name
<span class="hljs-string">"create"</span>: <span class="hljs-keyword">true</span>,           //optional, allow to create new row, defaults to <span class="hljs-keyword">false</span>
<span class="hljs-string">"delete"</span>: <span class="hljs-keyword">true</span>,           //optional, allow to delete row, defaults to <span class="hljs-keyword">false</span>
<span class="hljs-string">"order"</span>: {                //optional, change the order of rows <span class="hljs-keyword">in</span> <span class="hljs-built_in">table</span> view, defaults to <span class="hljs-keyword">false</span>
<span class="hljs-string">"column"</span>: <span class="hljs-string">"sequence"</span>,   //required, the name of the column
<span class="hljs-string">"direction"</span>: <span class="hljs-string">"ASC"</span>      //optional, the direction of order, defaults to ASC
},
<span class="hljs-string">"columns"</span>: {              //required, the columns array
<span class="hljs-string">"name"</span>: {               //required, the column name
<span class="hljs-string">"name"</span>: <span class="hljs-string">"Name"</span>,       //optional, the column display name, defaults to column name
<span class="hljs-string">"view"</span>: <span class="hljs-string">"row"</span>,        //optional, display the column <span class="hljs-keyword">in</span> <span class="hljs-built_in">table</span> <span class="hljs-keyword">or</span> row view, defaults to <span class="hljs-keyword">false</span>
<span class="hljs-string">"attributes"</span>: {       //optional, the input/<span class="hljs-built_in">select</span> field attributes, defaults to <span class="hljs-keyword">false</span>
<span class="hljs-string">"disabled"</span>: <span class="hljs-string">"true"</span>
},
<span class="hljs-string">"plugin"</span>: <span class="hljs-string">"slug"</span>,     //optional, set the plugin to slug, text <span class="hljs-keyword">or</span> image defaults to <span class="hljs-keyword">false</span>
<span class="hljs-string">"values"</span>: {           //optional, make dropdown <span class="hljs-keyword">for</span> the values, defaults to <span class="hljs-keyword">false</span>
<span class="hljs-string">"0"</span>: <span class="hljs-string">"No"</span>,
<span class="hljs-string">"1"</span>: <span class="hljs-string">"Yes"</span>
},
<span class="hljs-string">"join"</span>: {             //optional, join with a <span class="hljs-built_in">table</span>
<span class="hljs-string">"icons"</span>: {          //required, the join <span class="hljs-built_in">table</span> name
<span class="hljs-string">"key"</span>: <span class="hljs-string">"id"</span>,      //required, the join <span class="hljs-built_in">table</span> primary key
<span class="hljs-string">"columns"</span>: {      //required, the join columns array
<span class="hljs-string">"id"</span>: {},       //required, the join column name
<span class="hljs-string">"name"</span>: {}
}
}
}
}
}
}</code></pre>

<h2 id="contributing">Contributing</h2>

<p>It is more than welcome to contribute to the project - feel free to send pull requests. Also, I try to keep the issue section updated but don’t feel limited only by that. I would happily accept all critics/opinions/praises to <code>sitilge@gmail.com</code>.</p>

<p><a href="https://bitdeli.com/free" title="Bitdeli Badge"><img src="https://d2weczhvl823v0.cloudfront.net/sitilge/curdle/trend.png" alt="Bitdeli Badge" title=""></a></p>