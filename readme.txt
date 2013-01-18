===011 Ps Custom Taxonomy ===
Contributors: ouhinit
Tags: category, tag, taxonomy, custom taxonomy
Requires at least: 1.0
Tested up to: 3.3.2

add items for taxonomies.
== Description ==
add items(except for the name,description) for categoryor tag (taxonomies).
text,textarea,checkbox,radio,select of custom items.


= Functions =
1.add items(except for the name,description) for categoryor tag (taxonomies).

= Usage =
* You can if use get add custom items, get item from Data record(Result object get_the_category,get_terms,get_the_terms,get_term adn etc. )
* example  $cat = get_term(1,'category'); $custom_item = $cat->add item key;
* You can classify the item to be added archaic custom (tag, category) to the list of articles to display.

== Installation ==

1. Upload the 011 Ps Custom Taxonomy folder to the plugins directory in your WordPress installation
2. You can Sample file from (_config.php), to add the items you added.rename to config.php the _config.php
3. Go to plugins list and activate "011PS Custom Taxonomy". 

== Changelog ==
= Version 1.2 (13-06-2012) =
* FIXED: Quick Edit bug fixed


== Screenshots ==
1. add category.