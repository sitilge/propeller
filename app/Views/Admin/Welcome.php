<h1><a id="curdle_0"></a>curdle</h1>
<blockquote>
    <p>curdle - separate or cause to separate into curds or lumps</p>
</blockquote>
<p>curdle is a simple database administration tool for simple <a href="https://en.wikipedia.org/wiki/Create,_read,_update_and_delete">CRUD</a> operations using almost any relational database you prefer.</p>
<h2><a id="Demo_6"></a>Demo</h2>
<p>See a live demo here: <a href="http://curdle.sitilge.id.lv">http://curdle.sitilge.id.lv</a></p>
<h2><a id="Features_10"></a>Features</h2>
<ul>
    <li>Adapt an existing or new database with ease</li>
    <li><a href="https://en.wikipedia.org/wiki/Create,_read,_update_and_delete">CRUD</a> the tables, rows, and columns in an elegant interface</li>
    <li>Search &amp; order the rows instantly</li>
    <li>Customize columns to fit your needs - simple value, dropdown, editor, image, and more</li>
    <li>Join tables to get the best of multiple sources</li>
    <li>Use the image gallery to manage your media files</li>
</ul>
<h2><a id="Tech_19"></a>Tech</h2>
<ul>
    <li>small PHP footprint, &lt; 700 LOC</li>
    <li>implies PDO abstraction layer - MySQL, sqlite, etc.</li>
    <li>built with <a href="https://github.com/sitilge/abimo">abimo</a> framework, composer packages</li>
    <li>front-end stack - fully responsive, Bootstrap, SASS</li>
</ul>
<h2><a id="Installation_26"></a>Installation</h2>
<p>Clone the project</p>
<pre><code>git clone <span class="hljs-string">https:</span><span class="hljs-comment">//github.com/sitilge/curdle</span></code></pre>
<p>Install <a href="https://getcomposer.org/download/">composer</a> on your system and run</p>
<pre><code>composer <span class="hljs-keyword">install</span></code></pre>
<h2><a id="Configuration_37"></a>Configuration</h2>
<p>The respective <code>.json</code> files for the project are located under <code>app/Misc/Admin</code>. File names must match the table names; let us examine one of them the <code>menu.json</code> that corresponds to <code>menu</code> table in the database</p>
<pre><code>{
  <span class="hljs-string">"key"</span>: <span class="hljs-string">"id"</span>,                  <span class="hljs-comment">//required, the table primary key</span>
  <span class="hljs-string">"name"</span>: <span class="hljs-string">"Menu"</span>,               <span class="hljs-comment">//optional, the table display name, defaults to table name</span>
  <span class="hljs-string">"create"</span>: <span class="hljs-literal">true</span>,               <span class="hljs-comment">//optional, allow to create new row, defaults to false</span>
  <span class="hljs-string">"delete"</span>: <span class="hljs-literal">true</span>,               <span class="hljs-comment">//optional, allow to delete row, defaults to false</span>
  <span class="hljs-string">"order"</span>: {                    <span class="hljs-comment">//optional, change the order of rows in table view, defaults to false</span>
    <span class="hljs-string">"column"</span>: <span class="hljs-string">"sequence"</span>,       <span class="hljs-comment">//required, the name of the column</span>
    <span class="hljs-string">"direction"</span>: <span class="hljs-string">"ASC"</span>          <span class="hljs-comment">//optional, the direction of order, defaults to ASC</span>
  },
  <span class="hljs-string">"columns"</span>: {                  <span class="hljs-comment">//required, the columns array</span>
    <span class="hljs-string">"name"</span>: {                   <span class="hljs-comment">//required, the column name</span>
    <span class="hljs-string">"disabled"</span>: <span class="hljs-literal">true</span>,         <span class="hljs-comment">//optional, disable the field, defaults to false</span>
    <span class="hljs-string">"name"</span>: <span class="hljs-string">"Name"</span>,           <span class="hljs-comment">//optional, the column display name, defaults to column name</span>
    <span class="hljs-string">"view"</span>: <span class="hljs-string">"row"</span>,            <span class="hljs-comment">//optional, display the column in table or row view, defaults to false</span>
    <span class="hljs-string">"type"</span>: <span class="hljs-string">"slug"</span>,           <span class="hljs-comment">//optional, set the type to slug, text, image, length, or price, defaults to false</span>
    <span class="hljs-string">"values"</span>: {               <span class="hljs-comment">//optional, make dropdown for the values, defaults to false</span>
      <span class="hljs-string">"0"</span>: <span class="hljs-string">"No"</span>,
      <span class="hljs-string">"1"</span>: <span class="hljs-string">"Yes"</span>
  },
  <span class="hljs-string">"join"</span>: {                 <span class="hljs-comment">//optional, join with a table</span>
    <span class="hljs-string">"icons"</span>: {              <span class="hljs-comment">//required, the join table name</span>
    <span class="hljs-string">"key"</span>: <span class="hljs-string">"id"</span>,          <span class="hljs-comment">//required, the join table primary key</span>
    <span class="hljs-string">"columns"</span>: {          <span class="hljs-comment">//required, the join columns array</span>
      <span class="hljs-string">"id"</span>: {},           <span class="hljs-comment">//required, the join column name</span>
      <span class="hljs-string">"name"</span>: {}
          }
        }
      }
    }
  }
}</code></pre>
<h2><a id="Contributing_74"></a>Contributing</h2>
<p>It is more than welcome to contribute to the project - feel free to send pull requests. Also, I try to keep the issue section updated but don’t feel limited only by that. I would happily accept all critics/opinions/praises to <code>sitilge@gmail.com</code>.</p>
