# Project Title

Smart Content Insert

## Getting Started

This is a WordPress plugin that was built for a single project, and doesn't really offer the flexibility to be used on any site. However, It may provide what you need or can be used as a base to build upon. It does not have a UI and is intended for developer use within other plugins or theme customizations.

### Usage

This plugin lets you insert HTML after a given number of paragraphs, or before/after a specific element. It is meant to be called on `the_content` filter after **wpuatop** has run on the content. I suggest using a priority of 30 to your `the_content` filter.

**Carefully consider the performance impact of this code carefully before using if you are not using a page caching plugin.**

### Methods

Content can be inserted after a number of paragraphs using following method:

```
insert_into_paragraphs( $content, $insert_value, $insert_after )
```

`$content`: The content you wil be inserting into.

`$insert_value` HTML to add to the content.

`$insert_after`: The number of the paragraph to add the text to. (Default: 1)

Content can be inserted before or after a specific element using following method:

```
insert_at_element( $content, $insert_value, $selector, $selector_type, $selector_tag, $instance, $insert_before )
```

`$content`: The content you wil be inserting into.

`$insert_value` HTML to add to the content.

`$selector`: Element ID or class name to select by.

`$selector_type`: 'id' or 'class' (Default: `id`)

`$selector_tag`: HTML tag being selected (Deafult: `div`)

`$instace`: When using class selector, The index of the element to insert at (Default: `1`)

`$instace`: Insert the value before the selected element (Default: `false`)

