<h1>EEZ Template Documentation</h1>

<h2>Content</h2>
<ul>
	<li><a href="#controllers">Defining your controllers</a></li>
	<li><a href="#routes">Setting up your routes</a></li>
	<li><a href="#view">Setting the view</a></li>
	<li><a href="#filters">Adding filters to your routes</a></li>
	<li><a href="#database">Running Database queries</a></li>
	<li><a href="#models">Models</a></li>
	<li><a href="#validation">Validating input</a></li>
	<li><a href="#breadcrumbs">Breadcrumbs</a></li>
	<li><a href="#thumbnails">Generating images and thumbnails</a>	</li>
</ul>



<h2 id="controllers">Defining your controllers</h2>
<p>
	Most likely you will use some controllers for your application. The controllers have there place in the <strong>/controllers</strong> folder.
	To define your custom controllers there are a few things to keep in mind.
</p>

<ul>
	<li>Use camelcase for the class names</li>
	<li>Class names should be the same as filename</li>
	<li>The class has to extend the <strong>Controller</strong> class</li>
</ul>

<p>
	A typical controller might look like this:
</p>

<pre><code>class NewsController extends Controller 
{
	public function __construct()
	{
		// Run the parent constructor
		parent::__construct();
	}

	public function show()
	{
		// Set page title
		$this->setTitle('Page tile');

		// Render the view
		return View::make('view');
	}
}</code></pre>

<p>
	To run your controllers you will need to define routes which you can read about <a href="#routes">below</a>.
</p>

<h2 id="routes">Setting up your routes</h2>
<p>
	In order for your application to know which methods and which controller to use you need to assign routes. In your <em>index.php</em> file in the <em>public</em> folder you will find a couple of pre-defined routes such as the index page.
</p>
<h3>Adding a custom route</h3>
<p>
	Let's say you want to add a news page to your application and you would like to set up a route to your news controller.
</p>
<pre><code>Route::get('news', array('action' => 'show@NewsController'));</code></pre>
<p>
	Here the application would run the <strong>show</strong> method in the news controller when <strong>/news</strong> route is being accessed.
</p>
<p>
	If you want to protect your routes with filters you can add the <strong>filter</strong> key to the passed array.
	Filters is a good way to ensure that the user is authenticated for example without doing anything in the actual controller.
	Read more about filters <a href="#filters">here</a>.
</p>

<h2 id="view">Setting the view</h2>
<p>
	To render a view in EEZ you need your controller to return a View object. <br><br>

	To generate a view you simply type:
</p>

<pre><code>return View::make($template)</code></pre>

<p>
	This will make the application look for a template file in the "view" folder. If the file exists it will render the page.
</p>

<h3>Passing variables to the view</h3>
<p>
	Most of the time you would want to pass some data to your view. To do this you can use the "with" method.
</p>
<pre><code>return View::make($template)->with(['field' => 'value'])</code></pre>

<p>
	In the example above we pass an <em>array</em> to the view and it can now be accessed in the view by it's elements name, "field".
</p>

<h2 id="filters">Adding filters to your routes</h2>
<p>
	There will be some routes that you would like to protect and filters is a simply and good way to do that. 
</p>
<p>
	For example you might have a user page that should only be accessed by authenticated users. Then a filter would come in handy.
</p>

<h3>Defining a filter</h3>
<p>
	To define your filter to make sure that the user is authenticated you can do this in your <strong>index.php</strong> file in the <strong>public</strong> folder.
</p>
<pre><code>Filter::bind('authed', function() {
	return Auth::is_authed();
});</code></pre>

<p>This will create a filter than can be used in association with your routes like this</p>

<pre><code>Route::get('profile', array('action' => 'index@UserController', 'filter' => 'authed'));</code></pre>

<p>As you can see we have added the filter <strong>authed</strong> as we previously created and this route is now only accessable for users that are authenticated.</p>
<p>When defining your filters it is important to keep in mind that the function should return either <em>true</em> or <em>false</em></p>

<h2 id="database">Running Database queries</h2>

<p>In EEZ there are two ways of interacting with the database. One way is to use the <strong>DB</strong> class and 
the other is to use <em>Models</em> which you can read about <a href="#models">below</a></p>

<h3>Selecting rows</h3>
<p>To retrieve all rows you would do something like:</p>
<pre><code>DB::table('table')->get()</code></pre>

<p>The code above will retrieve all rows in the table <em>table</em> as an array of objects</p>

<p>To retrieve a single row you would use the <strong>first()</strong> selector</p>

<pre><code>DB::table('table')->first()</code></pre>

<p>Most likely when selecting one row you would like to specify a <em>where statement</em>. For example you might want to grab the news post that has the id "1". This can be easily be done</p>

<pre><code>DB::table('news_post')->where(array('id' => 1))->first()</code></pre>

<h3>Insert and store data</h3>

<p>To add data to the specified table you would use the <em>insert()</em> method. 
Let's use the news table for example again:</p>

<pre><code>DB::table('news_post')->insert(array('title' => 'The title'))</code></pre>

<h3>Updating a row</h3>

<p>If you want to update a row in the database you would use the <em>update()</em> method.</p>

<pre><code>DB::table('news_post')->where(['id' => 1])->update(['title' => 'New title'])</code></pre>

<p>The code above would update the <em>news_post</em> table where the id is 1 and set the title to <em>New title</em></p>

<h3>Deleting a row</h3>

<p>Deleting a row in EEZ is very similar to updating a row. This time you use the <em>delete()</em> method</p>
<p>To delete the row we updated in the previous example you would do this:</p>

<pre><code>DB::table('news_post')->where(['id' => 1])->delete()</code></pre>

<h2 id="models">Models</h2>

<p>Using models is an easy way to interacting with the tables. Your models can be found in the <strong>/models</strong> folder</p>

<h3>Creating your model</h3>
<p>To create your model you need to define to following code</p>

<pre><code>class ModelName extends Model {

	protected static $table = 'tablename';

	public function __construct( $data = array() )
	{
		parent::__construct($data);
	}
}</code></pre>

<p>It is important that you use camelcase for the model's name and that you ensure that it extends the <em>Model</em> class. The table property need to be set to the table name that the model represent and the constructor need to execute it's parents constructor</p>

<h3>Selecting rows using the model</h3>

<p>To select rows from the database using your model you can use either</p>

<pre><code>News::all()</code></pre>

<p>to select all rows or</p>

<pre><code>News::where('id', 1)->first()</code></pre>

<p>to select a specific row.</p>

<p>
	If you wan to select multiple rows you can use <strong>get()</strong> instead of <strong>first()</strong>. 
	Using <strong>get</strong> you will retrieve an array of the selected objects and using <strong>first</strong> you will retreive the row as an object itself.
</p>

<p>However if you want to select a row by it's id you can also use the <em>find()</em> method</p>

<pre><code>News::find(1)</code></pre>

<h3>Inserting data using the model</h3>

<p>To insert a row in the table you would do something like</p>

<pre><code>$news = new News;

$news->insert(array('title' => 'The title'));</code></pre>

<h3>Updating using the model</h3>

<p>To update a table you would do</p>

<pre><code>News::find(1)->update(['title' => 'New title']);</code></pre>

<p>The code above would update the news table where the id is 1 and set the title to <em>New title</em></p>

<h3>Deleting data using the model</h3>

<p>The example below shows how to delete a row in the model's table. We will use the same example table as before</p>

<pre><code>News::find(1)->delete()</code></pre>

<h3>Paginate results using the model</h3>

<p>Paginating your data is very easy in EEZ as it's built in into the model class</p>

<pre><code>$news = News::paginate(10)</code></pre>

<p>The code above would grab all the news posts and store them in <em>$news['data']</em>.
In <em>$news['links']</em> you will have your links to the different pages ready to be printed out in your HTML view</p>

<p>Of course you can still use the same selectors as before such as where() and so forth to limit your results to fit your needs.</p>

<h3>Relationship</h3>

<p>Sometimes the tables will depend on other tables and that is why we have relationships. In EEZ you can easily use this by defining a new method in the model class</p>

<h4>The has-one relationship</h4>

<p>If you have a news model that has one category you can use <strong>hasOne</strong></p>

<pre><code>public function category()
{
	$this->category = $this->hasOne('category', $this);

	return $this;
}</code></pre>


<h4>Using a pivot table</h4>
<p>
	The example below illustrates a many-to-many relationship. In the example we have our <strong>News</strong> model and the news posts should have multiple categories.
	Then we need to use a one table to store the categories which in this case is <em>category</em> and one pivot table, <em>news_category</em> to store the news id and category id.
</p>
<pre><code>	public function category()
{
	$this->category = $this->belongsToMany('category', 'news_category', $this);

	return $this;
}</code></pre>

<h2 id="validation">Validating input</h2>
<p>For security reasons you will need to validate user input. This can be done easily using the <em>Validatior</em> class</p>

<p>The validation class needs two arrays to work. One with the data to validate and one with the validation rules</p>

<pre><code>// Initialize the validator class
$validator = new Validator();

// Pass the arguments to the validator class
$validator->make(
	array('name' => 'James'),
	array('name' => 'required|min:3')
);</code></pre>

<p>The example above will make sure that the <em>name</em> is not empty and that it has at least three characters. This is done using "flags". You can use as many flags as
you like and you would use the "|" character to seperate them</p>

<p>
	To check if the validation failed you use:
</p>

<pre><code>if ($validator->fails())</code></pre>

<p>Which will return either true or false.</p>

<p>To add custom messages instead of the pre-defined you can add a third optional argument to the <em>Validator</em> class.</p>

<pre><code>$validator->make(
	array('name' => 'James'),
	array('name' => 'required|min:3'),
	array('name.required' => 'The name field is required')
);</code></pre>

<h2 id="breadcrumbs">Breadcrumbs</h2>

<p>To add breadcrumbs to your view, all you need to add is one row in your controller</p>

<pre><code>Breadcrumb::add('Name', 'Link', 'Label')</code></pre>

<p>You need to print your breadcrumbs somewhere, most likely in your master layout:</p>

<pre><code>if (Breadcrumb::hasBreadcrumbs())
	Breadcrumb::display();</code></pre>

<h2 id="thumbnails">Generating images and thumbnails</h2>

<p>There is an Image class in EEZ that allows you to quickly generate thumbnails for your pictures on the fly.</p>

<pre><code>img.php?src=image.jpg&amp;width=120</code></pre>

<p>The code above is what you need to resize the width of the image to 120. However there are many more options</p>

<h3>Cropping the image</h3>

<p>To crop the image you need to set both width and height and use the <em>crop-to-fit</em> parameter instead of <em>resize</em></p>

<pre><code>img.php?src=image.jpg&amp;width=120&amp;height=120&amp;crop-to-fit</code></pre>

<h3>Show verbose mode</h3>

<p>To use the verbose mode simply add the <em>verbose</em> parameter.

<pre><code>img.php?src=image.jpg&amp;width=120&amp;height=120&amp;crop-to-fit&amp;verbose</code></pre>

<h3>Save the image as a different file format</h3>

<p>If you want to save the image as another file format you are to use the <em>save-as</em> option.</p>

<pre><code>img.php?src=image.jpg&amp;width=120&amp;save-as=png</code></pre>
