At the top of the default MongoPress theme a series of tab like links allow
important pages to be accessed. This how to shows you how to add a new link
to this list, editing will be obvious after following the steps here.

-----

Adding a new link:

On the admin panel - using the "Object Control" - create an object with an
object type of "links" and a slug of "destination-link"
- edit "destination" as appropriate.

Title is the text that will appear in the tab.

Object Content should be either "//" for the home page, "destination"
- which matches a slug for some kind of other object,
or "http://www.example.com/" for an external link.

EG:

To create a "Our Mission"

1) Create an object - with a title of "Our Mission", an object type of
"articles" or "pages" (if you don't want it listed on the homepage),
an object slug of "our-mission" and object content
"Our mission is to save the Earth"

Save the object.

2) Now create a new object - title of "Our Mission", an object type of
"Our Mission", edit the slug to "our-mission-link", and simply give the
object content the value "our-mission"
- notice this corresponds to a slug above.

Save this object.

Now refresh the front page of your MongoPress themed site - you have a new Tab.